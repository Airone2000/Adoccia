<?php

namespace App\Services\CategoryFinder;

use App\Entity\Category;
use App\Entity\Widget;
use App\Enum\SearchCriteriaEnum;
use App\Enum\TimeFormatEnum;
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

    /**
     * @var array
     */
    private $lastSearchCriterias;

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
        $this->lastSearchCriterias = $criterias;

        $this->qb = $this->valueRepository->createQueryBuilder('v');

        # Filter on category
        $this->setWhereCategory($category);

        # Apply criterias
        $this->applyCriterias($category, $criterias);

        # Matching widgets
        $matchingValues = $this->qb->getQuery()->getArrayResult();

        # Query for fiches in this category
        $fichesQ = $this->ficheRepository->createQueryBuilder('f');
        $fichesQ->andWhere('f.category = :category')->setParameter('category', $category);

        # Search fiches by matching widgets
        if ($this->searchCriteriaCount > 0) {
            $this->ficheRepository->getFicheByValues($fichesQ, $matchingValues, $this->searchCriteriaCount);
        }

        # Filter on title
        if (array_key_exists('title', $criterias)) {
            $criteriaTitle = $criterias['title'];
            if ($criteriaTitle['criteria'] !== SearchCriteriaEnum::DISABLED && !empty($criteriaTitle['value'])) {
                $this->searchCriteriaCount++;
                $this->ficheRepository->filterByTitle($fichesQ, $criteriaTitle['criteria'], $criteriaTitle['value']);
            }
        }

        if ($this->searchCriteriaCount > 0) {
            return $fichesQ->getQuery()->getResult();
        }

        # No filtering -> empty
        # That avoids to have too many data in the output
        return [];
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
                !empty($criteria[$widget->getImmutableId()]) &&
                ($criteria[$widget->getImmutableId()]['criteria'] ?? SearchCriteriaEnum::DISABLED) !== SearchCriteriaEnum::DISABLED)
            {

                $type = ucfirst($widget->getType());
                $valueColumn = "valueOfType{$type}";
                $parameterKey = "value{$widget->getImmutableId()}";
                $searchCriteria = $criteria[$widget->getImmutableId()]['criteria'];
                $searchValue = $criteria[$widget->getImmutableId()]['value'];

                switch ($searchCriteria) {
                    case SearchCriteriaEnum::IS_NULL:
                        $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND v.{$valueColumn} IS NULL)";
                        break;
                    case SearchCriteriaEnum::IS_NOT_NULL:
                        $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND v.{$valueColumn} IS NOT NULL)";
                        break;
                    case SearchCriteriaEnum::EXACT:
                        if ($searchValue !== null) {
                            $searchValue = explode(',', $searchValue);
                            $searchValue = $this->removeNullOrBlankValuesFromArray($searchValue);
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND v.{$valueColumn} IN (:{$parameterKey}))";
                            $subOrWhereParameters[$parameterKey] = $searchValue;
                        }
                        break;
                    case SearchCriteriaEnum::CONTAINS:
                        if ($searchValue !== null) {
                            $searchValue = explode(',', $searchValue);
                            $searchValue = $this->removeNullOrBlankValuesFromArray($searchValue);
                            if (!empty($searchValue)) {
                                $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND REGEXP(v.{$valueColumn}, :{$parameterKey}) = 1)";
                                $subOrWhereParameters[$parameterKey] = implode('|', $searchValue);
                            }
                        }
                        break;
                    case SearchCriteriaEnum::STARTS_WITH:
                        if ($searchValue !== null) {
                            $searchValue = explode(',', $searchValue);
                            $searchValue = $this->removeNullOrBlankValuesFromArray($searchValue);
                            if (!empty($searchValue)) {
                                $regexp = '^(' . implode('|', $searchValue) . ')';
                                $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND REGEXP(v.{$valueColumn}, :{$parameterKey}) = 1)";
                                $subOrWhereParameters[$parameterKey] = $regexp;
                            }
                        }
                        break;
                    case SearchCriteriaEnum::ENDS_WITH:
                        if ($searchValue !== null) {
                            $searchValue = explode(',', $searchValue);
                            $searchValue = $this->removeNullOrBlankValuesFromArray($searchValue);
                            if (!empty($searchValue)) {
                                $regexp = '(' . implode('|', $searchValue) . ')$';
                                $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND REGEXP(v.{$valueColumn}, :{$parameterKey}) = 1)";
                                $subOrWhereParameters[$parameterKey] = $regexp;
                            }
                        }
                        break;
                    case SearchCriteriaEnum::GREATER_THAN:
                        if ($searchValue !== null) {
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND v.{$valueColumn} > :{$parameterKey})";
                            $subOrWhereParameters[$parameterKey] = $searchValue;
                        }
                        break;
                    case SearchCriteriaEnum::LOWER_THAN:
                        if ($searchValue !== null) {
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND v.{$valueColumn} < :{$parameterKey})";
                            $subOrWhereParameters[$parameterKey] = $searchValue;
                        }
                        break;
                    case SearchCriteriaEnum::EQUAL_TO:
                        if ($searchValue !== null) {
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND v.{$valueColumn} = :{$parameterKey})";
                            $subOrWhereParameters[$parameterKey] = $searchValue;
                        }
                        break;
                    case SearchCriteriaEnum::BETWEEN:
                        $searchValue2 = isset($criteria[$widget->getImmutableId()]['value2']) ? $criteria[$widget->getImmutableId()]['value2'] : null;
                        if ($searchValue !== null && $searchValue2 !== null) {
                            # Cannot bind as parameter because doctrine casts it to string and then filtering is wrong
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND v.{$valueColumn} BETWEEN '{$searchValue}' AND '{$searchValue2}')";
                        }
                        break;
                    case SearchCriteriaEnum::YEAR_EQUAL_TO:
                    case SearchCriteriaEnum::MONTH_EQUAL_TO:
                    case SearchCriteriaEnum::DAY_EQUAL_TO:
                        $criteriaParts = explode('_', $searchCriteria);
                        $datePart = strtolower($criteriaParts[0]);
                        $searchValue = $criteria[$widget->getImmutableId()]['value'. ucfirst($datePart)];
                        if ($searchValue !== null) {
                            $searchValue = (int)$searchValue;
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND {$datePart}(v.{$valueColumn}) = :{$parameterKey})";
                            $subOrWhereParameters[$parameterKey] = $searchValue;
                        }
                        break;
                    case SearchCriteriaEnum::YEAR_LESS_THAN:
                    case SearchCriteriaEnum::MONTH_LESS_THAN:
                    case SearchCriteriaEnum::DAY_LESS_THAN:
                        $criteriaParts = explode('_', $searchCriteria);
                        $datePart = strtolower($criteriaParts[0]);
                        $searchValue = $criteria[$widget->getImmutableId()]['value'. ucfirst($datePart)];
                        if ($searchValue !== null) {
                            $searchValue = (int)$searchValue;
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND {$datePart}(v.{$valueColumn}) < :{$parameterKey})";
                            $subOrWhereParameters[$parameterKey] = $searchValue;
                        }
                        break;
                    case SearchCriteriaEnum::YEAR_GREATER_THAN:
                    case SearchCriteriaEnum::MONTH_GREATER_THAN:
                    case SearchCriteriaEnum::DAY_GREATER_THAN:
                        $criteriaParts = explode('_', $searchCriteria);
                        $datePart = strtolower($criteriaParts[0]);
                        $searchValue = $criteria[$widget->getImmutableId()]['value'. ucfirst($datePart)];
                        if ($searchValue !== null) {
                            $searchValue = (int)$searchValue;
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND {$datePart}(v.{$valueColumn}) > :{$parameterKey})";
                            $subOrWhereParameters[$parameterKey] = $searchValue;
                        }
                        break;
                    case SearchCriteriaEnum::YEAR_BETWEEN:
                    case SearchCriteriaEnum::MONTH_BETWEEN:
                    case SearchCriteriaEnum::DAY_BETWEEN:
                        $criteriaParts = explode('_', $searchCriteria);
                        $datePart = strtolower($criteriaParts[0]);
                        $from = $criteria[$widget->getImmutableId()]['value'.ucfirst($datePart).'From'];
                        $to = $criteria[$widget->getImmutableId()]['value'.ucfirst($datePart).'To'];
                        if ($from !== null && $to !== null) {
                            $from = (int)$from; $to = (int)$to;
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND {$datePart}(v.{$valueColumn}) BETWEEN '{$from}' AND '{$to}')";
                        }
                        break;
                    case SearchCriteriaEnum::TIME_EQUAL_TO:
                        if ($searchValue != null) {
                            $sqlFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['sql'];
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND DATE_FORMAT(v.{$valueColumn}, '${sqlFormat}') = '{$searchValue}' )";
                        }
                        break;
                    case SearchCriteriaEnum::TIME_LOWER_THAN:
                        if ($searchValue != null) {
                            $sqlFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['sql'];
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND DATE_FORMAT(v.{$valueColumn}, '${sqlFormat}') < '{$searchValue}' )";
                        }
                        break;
                    case SearchCriteriaEnum::TIME_GREATER_THAN:
                        if ($searchValue != null) {
                            $sqlFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['sql'];
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND DATE_FORMAT(v.{$valueColumn}, '${sqlFormat}') > '{$searchValue}' )";
                        }
                        break;
                    case SearchCriteriaEnum::TIME_BETWEEN:
                        $start = $criteria[$widget->getImmutableId()]['value'];
                        $end = $criteria[$widget->getImmutableId()]['value2'];
                        if ($start !== null && $end !== null) {
                            $sqlFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['sql'];
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND DATE_FORMAT(v.{$valueColumn}, '${sqlFormat}') BETWEEN '{$start}' AND '{$end}' )";
                        }
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

    private function setWhereCategory(Category $category)
    {
        $this->qb
            ->leftJoin('v.fiche', 'f')
            ->andWhere('f.category = :category')
            ->setParameter('category', $category)
        ;
    }

    public function getLastSearchCriterias(): array
    {
        return $this->lastSearchCriterias ?? [];
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