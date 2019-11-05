<?php

namespace App\Form\SearchType;

use App\Entity\Widget;
use App\Enum\SearchCriteriaEnum;
use App\Form\FormBuilderType\TimeType as TimeTypeSingle;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class TimeType extends AbstractSearchType
{
    public function getBlockPrefix()
    {
        return 'fichit_time';
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
                        case SearchCriteriaEnum::TIME_EQUAL_TO:
                        case SearchCriteriaEnum::TIME_LOWER_THAN:
                        case SearchCriteriaEnum::TIME_GREATER_THAN:
                            $attr['data-inputs'] = '.value';
                            break;
                        case SearchCriteriaEnum::TIME_BETWEEN:
                            $attr['data-inputs'] = '.value,.value2';
                            break;
                        case SearchCriteriaEnum::HOUR_EQUAL_TO:
                        case SearchCriteriaEnum::HOUR_LESS_THAN:
                        case SearchCriteriaEnum::HOUR_GREATER_THAN:
                            $attr['data-inputs'] = '.hour';
                            break;
                        case SearchCriteriaEnum::HOUR_BETWEEN:
                            $attr['data-inputs'] = '.hour,.hour2';
                            break;
                        case SearchCriteriaEnum::MINUTE_EQUAL_TO:
                        case SearchCriteriaEnum::MINUTE_LESS_THAN:
                        case SearchCriteriaEnum::MINUTE_GREATER_THAN:
                        case SearchCriteriaEnum::SECOND_EQUAL_TO:
                        case SearchCriteriaEnum::SECOND_LESS_THAN:
                        case SearchCriteriaEnum::SECOND_GREATER_THAN:
                            $attr['data-inputs'] = '.minOrSec';
                            break;
                        case SearchCriteriaEnum::MINUTE_BETWEEN:
                        case SearchCriteriaEnum::SECOND_BETWEEN:
                            $attr['data-inputs'] = '.minOrSec,.minOrSec2';
                            break;
                    }
                    return $attr;
                }
            ])
            ->add('value', TextType::class, [
                    'required' => false,
                    'attr' => [
                            'class' => 'value hidden',
                            'placeholder' => TimeTypeSingle::getTimeTypePlaceholder($widget)
                        ] + TimeTypeSingle::getHTMLInputAttributes($widget)
                ]
            )
            ->add('value2', TextType::class, [
                    'required' => false,
                    'attr' => [
                            'class' => 'value2 hidden',
                            'placeholder' => TimeTypeSingle::getTimeTypePlaceholder($widget)
                        ] + TimeTypeSingle::getHTMLInputAttributes($widget)
                ]
            )
            ->add('hour', IntegerType::class, [
                    'required' => false,
                    'attr' => [
                        'class' => 'hour hidden',
                        'min' => 0,
                        'max' => 24
                    ]
                ]
            )
            ->add('hour2', IntegerType::class, [
                    'required' => false,
                    'attr' => [
                        'class' => 'hour2 hidden',
                        'min' => 0,
                        'max' => 24
                    ]
                ]
            )
            ->add('minOrSec', IntegerType::class, [
                    'required' => false,
                    'attr' => [
                        'class' => 'minOrSec hidden',
                        'min' => 0,
                        'max' => 60
                    ]
                ]
            )
            ->add('minOrSec2', IntegerType::class, [
                    'required' => false,
                    'attr' => [
                        'class' => 'minOrSec2 hidden',
                        'min' => 0,
                        'max' => 60
                    ]
                ]
            )
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
                $data['value'] = TimeTypeSingle::transformTo($widget, $value);
                $value2 = !empty($data['value2']) ? $data['value2'] : null;
                $data['value2'] = TimeTypeSingle::transformTo($widget, $value2);
                $formEvent->setData($data);
            }
        });

        # We cannot add modelTransformer in eventListenerHandler
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(PreSubmitEvent $preSubmitEvent) use ($widget){
            $data = $preSubmitEvent->getData();

            $value = !empty($data['value']) ? $data['value'] : null;
            $value = TimeTypeSingle::transformFrom($widget, $value);
            if ($value instanceof \DateTime) {
                $data['value'] = $value->format('H:i:s');
            }
            else $data['value'] = null;

            $value2 = !empty($data['value2']) ? $data['value2'] : null;
            $value2 = TimeTypeSingle::transformFrom($widget, $value2);
            if ($value2 instanceof \DateTime) {
                $data['value2'] = $value2->format('H:i:s');
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
            SearchCriteriaEnum::TIME_EQUAL_TO,
            SearchCriteriaEnum::TIME_LOWER_THAN,
            SearchCriteriaEnum::TIME_GREATER_THAN,
            SearchCriteriaEnum::TIME_BETWEEN,

            SearchCriteriaEnum::HOUR_EQUAL_TO,
            SearchCriteriaEnum::HOUR_LESS_THAN,
            SearchCriteriaEnum::HOUR_GREATER_THAN,
            SearchCriteriaEnum::HOUR_BETWEEN,

            SearchCriteriaEnum::MINUTE_EQUAL_TO,
            SearchCriteriaEnum::MINUTE_LESS_THAN,
            SearchCriteriaEnum::MINUTE_GREATER_THAN,
            SearchCriteriaEnum::MINUTE_BETWEEN,

            SearchCriteriaEnum::SECOND_EQUAL_TO,
            SearchCriteriaEnum::SECOND_LESS_THAN,
            SearchCriteriaEnum::SECOND_GREATER_THAN,
            SearchCriteriaEnum::SECOND_BETWEEN
        ];
    }
}