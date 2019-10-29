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

    /**
     * @var int
     * The number of criteria filled by the user to filter
     */
    private $searchCriteriaCount;

    public function __construct(EntityManagerInterface $entityManager,
                                ValueRepository $valueRepository,
                                WidgetRepository $widgetRepository,
                                FicheRepository $ficheRepository)
    {
        $this->entityManager = $entityManager;
        $this->valueRepository = $valueRepository;
        $this->widgetRepository = $widgetRepository;
        $this->ficheRepository = $ficheRepository;
    }

    public function search(Category $category, array $criterias): array
    {
        $this->searchCriteriaCount = 0;

        $this->qb = $this->valueRepository->createQueryBuilder('v');

        # Filter on category
        $this->setWhereCategory($category);

        # Apply search on title
        $this->applySearchOnTitle($category, $criterias);

        # Apply criterias
        $this->applyCriterias($category, $criterias);

        # Matching widgets
        $matchingValues = $this->qb->getQuery()->getArrayResult();

        # Search fiches by matching widgets
        $fiches = $this->ficheRepository->getFicheByValues($matchingValues, $this->searchCriteriaCount);

        return $fiches;
    }

    private function applySearchOnTitle(Category $category, array $criterias)
    {
        if (array_key_exists('title', $criterias)) {
            
        }
        dd($criterias);
    }


    private function applyCriterias(Category $category, array $criteria)
    {
        /** @var Widget[] $widgets */
        $widgets = $this->widgetRepository->findByForm($category->getForm());
        # Get Widget by category->frm (bypass area)

        $subOrWheres = [];
        $subOrWhereParameters = [];

        foreach ($widgets as $widget) {

            if (
                !empty($criteria[$widget->getId()]) &&
                ($criteria[$widget->getId()]['criteria'] ?? SearchCriteriaEnum::DISABLED) !== SearchCriteriaEnum::DISABLED)
            {

                $type = ucfirst($widget->getType());
                $valueColumn = "valueOfType{$type}";
                $parameterKey = "value{$widget->getId()}";
                $searchCriteria = $criteria[$widget->getId()]['criteria'];
                $searchValue = $criteria[$widget->getId()]['value'];

                switch ($searchCriteria) {
                    case SearchCriteriaEnum::IS_NULL:
                        $subOrWheres[] = "v.{$valueColumn} IS NULL";
                        break;
                    case SearchCriteriaEnum::IS_NOT_NULL:
                        $subOrWheres[] = "v.{$valueColumn} IS NOT NULL";
                        break;
                    case SearchCriteriaEnum::EXACT:
                        $subOrWheres[] = "v.{$valueColumn} = :{$parameterKey}";
                        $subOrWhereParameters[$parameterKey] = $searchValue;
                        break;
                    case SearchCriteriaEnum::CONTAINS:
                        $subOrWheres[] = "v.{$valueColumn} LIKE :{$parameterKey}";
                        $subOrWhereParameters[$parameterKey] = "%{$searchValue}%";
                        break;
                    case SearchCriteriaEnum::STARTS_WITH:
                        $subOrWheres[] = "v.{$valueColumn} LIKE :{$parameterKey}";
                        $subOrWhereParameters[$parameterKey] = "{$searchValue}%";
                        break;
                    case SearchCriteriaEnum::ENDS_WITH:
                        $subOrWheres[] = "v.{$valueColumn} LIKE :{$parameterKey}";
                        $subOrWhereParameters[$parameterKey] = "%{$searchValue}";
                        break;
                }
            }
        }

        # Apply subOrWhere and parameters
        if (!empty($subOrWheres)) {

            $this->searchCriteriaCount = count($subOrWheres);

            $this->qb
                ->andWhere( implode(' OR ', $subOrWheres) )
            ;

            # setParameters erase previously defined parameters
            foreach ($subOrWhereParameters as $parameterKey => $value)
            {
                $this->qb->setParameter($parameterKey, $value);
            }
        }
    }

    private function appendToQueryBySwitch(string $valueColumn, string $parameterKey, $searchValue)
    {
        
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