<?php

namespace App\Form\SearchType;

use App\Entity\Widget;
use App\Enum\SearchCriteriaEnum;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;

final class FloatType extends AbstractSearchType
{
    public function getBlockPrefix()
    {
        return 'fichit_float';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget*/
        $widget = $options['widget'];

        $valueOptions = [
            'required' => false,
            'attr' => [
                'placeholder' => $widget->getInputPlaceholder(),
                'min' => $widget->getMin() ?? '',
                'max' => $widget->getMax() ?? '',
                'step' => 'any'
            ],
            'constraints' => [
                new Type(['type' => 'numeric'])
            ],
            'html5' => true
        ];

        $builder
            ->add('criteria', ChoiceType::class, [
                'choices' => $this->getSearchCriterias(),
                'choice_label' => function($value){ return "trans.{$value}"; },
                'choice_attr' => function(string $value) {
                    $attr = [];
                    switch ($value) {
                        case SearchCriteriaEnum::EQUAL_TO:
                        case SearchCriteriaEnum::GREATER_THAN:
                        case SearchCriteriaEnum::LOWER_THAN:
                            $attr['data-inputs'] = '.value';
                            break;
                        case SearchCriteriaEnum::BETWEEN:
                            $attr['data-inputs'] = '.value,.value2';
                            break;
                    }
                    return $attr;
                }
            ])
            ->add('value', NumberType::class, [
                    'attr' => ['class' => 'value hidden'] + $valueOptions['attr']
                ] + $valueOptions
            )
            ->add('value2', NumberType::class, [
                    'attr' => ['class' => 'value2 hidden'] + $valueOptions['attr']
                ] + $valueOptions)
        ;
    }

    private function getSearchCriterias(): array
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
}