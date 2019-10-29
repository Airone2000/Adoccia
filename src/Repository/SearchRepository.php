<?php

namespace App\Repository;

use App\Entity\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Search|null find($id, $lockMode = null, $lockVersion = null)
 * @method Search|null findOneBy(array $criteria, array $orderBy = null)
 * @method Search[]    findAll()
 * @method Search[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Search::class);
    }

    public function findOneByIdAndCategory(int $id, int $categoryId): ?Search
    {
        $qb = $this->createQueryBuilder('s');
        return $qb
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->andWhere('s.category = :category')
            ->setParameter('category', $categoryId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
