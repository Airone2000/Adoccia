<?php

namespace App\Form\WidgetType;

use App\Enum\FicheModeEnum;
use App\Enum\SearchCriteriaEnum;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;

class IntType extends AbstractWidgetType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['mode'] === FicheModeEnum::SEARCH) {
            $this->buildSearchForm($builder, $options);
        }
    }

    public function getBlockPrefix()
    {
        return 'fichit_int';
    }

    private function buildSearchForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('criteria', ChoiceType::class, [
                'choices' => $this->getSearchCriterias(),
                'choice_label' => function(string $label) {
                    return $label;
                }
            ])
            ->add('value', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new Type(['type' => 'int'])
                ]
            ])
        ;
    }

    protected function getSearchCriterias(): array
    {
        return [
            SearchCriteriaEnum::DISABLED,
            SearchCriteriaEnum::IS_NULL,
            SearchCriteriaEnum::IS_NOT_NULL,
            SearchCriteriaEnum::EXACT,
            SearchCriteriaEnum::CONTAINS,
            SearchCriteriaEnum::STARTS_WITH,
            SearchCriteriaEnum::ENDS_WITH,
            SearchCriteriaEnum::GREATER_THAN,
            SearchCriteriaEnum::LOWER_THAN
        ];
    }
}