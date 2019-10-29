<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Fiche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Fiche|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fiche|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fiche[]    findAll()
 * @method Fiche[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FicheRepository extends ServiceEntityRepository
{
    /**
     * @var WidgetRepository
     */
    private $widgetRepository;

    public function __construct(ManagerRegistry $registry, WidgetRepository $widgetRepository)
    {
        parent::__construct($registry, Fiche::class);
        $this->widgetRepository = $widgetRepository;
    }

    public function getCategoryFiches(Category $category, array $moreCriterias = [])
    {
        $qb = $this->createQueryBuilder('f');

        return $qb->getQuery()->getResult();
    }

    /**
     * Used by the CategoryFinder to retrieve fiches having values that match user criterias
     * @param array $values
     * @return array
     */
    public function getFicheByValues(array $values, int $havingCount): array
    {
        $qb = $this->createQueryBuilder('f');
        return $qb
            ->leftJoin('f.values', 'v')
            ->andWhere('v.id IN (:values)')
            ->setParameter('values', $values)
            ->having('COUNT(v) = :nOfMatchingValues')
            ->setParameter('nOfMatchingValues', $havingCount)
            ->groupBy('f.id')
            ->getQuery()
            ->getResult()
        ;

    }
}
