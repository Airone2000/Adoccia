<?php

namespace App\Form\SearchType;

use App\Enum\SearchCriteriaEnum;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

final class ButtonType extends AbstractSearchType
{
    public function getBlockPrefix()
    {
        return 'fichit_button';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var \App\Entity\Widget $widget */
        $widget = $options['widget'];

        $builder
            ->add('criteria', ChoiceType::class, [
                'choices' => $this->getSearchCriterias(),
                'choice_label' => function($value){ return "trans.{$value}"; },
                'choice_attr' => function(string $value) {
                    $attr = [];
                    switch ($value) {
                        case SearchCriteriaEnum::BUTTON_LABEL_EQUAL_TO:
                        case SearchCriteriaEnum::BUTTON_LABEL_NOT_EQUAL_TO:
                        case SearchCriteriaEnum::BUTTON_LABEL_CONTAINS:
                        case SearchCriteriaEnum::BUTTON_LABEL_NOT_CONTAINS:
                        case SearchCriteriaEnum::BUTTON_TARGET_EQUAL_TO:
                        case SearchCriteriaEnum::BUTTON_TARGET_NOT_EQUAL_TO:
                        case SearchCriteriaEnum::BUTTON_TARGET_CONTAINS:
                        case SearchCriteriaEnum::BUTTON_TARGET_NOT_CONTAINS:
                            $attr['data-inputs'] = '.value';
                            break;
                    }
                    return $attr;
                }
            ])
            ->add('value', TextType::class, [
                'required' => false,
                'constraints' => [new Length(['max' => 250])],
                'attr' => [
                    'placeholder' => $widget->getInputPlaceholder(),
                    'class' => 'value hidden'
                ]
            ])
        ;
    }

    private function getSearchCriterias(): array
    {
        return [
            SearchCriteriaEnum::BUTTON_LABEL_EQUAL_TO,
            SearchCriteriaEnum::BUTTON_LABEL_NOT_EQUAL_TO,
            SearchCriteriaEnum::BUTTON_LABEL_CONTAINS,
            SearchCriteriaEnum::BUTTON_LABEL_NOT_CONTAINS,
            SearchCriteriaEnum::BUTTON_TARGET_EQUAL_TO,
            SearchCriteriaEnum::BUTTON_TARGET_NOT_EQUAL_TO,
            SearchCriteriaEnum::BUTTON_TARGET_CONTAINS,
            SearchCriteriaEnum::BUTTON_TARGET_NOT_CONTAINS
        ];
    }
}