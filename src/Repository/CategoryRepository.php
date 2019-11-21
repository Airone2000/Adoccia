<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\User;
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
        $q = '(c.public = 1 AND c.online = 1)';
        if ($user instanceof User) {
            $q = "({$q} OR (c.createdBy = :user))";
            $queryBuilder->setParameter('user', $user);
        }
        $queryBuilder->andWhere($q);
    }

    public function findAllForUserOrPublic(?User $user, int $page = 1, int $items = 30): Paginator
    {
        $qb = $this->createQueryBuilder('c');
        $this->getForUser($user, $qb);
        $qb
            ->setFirstResult( ($page - 1) * $items)
            ->setMaxResults($items)
        ;
        $paginator = new Paginator($qb, false);
        return $paginator;
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
