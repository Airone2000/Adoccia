<?php

namespace App\Form\WidgetType;

use App\Entity\Widget;
use App\Enum\FicheModeEnum;
use App\Enum\SearchCriteriaEnum;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType as SfTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class TextType extends AbstractWidgetType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['mode'] === FicheModeEnum::SEARCH) {
            $this->buildSearchForm($builder, $options);
        }
    }

    public function getBlockPrefix()
    {
        return 'fichit_text';
    }

    public function getParent()
    {
        return TextareaType::class;
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
            ->add('value', SfTextType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['max' => 250])
                ],
                'attr' => [
                    'placeholder' => $widget->getInputPlaceholder()
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
            SearchCriteriaEnum::ENDS_WITH
        ];
    }
}