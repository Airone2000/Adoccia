<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Fiche;
use App\Enum\SearchCriteriaEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

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
     * @param QueryBuilder $queryBuilder
     * @param array $values
     * @param int $havingCount
     * @return QueryBuilder
     */
    public function getFicheByValues(QueryBuilder $queryBuilder, array $values, int $havingCount): QueryBuilder
    {
        return $queryBuilder
            ->leftJoin('f.values', 'v')
            ->andWhere('v.id IN (:values)')
            ->setParameter('values', $values)
            ->having('COUNT(v) = :nOfMatchingValues')
            ->setParameter('nOfMatchingValues', $havingCount)
            ->groupBy('f')
        ;
    }

    public function filterByTitle(QueryBuilder $queryBuilder, string $criteria, ?string $value)
    {
        if (is_null($value)) return;
        if (!SearchCriteriaEnum::isset($criteria)) return;

        switch ($criteria) {
            case SearchCriteriaEnum::DISABLED:
                // Nothing
                return;
            case SearchCriteriaEnum::CONTAINS:
                $value = explode(',', $value);
                $value = $this->removeNullOrBlankValuesFromArray($value);
                $queryBuilder
                    ->andWhere('REGEXP(f.title, :title) = 1')
                    ->setParameter('title', implode('|', $value))
                ;
                return;
            case SearchCriteriaEnum::EXACT:
                $value = explode(',', $value);
                $value = $this->removeNullOrBlankValuesFromArray($value);
                $queryBuilder
                    ->andWhere('f.title IN (:titles)')
                    ->setParameter('titles', $value)
                ;
                return;
            case SearchCriteriaEnum::STARTS_WITH:
                $value = explode(',', $value);
                $value = $this->removeNullOrBlankValuesFromArray($value);
                $queryBuilder
                    ->andWhere('REGEXP(f.title, :title) = 1')
                    ->setParameter('title', '^('.implode('|', $value).')')
                ;
                return;
            case SearchCriteriaEnum::ENDS_WITH:
                $value = explode(',', $value);
                $value = $this->removeNullOrBlankValuesFromArray($value);
                $queryBuilder
                    ->andWhere('REGEXP(f.title, :title) = 1')
                    ->setParameter('title', '('.implode('|', $value).')$')
                ;
                return;
        }
    }

    public function findFicheByCategoryAndId($categoryId, $ficheId)
    {
        $qb = $this->createQueryBuilder('f');
        return $qb
            ->where('f.category = :category')
            ->setParameter('category', $categoryId)
            ->andWhere('f.id = :id')
            ->setParameter('id', $ficheId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    private function removeNullOrBlankValuesFromArray(array $tab)
    {
        $tab = array_map('trim', $tab);
        $tab = array_filter($tab, function($val){
            return !($val === '' || $val === null);
        });
        return $tab;
    }
}
