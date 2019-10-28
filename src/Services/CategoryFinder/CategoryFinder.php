<?php

namespace App\Services\CategoryFinder;

use App\Entity\Category;
use App\Entity\Value;
use App\Entity\Widget;
use App\Enum\SearchCriteriaEnum;
use App\Repository\FicheRepository;
use App\Repository\ValueRepository;
use App\Repository\WidgetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

final class CategoryFinder implements CategoryFinderInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var QueryBuilder
     */
    private $qb;
    /**
     * @var FicheRepository
     */
    private $ficheRepository;
    /**
     * @var ValueRepository
     */
    private $valueRepository;
    /**
     * @var WidgetRepository
     */
    private $widgetRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                ValueRepository $valueRepository,
                                WidgetRepository $widgetRepository)
    {
        $this->entityManager = $entityManager;
        $this->valueRepository = $valueRepository;
        $this->widgetRepository = $widgetRepository;
    }

    public function search(Category $category, array $criterias): array
    {
        $this->qb = $this->valueRepository->createQueryBuilder('v');

        # Filter on category
        $this->setWhereCategory($category);

        # Apply criterias
        $this->applyCriterias($category, $criterias);

        # Output results
        return $this->qb->getQuery()->getArrayResult();
    }


    private function applyCriterias(Category $category, array $criteria)
    {
        /** @var Widget[] $widgets */
        $widgets = $this->widgetRepository->findByForm($category->getForm());
        # Get Widget by category->frm (bypass area)

        foreach ($widgets as $widget) {

            if (!empty($criteria[$widget->getId()])) { # Isset and not empty
                $type = ucfirst($widget->getType());
                $valueColumn = "valueOfType{$type}";
                $parameterKey = "value{$widget->getId()}";
                $searchCriteria = $criteria[$widget->getId()]['criteria'];
                $searchValue = $criteria[$widget->getId()]['value'];

                switch ($searchCriteria) {
                    case SearchCriteriaEnum::IS_NULL:
                        $this->qb->andWhere("v.{$valueColumn} IS NULL");
                        break;
                    case SearchCriteriaEnum::IS_NOT_NULL:
                        $this->qb->andWhere("v.{$valueColumn} IS NOT NULL");
                        break;
                    case SearchCriteriaEnum::EXACT:
                        $this->qb
                            ->andWhere("v.{$valueColumn} = :{$parameterKey}")
                            ->setParameter($parameterKey, $searchValue);
                        break;
                    case SearchCriteriaEnum::CONTAINS:
                        $this->qb
                            ->andWhere("v.{$valueColumn} LIKE :{$parameterKey}")
                            ->setParameter($parameterKey, "%{$searchValue}%");
                        break;
                    case SearchCriteriaEnum::STARTS_WITH:
                        $this->qb
                            ->andWhere("v.{$valueColumn} LIKE :{$parameterKey}")
                            ->setParameter($parameterKey, "{$searchValue}%");
                        break;
                    case SearchCriteriaEnum::ENDS_WITH:
                        $this->qb
                            ->andWhere("v.{$valueColumn} LIKE :{$parameterKey}")
                            ->setParameter($parameterKey, "%{$searchValue}");
                        break;
                }
            }
        }
    }

    private function setWhereCategory(Category $category)
    {
        $this->qb
            ->leftJoin('v.fiche', 'f')
            ->andWhere('f.category = :category')
            ->setParameter('category', $category)
        ;
    }
}