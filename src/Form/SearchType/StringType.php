<?php

namespace App\Form\SearchType;

use App\Enum\SearchCriteriaEnum;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class StringType extends AbstractSearchType
{
    public function getBlockPrefix()
    {
        return 'fichit_string';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var \App\Entity\Widget $widget */
        $widget = $options['widget'];

        $builder
            ->add('criteria', ChoiceType::class, [
                'choices' => $this->getSearchCriterias(),
                'choice_label' => function ($value) { return "trans.{$value}"; },
                'choice_attr' => function (string $value) {
                    $attr = [];
                    switch ($value) {
                        case SearchCriteriaEnum::EXACT:
                        case SearchCriteriaEnum::CONTAINS:
                        case SearchCriteriaEnum::STARTS_WITH:
                        case SearchCriteriaEnum::ENDS_WITH:
                            $attr['data-inputs'] = '.value';
                            break;
                    }

                    return $attr;
                },
            ])
            ->add('value', TextType::class, [
                'required' => false,
                'constraints' => [new Length(['max' => 250])],
                'attr' => [
                    'placeholder' => $widget->getInputPlaceholder(),
                    'class' => 'value hidden',
                ],
            ])
        ;
    }

    private function getSearchCriterias(): array
    {
        return [
            SearchCriteriaEnum::DISABLED,
            SearchCriteriaEnum::IS_NULL,
            SearchCriteriaEnum::IS_NOT_NULL,
            SearchCriteriaEnum::EXACT,
            SearchCriteriaEnum::CONTAINS,
            SearchCriteriaEnum::STARTS_WITH,
            SearchCriteriaEnum::ENDS_WITH,
        ];
    }
}
