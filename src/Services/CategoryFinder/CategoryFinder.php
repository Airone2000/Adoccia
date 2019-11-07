<?php

namespace App\Services\CategoryFinder;

use App\Entity\Category;
use App\Entity\Value;
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
    private $fichesWhereAuthors = ['direction' => null, 'authors' => []];

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

        # Filter on authors
        if ($this->fichesWhereAuthors['direction'] !== null && !empty($this->fichesWhereAuthors['authors'])) {
            $this->searchCriteriaCount++;
            $fichesQ
                ->andWhere("f.creator {$this->fichesWhereAuthors['direction']} (:authors)")
                ->setParameter('authors', $this->fichesWhereAuthors['authors'])
            ;
        }

        if ($this->searchCriteriaCount > 0) {
            $alias = $fichesQ->getRootAliases()[0];
            $fichesQ
                ->andWhere($alias.'.valid = 1') # Fiche not valid are not trustable for research
            ;

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
                            $phpFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['php'];
                            $date = \DateTime::createFromFormat('H:i:s', $searchValue);
                            if ($date instanceof \DateTime) {
                                $searchValue = $date->format($phpFormat);
                                $sqlFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['sql'];
                                $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND DATE_FORMAT(v.{$valueColumn}, '${sqlFormat}') = '{$searchValue}' )";
                            }
                        }
                        break;
                    case SearchCriteriaEnum::TIME_LOWER_THAN:
                        if ($searchValue != null) {
                            $phpFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['php'];
                            $date = \DateTime::createFromFormat('H:i:s', $searchValue);
                            if ($date instanceof \DateTime) {
                                $searchValue = $date->format($phpFormat);
                                $sqlFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['sql'];
                                $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND DATE_FORMAT(v.{$valueColumn}, '${sqlFormat}') < '{$searchValue}' )";
                            }
                        }
                        break;
                    case SearchCriteriaEnum::TIME_GREATER_THAN:
                        if ($searchValue != null) {
                            $phpFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['php'];
                            $date = \DateTime::createFromFormat('H:i:s', $searchValue);
                            if ($date instanceof \DateTime) {
                                $searchValue = $date->format($phpFormat);
                                $sqlFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['sql'];
                                $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND DATE_FORMAT(v.{$valueColumn}, '${sqlFormat}') > '{$searchValue}' )";
                            }
                        }
                        break;
                    case SearchCriteriaEnum::TIME_BETWEEN:
                        $start = $criteria[$widget->getImmutableId()]['value'];
                        $end = $criteria[$widget->getImmutableId()]['value2'];
                        if ($start !== null && $end !== null) {
                            $phpFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['php'];
                            $dateStart = \DateTime::createFromFormat('H:i:s', $start);
                            $dateEnd = \DateTime::createFromFormat('H:i:s', $end);
                            if ($dateStart instanceof \DateTime && $dateEnd instanceof \DateTime) {
                                $start = $searchValue = $dateStart->format($phpFormat);
                                $end = $searchValue = $dateEnd->format($phpFormat);
                                $sqlFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['sql'];
                                $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND DATE_FORMAT(v.{$valueColumn}, '${sqlFormat}') BETWEEN '{$start}' AND '{$end}' )";
                            }
                        }
                        break;
                    case SearchCriteriaEnum::HOUR_EQUAL_TO:
                    case SearchCriteriaEnum::MINUTE_EQUAL_TO:
                    case SearchCriteriaEnum::SECOND_EQUAL_TO:
                        $criteriaParts = explode('_', $searchCriteria);
                        $timePart = strtolower($criteriaParts[0]);
                        $selector = $timePart === 'hour' ? 'hour' : 'minOrSec';
                        $searchValue = $criteria[$widget->getImmutableId()][$selector];
                        if ($searchValue !== null) {
                            $searchValue = (int)$searchValue;
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND {$timePart}(v.{$valueColumn}) = '{$searchValue}')";
                        }
                        break;
                    case SearchCriteriaEnum::HOUR_LESS_THAN:
                    case SearchCriteriaEnum::MINUTE_LESS_THAN:
                    case SearchCriteriaEnum::SECOND_LESS_THAN:
                        $criteriaParts = explode('_', $searchCriteria);
                        $timePart = strtolower($criteriaParts[0]);
                        $selector = $timePart === 'hour' ? 'hour' : 'minOrSec';
                        $searchValue = $criteria[$widget->getImmutableId()][$selector];
                        if ($searchValue !== null) {
                            $searchValue = (int)$searchValue;
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND {$timePart}(v.{$valueColumn}) < '{$searchValue}')";
                        }
                        break;
                    case SearchCriteriaEnum::HOUR_GREATER_THAN:
                    case SearchCriteriaEnum::MINUTE_GREATER_THAN:
                    case SearchCriteriaEnum::SECOND_GREATER_THAN:
                        $criteriaParts = explode('_', $searchCriteria);
                        $timePart = strtolower($criteriaParts[0]);
                        $selector = $timePart === 'hour' ? 'hour' : 'minOrSec';
                        $searchValue = $criteria[$widget->getImmutableId()][$selector];
                        if ($searchValue !== null) {
                            $searchValue = (int)$searchValue;
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND {$timePart}(v.{$valueColumn}) > '{$searchValue}')";
                        }
                        break;
                    case SearchCriteriaEnum::HOUR_BETWEEN:
                    case SearchCriteriaEnum::MINUTE_BETWEEN:
                    case SearchCriteriaEnum::SECOND_BETWEEN:
                        $criteriaParts = explode('_', $searchCriteria);
                        $timePart = strtolower($criteriaParts[0]);
                        $selector = $timePart === 'hour' ? 'hour' : 'minOrSec';
                        $start = $criteria[$widget->getImmutableId()][$selector];
                        $end = $criteria[$widget->getImmutableId()]["{$selector}2"];
                        if ($start !== null && $end !== null) {
                            $start = (int) $start; $end = (int) $end;
                            $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND {$timePart}(v.{$valueColumn}) BETWEEN '{$start}' AND '{$end}')";
                        }
                        break;
                    case SearchCriteriaEnum::IN_ARRAY:
                        if ($searchValue !== null) {
                            $searchValue = json_decode($searchValue, true);
                            if (is_array($searchValue)) {
                                $subAndWheres = [];
                                $i = 0;
                                foreach ($searchValue as $value) {
                                    $subParameterKey = ($parameterKey . $i++);
                                    $subAndWheres[] = "(REGEXP(v.{$valueColumn}, :{$subParameterKey}) = 1)";
                                    $subOrWhereParameters[$subParameterKey] = $value;
                                }
                                if (!empty($subAndWheres)) {
                                    $glue = $widget->hasMultipleValues() ? ' AND ' : ' OR ';
                                    $subAndWheres = implode($glue, $subAndWheres);
                                    $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND ({$subAndWheres}))";
                                }
                            }
                        }
                        break;
                    case SearchCriteriaEnum::NOT_IN_ARRAY:
                        if ($searchValue !== null) {
                            $searchValue = json_decode($searchValue, true);
                            if (is_array($searchValue)) {
                                $subAndWheres = [];
                                $i = 0;
                                foreach ($searchValue as $value) {
                                    $subParameterKey = ($parameterKey . $i++);
                                    $subAndWheres[] = "(REGEXP(v.{$valueColumn}, :{$subParameterKey}) = 0)";
                                    $subOrWhereParameters[$subParameterKey] = $value;
                                }
                                if (!empty($subAndWheres)) {
                                    $glue = $widget->hasMultipleValues() ? ' OR ' : ' AND ';
                                    $subAndWheres = implode($glue, $subAndWheres);
                                    $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND ({$subAndWheres}))";
                                }
                            }
                        }
                        break;
                    case SearchCriteriaEnum::IN_ARRAY_EXACT:
                        if ($searchValue !== null) {
                            $searchValue = json_decode($searchValue, true);
                            if (is_array($searchValue)) {

                                $length = array_reduce($searchValue, function($acc, $value){
                                    return $acc + mb_strlen($value);
                                }, count($searchValue) - 1);

                                $subAndWheres = [];
                                $i = 0;
                                foreach ($searchValue as $value) {
                                    $subParameterKey = ($parameterKey . $i++);
                                    $subAndWheres[] = "(REGEXP(v.{$valueColumn}, :{$subParameterKey}) = 1)";
                                    $subOrWhereParameters[$subParameterKey] = $value;
                                }
                                if (!empty($subAndWheres)) {
                                    $subAndWheres = implode(' AND ', $subAndWheres);
                                    $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND CHAR_LENGTH(v.{$valueColumn}) = {$length} AND ({$subAndWheres}))";
                                }
                            }
                        }
                        break;
                    case SearchCriteriaEnum::BUTTON_LABEL_EQUAL_TO:
                    case SearchCriteriaEnum::BUTTON_LABEL_NOT_EQUAL_TO:
                    case SearchCriteriaEnum::BUTTON_TARGET_EQUAL_TO:
                    case SearchCriteriaEnum::BUTTON_TARGET_NOT_EQUAL_TO:
                        if ($searchValue !== null) {
                            $attribute = strpos($searchCriteria, 'LABEL') !== false ? 'ilabel' : 'itarget';
                            $not = strpos($searchCriteria, 'NOT') !== false ? 'NOT' : '';
                            $searchValue = mb_strtolower($searchValue);
                            $searchValue = explode(',', $searchValue);
                            $searchValue = $this->removeNullOrBlankValuesFromArray($searchValue);
                            if (!empty($searchValue)) {
                                $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND JSON_UNQUOTE(JSON_EXTRACT(v.valueOfTypeButton, '$.{$attribute}')) {$not} IN (:{$parameterKey}) )";
                                $subOrWhereParameters[$parameterKey] = $searchValue;
                            }
                        }
                        break;
                    case SearchCriteriaEnum::BUTTON_LABEL_CONTAINS:
                    case SearchCriteriaEnum::BUTTON_LABEL_NOT_CONTAINS:
                    case SearchCriteriaEnum::BUTTON_TARGET_CONTAINS:
                    case SearchCriteriaEnum::BUTTON_TARGET_NOT_CONTAINS:
                        if ($searchValue !== null) {
                            $attribute = strpos($searchCriteria, 'LABEL') !== false ? 'ilabel' : 'itarget';
                            $not = strpos($searchCriteria, 'NOT') !== false ? '0' : '1';
                            $searchValue = mb_strtolower($searchValue);
                            $searchValue = explode(',', $searchValue);
                            $searchValue = $this->removeNullOrBlankValuesFromArray($searchValue);
                            if (!empty($searchValue)) {
                                $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND REGEXP(JSON_UNQUOTE(JSON_EXTRACT(v.valueOfTypeButton, '$.{$attribute}')), :{$parameterKey}) = {$not})";
                                $subOrWhereParameters[$parameterKey] = implode('|', $searchValue);
                            }
                        }
                        break;
                    case SearchCriteriaEnum::BUTTON_LABEL_IS_NULL:
                    case SearchCriteriaEnum::BUTTON_LABEL_IS_NOT_NULL:
                    case SearchCriteriaEnum::BUTTON_TARGET_IS_NULL:
                    case SearchCriteriaEnum::BUTTON_TARGET_IS_NOT_NULL:
                        $attribute = strpos($searchCriteria, 'LABEL') !== false ? 'ilabel' : 'itarget';
                        $operator = strpos($searchCriteria, 'NOT') !== false ? '<>' : '=';
                        $subOrWheres[] = "(v.widgetImmutableId = '{$widget->getImmutableId()}' AND JSON_EXTRACT(v.valueOfTypeButton, '$.{$attribute}') {$operator} '')";
                        break;
                    case SearchCriteriaEnum::CREATOR_IS:
                    case SearchCriteriaEnum::CREATOR_IS_NOT:
                        if (!empty($searchValue) && is_array($searchValue)) {
                            $direction = strpos($searchCriteria, 'NOT') !== false ? 'NOT IN' : 'IN';
                            $this->fichesWhereAuthors['direction'] = $direction;
                            $this->fichesWhereAuthors['authors'] = $searchValue;
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