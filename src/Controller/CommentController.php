<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Security\Voter\CommentVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/comments')]
class CommentController extends AbstractController
{
    #[Route('/{slug}/create', name: 'app_comment_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(
        string $slug,
        Request $request,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $article = $articleRepository->findOneBySlugPublished($slug);

        if (!$article) {
            throw $this->createNotFoundException('L\'article demandé n\'existe pas.');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $comment->setAuthor($this->getUser());

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été ajouté avec succès !');
        } else {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout de votre commentaire.');
        }

        return $this->redirectToRoute('app_article_show', ['slug' => $slug]);
    }

    #[Route('/{id}/delete', name: 'app_comment_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Comment $comment, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(CommentVoter::DELETE, $comment);

        $articleSlug = $comment->getArticle()->getSlug();

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire supprimé avec succès !');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_article_show', ['slug' => $articleSlug]);
    }
}
