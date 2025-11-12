<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Security\Voter\ArticleVoter;
use App\Service\SlugGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository, Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $sortField = $request->query->get('sort', 'createdAt');
        $sortDirection = $request->query->get('dir', 'desc');

        $paginator = $articleRepository->findPublishedPaginated($page, $sortField, $sortDirection);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / ArticleRepository::ITEMS_PER_PAGE);

        return $this->render('article/index.html.twig', [
            'articles' => $paginator,
            'current_page' => $page,
            'pages_count' => $pagesCount,
            'sort_field' => $sortField,
            'sort_direction' => $sortDirection,
        ]);
    }

    #[Route('/articles', name: 'app_article_list')]
    public function list(ArticleRepository $articleRepository, Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $sortField = $request->query->get('sort', 'createdAt');
        $sortDirection = $request->query->get('dir', 'desc');

        $paginator = $articleRepository->findPublishedPaginated($page, $sortField, $sortDirection);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / ArticleRepository::ITEMS_PER_PAGE);

        return $this->render('article/list.html.twig', [
            'articles' => $paginator,
            'current_page' => $page,
            'pages_count' => $pagesCount,
            'sort_field' => $sortField,
            'sort_direction' => $sortDirection,
        ]);
    }

    #[Route('/articles/{slug}', name: 'app_article_show')]
    public function show(string $slug, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->findOneBySlugPublished($slug);

        if (!$article) {
            throw $this->createNotFoundException('L\'article demandé n\'existe pas.');
        }

        // Create comment form
        $comment = new Comment();
        $commentForm = null;

        if ($this->getUser()) {
            $commentForm = $this->createForm(CommentType::class, $comment, [
                'action' => $this->generateUrl('app_comment_create', ['slug' => $slug]),
            ]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'comment_form' => $commentForm?->createView(),
        ]);
    }

    #[Route('/articles/new', name: 'app_article_new', priority: 2)]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager, SlugGenerator $slugGenerator): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuthor($this->getUser());

            // Generate slug from title
            $slug = $slugGenerator->generateFromTitle($article->getTitle());
            $article->setSlug($slug);

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article créé avec succès !');

            return $this->redirectToRoute('app_article_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/new.html.twig', [
            'article_form' => $form,
        ]);
    }

    #[Route('/articles/{id}/edit', name: 'app_article_edit', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Article $article, Request $request, EntityManagerInterface $entityManager, SlugGenerator $slugGenerator): Response
    {
        $this->denyAccessUnlessGranted(ArticleVoter::EDIT, $article);

        $oldTitle = $article->getTitle();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Regenerate slug if title changed
            if ($oldTitle !== $article->getTitle()) {
                $slug = $slugGenerator->generateFromTitle($article->getTitle(), $article->getId());
                $article->setSlug($slug);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Article modifié avec succès !');

            return $this->redirectToRoute('app_article_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'article_form' => $form,
        ]);
    }

    #[Route('/articles/{id}/delete', name: 'app_article_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(ArticleVoter::DELETE, $article);

        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article supprimé avec succès !');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_home');
    }
}
