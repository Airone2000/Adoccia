<?php

namespace App\Form\WidgetType;

use App\Entity\Widget;
use App\Enum\DateFormatEnum;
use App\Enum\FicheModeEnum;
use App\Enum\SearchCriteriaEnum;
use App\Enum\TimeFormatEnum;
use Symfony\Component\Form\Event\PreSetDataEvent;
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

class TimeType extends AbstractWidgetType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['mode'] === FicheModeEnum::SEARCH) {
            $this->buildSearchForm($builder, $options);
        }
    }

    public function getTimeTypePlaceholder(Widget $widget): ?string
    {
        if ($widget->getInputPlaceholder()) {
            $placeholder = $widget->getInputPlaceholder();
        }
        else {
            $placeholder = preg_replace('/[hms]/i', '_', $widget->getTimeFormat());
        }

        return $placeholder;
    }

    private function getHTMLInputAttributes(Widget $widget, array $attr = []): array
    {
        $attr['data-masked'] = 'true';
        $attr['data-inputmask-alias'] = 'datetime';
        $attr['data-inputmask-inputformat'] = $widget->getTimeFormat();
        $attr['data-inputmask-placeholder'] = $this->getTimeTypePlaceholder($widget);
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
        return 'fichit_time';
    }

    private function buildSearchForm(FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $valueOptions = [
            'required' => false,
            'attr' => [
                'placeholder' => $this->getTimeTypePlaceholder($widget)
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
                        case SearchCriteriaEnum::TIME_EQUAL_TO:
                        case SearchCriteriaEnum::TIME_LOWER_THAN:
                        case SearchCriteriaEnum::TIME_GREATER_THAN:
                            $attr['data-inputs'] = '.value';
                            break;
                        case SearchCriteriaEnum::TIME_BETWEEN:
                            $attr['data-inputs'] = '.value,.value2';
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
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(PreSetDataEvent $preSetDataEvent) use ($widget){
            $data = $preSetDataEvent->getData();
            if ($data !== null) {
                $value = !empty($data['value']) ? $data['value'] : null;
                $data['value'] = self::transformTo($widget, $value);
                $value2 = !empty($data['value2']) ? $data['value2'] : null;
                $data['value2'] = self::transformTo($widget, $value2);
                $preSetDataEvent->setData($data);
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(PreSubmitEvent $preSubmitEvent) use ($widget){
            $data = $preSubmitEvent->getData();

            $value = !empty($data['value']) ? $data['value'] : null;
            $value = self::transformFrom($widget, $value);
            if ($value instanceof \DateTime) {
                $data['value'] = $value->format('H:i:s');
            }
            else $data['value'] = null;

            $value2 = !empty($data['value2']) ? $data['value2'] : null;
            $value2 = self::transformFrom($widget, $value2);
            if ($value2 instanceof \DateTime) {
                $data['value2'] = $value2->format('H:i:s');
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
            SearchCriteriaEnum::TIME_EQUAL_TO,
            SearchCriteriaEnum::TIME_LOWER_THAN,
            SearchCriteriaEnum::TIME_GREATER_THAN,
            SearchCriteriaEnum::TIME_BETWEEN
        ];
    }

    public static function transformFrom(Widget $widget, $value)
    {
        if (is_string($value)) {
            $timeFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['php'];
            $datetime = \DateTime::createFromFormat($timeFormat, $value);
            if ($datetime !== false) {
                $datetime->setDate(0,0,0);
                return $datetime;
            }
        }
        return null;
    }

    public static function transformTo(Widget $widget, $value)
    {
        if (is_string($value)) {
            $value = \DateTime::createFromFormat('H:i:s', $value);
            $value->setDate(0,0,0);
        }

        if ($value instanceof \DateTime) {
            $timeFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['php'];
            return $value->format($timeFormat);
        }
        return null;
    }


}