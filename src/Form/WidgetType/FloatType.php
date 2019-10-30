<?php

namespace App\Form\WidgetType;

use App\Entity\Widget;
use App\Enum\FicheModeEnum;
use App\Enum\SearchCriteriaEnum;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;

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
                    return 'trans.'.$label;
                },
                'choice_attr' => function(string $value) {
                    $attr = [];
                    if ($value === SearchCriteriaEnum::BETWEEN) {
                        $attr['class'] = 'display-value2';
                    }
                    return $attr;
                }
            ])
        ;

        $valueOptions = [
            'required' => false,
            'attr' => [
                'placeholder' => $widget->getInputPlaceholder(),
                'min' => $widget->getMin() ?? '',
                'max' => $widget->getMax() ?? '',
                'step' => 'any'
            ],
            'html5' => true,
            'constraints' => [
                new Type(['type' => 'numeric'])
            ]
        ];

        $builder
            ->add('value', NumberType::class, $valueOptions)

            # Second input, useful in some case (for instance with SearchCriteria BETWEEN)
            ->add('value2', NumberType::class,[
                    'attr' => ['class' => 'value2 hidden'] + $valueOptions['attr']
                ] + $valueOptions
            )
        ;
    }

    protected function getSearchCriterias(): array
    {
        return [
            SearchCriteriaEnum::DISABLED,
            SearchCriteriaEnum::IS_NULL,
            SearchCriteriaEnum::IS_NOT_NULL,
            SearchCriteriaEnum::EQUAL_TO,
            SearchCriteriaEnum::GREATER_THAN,
            SearchCriteriaEnum::LOWER_THAN,
            SearchCriteriaEnum::BETWEEN
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