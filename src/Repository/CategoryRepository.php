<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\CategorySearch;
use App\Entity\User;
use App\Security\Voter\CategoryVoter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * @var ManagerRegistry
     */
    private $registry;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(ManagerRegistry $registry, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($registry, Category::class);
        $this->registry = $registry;
        $this->tokenStorage = $tokenStorage;
    }

    private function getForUser(?User $user, QueryBuilder $queryBuilder)
    {
        # No filtering when user can access all categories
        if (CategoryVoter::canAccessAllCategories($user)) return;

        $q = '(c.public = 1 AND c.online = 1)';
        if ($user instanceof User) {
            $q = "({$q} OR (c.createdBy = :user))";
            $queryBuilder->setParameter('user', $user);
        }
        $queryBuilder->andWhere($q);
    }

    public function findAllForUserOrPublic(?User $user, int $page = 1, int $items = 30, CategorySearch $categorySearch): Paginator
    {
        $qb = $this->createQueryBuilder('c');
        $this->getForUser($user, $qb);
        $qb
            ->setFirstResult( ($page - 1) * $items)
            ->setMaxResults($items)
        ;

        $this->applyCategorySearch($qb, $categorySearch, [
            'user' => $user
        ]);

        $paginator = new Paginator($qb, false);
        return $paginator;
    }

    private function applyCategorySearch(QueryBuilder $qb, ?CategorySearch $categorySearch, array $options = []): void
    {
        if($categorySearch === null) return;

        # Filter on title / name
        if ($title = $categorySearch->getTitle()) {
            $qb
                ->andWhere('c.name LIKE :title')
                ->setParameter('title', "%{$title}%")
            ;
        }

        # Order by
        if ($orderBy = $categorySearch->getOrderBy()) {
            switch($orderBy) {
                case 'created_at_desc': $qb->orderBy('c.createdAt', 'DESC'); break;
                case 'created_at_asc': $qb->orderBy('c.createdAt', 'ASC'); break;
                case 'name_desc': $qb->orderBy('c.name', 'DESC'); break;
                case 'name_asc': $qb->orderBy('c.name', 'ASC');break;
            }
        }

        # Filter
        if ($filter = $categorySearch->getFilter()) {
            switch ($filter) {
                case 'mine':
                    $user = ($options['user'] ?? null);
                    if ($user instanceof User) {
                        $qb->andWhere('c.createdBy = :user')->setParameter('user', $user);
                    }
                    break;
            }
        }
    }

    private function getUser(): ?User
    {
        if ($this->tokenStorage->getToken() instanceof AnonymousToken) {
            return null;
        }
        /* @var User|null $user */
        $user = $this->tokenStorage->getToken()->getUser();
        return $user;
    }

    public function getOneForUserById(?User $user, $id): ?Category
    {
        $user = $user ?? $this->getUser();
        $qb = $this->createQueryBuilder('c');
        $qb
            ->where('c.id = :id')
            ->setParameter('id', $id)
        ;
        $this->getForUser($user, $qb);
        return $qb->getQuery()->getOneOrNullResult();
    }
}
