<?php

namespace App\Form\WidgetType;

use App\Entity\Widget;
use App\Enum\DateFormatEnum;
use App\Enum\FicheModeEnum;
use App\Enum\SearchCriteriaEnum;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType as SfTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\DateTime;

class DateType extends AbstractWidgetType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['mode'] === FicheModeEnum::SEARCH) {
            $this->buildSearchForm($builder, $options);
        }
    }

    public function getDateTypePlaceholder(Widget $widget): ?string
    {
        if ($widget->getInputPlaceholder()) {
            $placeholder = $widget->getInputPlaceholder();
        }
        else {
            $placeholder = preg_replace('/[dmy]/i', '_', $widget->getDateFormat());
        }

        return $placeholder;
    }

    private function getHTMLInputAttributes(Widget $widget, array $attr = []): array
    {
        $attr['data-masked'] = 'true';
        $attr['data-inputmask-alias'] = 'datetime';
        $attr['data-inputmask-inputformat'] = $widget->getDateFormat();
        $attr['data-inputmask-placeholder'] = $this->getDateTypePlaceholder($widget);
        $attr['inputmode'] = 'numeric';
        return $attr;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /**
         * @var Widget $widget
         */
        $widget = $options['widget'];


        $view->vars['attr'] = $this->getHTMLInputAttributes($widget, $view->vars['attr'] ?? []);
    }

    public function getBlockPrefix()
    {
        return 'fichit_date';
    }

    private function buildSearchForm(FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $valueOptions = [
            'required' => false,
            'attr' => [
                    'placeholder' => self::getDateTypePlaceholder($widget)
                ] + $this->getHTMLInputAttributes($widget, []),
        ];

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
            ->add('value', SfTextType::class, [
                    'attr' => ['class' => 'value hidden'] + $valueOptions['attr']
                ] + $valueOptions)
            ->add('value2', SfTextType::class, [
                    'attr' => ['class' => 'value2 hidden'] + $valueOptions['attr']
                ] + $valueOptions)
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
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $formEvent) use ($widget, $valueOptions) {
            /**
             * Entity Search stores date like Y-m-d.
             * To render this value back in the form, we must convert it based on the widget->dateFormat()
             */
            $data = $formEvent->getData();
            if ($data !== null) {
                $value = !empty($data['value']) ? $data['value'] : null;
                $data['value'] = self::transformTo($widget, $value);
                $value2 = !empty($data['value2']) ? $data['value2'] : null;
                $data['value2'] = self::transformTo($widget, $value2);
                $formEvent->setData($data);
            }
        });

        # We cannot add modelTransformer in eventListenerHandler
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(PreSubmitEvent $preSubmitEvent) use ($widget){
            $data = $preSubmitEvent->getData();

            $value = !empty($data['value']) ? $data['value'] : null;
            $value = self::transformFrom($widget, $value);
            if ($value instanceof \DateTime) {
                $data['value'] = $value->format('Y-m-d');
            }
            else $data['value'] = null;

            $value2 = !empty($data['value2']) ? $data['value2'] : null;
            $value2 = self::transformFrom($widget, $value2);
            if ($value2 instanceof \DateTime) {
                $data['value2'] = $value2->format('Y-m-d');
            }
            else $data['value2'] = null;

            $preSubmitEvent->setData($data);
        });
    }

    protected function getSearchCriterias(): array
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

    public static function transformFrom(Widget $widget, $value)
    {
        if (is_string($value)) {
            $dateFormat = DateFormatEnum::getPHPFormatForJsFormat($widget->getDateFormat());
            $datetime = \DateTime::createFromFormat($dateFormat, $value);
            if ($datetime !== false) {
                $datetime->setTime(0, 0, 0, 0);
                return $datetime;
            }
        }
        return null;
    }

    public static function transformTo(Widget $widget, $value)
    {
        if (is_string($value) && preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $value)) {
            // Let's assume it can be a value like Y-m-d (default value)
            $value = \DateTime::createFromFormat('Y-m-d', $value);
        }

        if ($value instanceof \DateTime) {
            $dateFormat = DateFormatEnum::getPHPFormatForJsFormat($widget->getDateFormat());
            return $value->format($dateFormat);
        }
        return null;
    }

}