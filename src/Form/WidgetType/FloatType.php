<?php

namespace App\Form\WidgetType;

use App\Entity\Widget;
use App\Enum\FicheModeEnum;
use App\Enum\SearchCriteriaEnum;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class FloatType extends AbstractWidgetType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['mode'] === FicheModeEnum::SEARCH) {
            $this->buildSearchForm($builder, $options);
        }
    }

    public function getBlockPrefix()
    {
        return 'fichit_float';
    }

    private function buildSearchForm(FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $builder
            ->add('criteria', ChoiceType::class, [
                'choices' => $this->getSearchCriterias(),
                'choice_label' => function(string $label) {
                    return $label;
                }
            ])
        ;

        $builder
            ->add('value', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => $widget->getInputPlaceholder(),
                    'min' => $widget->getMin() ?? '',
                    'max' => $widget->getMax() ?? '',
                    'step' => 'any'
                ],
                'html5' => true
            ])
        ;
    }

    protected function getSearchCriterias(): array
    {
        return [
            SearchCriteriaEnum::DISABLED,
            SearchCriteriaEnum::IS_NULL,
            SearchCriteriaEnum::IS_NOT_NULL,
            SearchCriteriaEnum::EQUAL_TO,
            SearchCriteriaEnum::CONTAINS,
            SearchCriteriaEnum::STARTS_WITH,
            SearchCriteriaEnum::ENDS_WITH,
            SearchCriteriaEnum::GREATER_THAN,
            SearchCriteriaEnum::LOWER_THAN
        ];
    }

    public static function transformToFloat(Widget $widget, $value)
    {
        $decimalCount = $widget->getDecimalCount();
        $value = number_format($value, $decimalCount, '.', '');
        return $value;
    }

    public static function transformTo(Widget $widget, $value)
    {
        return self::transformToFloat($widget, $value);
    }

    public static function transformFrom(Widget $widget, $value)
    {
        return self::transformToFloat($widget, $value);
    }
}