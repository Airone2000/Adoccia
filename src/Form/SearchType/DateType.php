<?php

namespace App\Form\SearchType;

use App\Entity\Widget;
use App\Enum\SearchCriteriaEnum;
use App\Form\FormBuilderType\DateType as DateTypeSingle;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class DateType extends AbstractSearchType
{
    public function getBlockPrefix()
    {
        return 'fichit_date';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $builder
            ->add('criteria', ChoiceType::class, [
                'choices' => $this->getSearchCriterias(),
                'choice_label' => function(string $label) {
                    return 'trans.'.$label;
                },
                'choice_attr' => function(string $value) {
                    $attr = [];
                    switch ($value) {
                        case SearchCriteriaEnum::BETWEEN:
                            $attr['data-inputs'] = '.value,.value2';
                            break;
                        case SearchCriteriaEnum::EXACT:
                        case SearchCriteriaEnum::LOWER_THAN:
                        case SearchCriteriaEnum::GREATER_THAN:
                            $attr['data-inputs'] = '.value';
                            break;
                        case SearchCriteriaEnum::YEAR_EQUAL_TO:
                        case SearchCriteriaEnum::YEAR_LESS_THAN:
                        case SearchCriteriaEnum::YEAR_GREATER_THAN:
                            $attr['data-inputs'] = '.valueYear';
                            break;
                        case SearchCriteriaEnum::YEAR_BETWEEN:
                            $attr['data-inputs'] = '.valueYearFrom,.valueYearTo';
                            break;
                        case SearchCriteriaEnum::MONTH_EQUAL_TO:
                        case SearchCriteriaEnum::MONTH_LESS_THAN:
                        case SearchCriteriaEnum::MONTH_GREATER_THAN:
                            $attr['data-inputs'] = '.valueMonth';
                            break;
                        case SearchCriteriaEnum::MONTH_BETWEEN:
                            $attr['data-inputs'] = '.valueMonthFrom,.valueMonthTo';
                            break;
                        case SearchCriteriaEnum::DAY_EQUAL_TO:
                        case SearchCriteriaEnum::DAY_LESS_THAN:
                        case SearchCriteriaEnum::DAY_GREATER_THAN:
                            $attr['data-inputs'] = '.valueDay';
                            break;
                        case SearchCriteriaEnum::DAY_BETWEEN:
                            $attr['data-inputs'] = '.valueDayFrom,.valueDayTo';
                            break;
                    }
                    return $attr;
                }
            ])
            ->add('value', TextType::class, [
                'attr' => [
                        'class' => 'value hidden',
                        'placeholder' => DateTypeSingle::getDateTypePlaceholder($widget)
                    ] + DateTypeSingle::getHTMLInputAttributes($widget),
                'required' => false
            ])
            ->add('value2', TextType::class, [
                'attr' => [
                        'class' => 'value2 hidden',
                        'placeholder' => DateTypeSingle::getDateTypePlaceholder($widget)
                    ] + DateTypeSingle::getHTMLInputAttributes($widget),
                'required' => false
            ])
            ->add('valueYear', IntegerType::class, [
                'attr' => ['class' => 'valueYear hidden'],
                'required' => false
            ])
            ->add('valueYearFrom', IntegerType::class, [
                'attr' => ['class' => 'valueYearFrom hidden'],
                'required' => false
            ])
            ->add('valueYearTo', IntegerType::class, [
                'attr' => ['class' => 'valueYearTo hidden'],
                'required' => false
            ])
            ->add('valueMonth', IntegerType::class, [
                'attr' => ['class' => 'valueMonth hidden'],
                'required' => false
            ])
            ->add('valueMonthFrom', IntegerType::class, [
                'attr' => ['class' => 'valueMonthFrom hidden'],
                'required' => false
            ])
            ->add('valueMonthTo', IntegerType::class, [
                'attr' => ['class' => 'valueMonthTo hidden'],
                'required' => false
            ])
            ->add('valueDay', IntegerType::class, [
                'attr' => ['class' => 'valueDay hidden'],
                'required' => false
            ])
            ->add('valueDayFrom', IntegerType::class, [
                'attr' => ['class' => 'valueDayFrom hidden'],
                'required' => false
            ])
            ->add('valueDayTo', IntegerType::class, [
                'attr' => ['class' => 'valueDayTo hidden'],
                'required' => false
            ])
        ;

        /**
         * Get the value of criteria.
         * If it's equal to BETWEEN, let's display the Value2 input
         */
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $formEvent) use ($widget) {
            /**
             * Entity Search stores date like Y-m-d.
             * To render this value back in the form, we must convert it based on the widget->dateFormat()
             */
            $data = $formEvent->getData();
            if ($data !== null) {
                $value = !empty($data['value']) ? $data['value'] : null;
                $data['value'] = DateTypeSingle::transformTo($widget, $value);
                $value2 = !empty($data['value2']) ? $data['value2'] : null;
                $data['value2'] = DateTypeSingle::transformTo($widget, $value2);
                $formEvent->setData($data);
            }
        });

        # We cannot add modelTransformer in eventListenerHandler
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(PreSubmitEvent $preSubmitEvent) use ($widget){
            $data = $preSubmitEvent->getData();

            $value = !empty($data['value']) ? $data['value'] : null;
            $value = DateTypeSingle::transformFrom($widget, $value);
            if ($value instanceof \DateTime) {
                $data['value'] = $value->format('Y-m-d');
            }
            else $data['value'] = null;

            $value2 = !empty($data['value2']) ? $data['value2'] : null;
            $value2 = DateTypeSingle::transformFrom($widget, $value2);
            if ($value2 instanceof \DateTime) {
                $data['value2'] = $value2->format('Y-m-d');
            }
            else $data['value2'] = null;

            $preSubmitEvent->setData($data);
        });
    }

    private function getSearchCriterias(): array
    {
        return [
            SearchCriteriaEnum::DISABLED,
            SearchCriteriaEnum::IS_NULL,
            SearchCriteriaEnum::IS_NOT_NULL,
            SearchCriteriaEnum::EXACT,
            SearchCriteriaEnum::LOWER_THAN,
            SearchCriteriaEnum::GREATER_THAN,
            SearchCriteriaEnum::BETWEEN,

            SearchCriteriaEnum::YEAR_EQUAL_TO,
            SearchCriteriaEnum::YEAR_LESS_THAN,
            SearchCriteriaEnum::YEAR_GREATER_THAN,
            SearchCriteriaEnum::YEAR_BETWEEN,

            SearchCriteriaEnum::MONTH_EQUAL_TO,
            SearchCriteriaEnum::MONTH_LESS_THAN,
            SearchCriteriaEnum::MONTH_GREATER_THAN,
            SearchCriteriaEnum::MONTH_BETWEEN,

            SearchCriteriaEnum::DAY_EQUAL_TO,
            SearchCriteriaEnum::DAY_LESS_THAN,
            SearchCriteriaEnum::DAY_GREATER_THAN,
            SearchCriteriaEnum::DAY_BETWEEN
        ];
    }
}