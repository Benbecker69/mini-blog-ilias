<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public const ITEMS_PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Get paginated published articles with sorting
     *
     * @param int $page Current page number
     * @param string $sortField Field to sort by (createdAt or title)
     * @param string $sortDirection Sort direction (asc or desc)
     * @return Paginator
     */
    public function findPublishedPaginated(int $page = 1, string $sortField = 'createdAt', string $sortDirection = 'desc'): Paginator
    {
        // Validate sort field
        $allowedSortFields = ['createdAt', 'title', 'publishedAt'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'createdAt';
        }

        // Validate sort direction
        $sortDirection = strtolower($sortDirection);
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $query = $this->createQueryBuilder('a')
            ->where('a.isPublished = :published')
            ->setParameter('published', true)
            ->orderBy('a.' . $sortField, $sortDirection)
            ->setFirstResult(($page - 1) * self::ITEMS_PER_PAGE)
            ->setMaxResults(self::ITEMS_PER_PAGE)
            ->getQuery();

        return new Paginator($query);
    }

    /**
     * Find one article by slug (only if published)
     */
    public function findOneBySlugPublished(string $slug): ?Article
    {
        return $this->createQueryBuilder('a')
            ->where('a.slug = :slug')
            ->andWhere('a.isPublished = :published')
            ->setParameter('slug', $slug)
            ->setParameter('published', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find one article by slug (for admin/author - no published check)
     */
    public function findOneBySlug(string $slug): ?Article
    {
        return $this->findOneBy(['slug' => $slug]);
    }
}
