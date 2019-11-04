<?php

namespace App\Form\FormBuilderType;

use App\Enum\FicheModeEnum;
use App\Enum\SearchCriteriaEnum;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

final class StringType extends TextType
{
    use FormBuilderTypeTrait;

    public function getBlockPrefix()
    {
        return 'fichit_string';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['mode'] === FicheModeEnum::SEARCH) {
            $this->buildSearchForm($builder, $options);
        }
    }

    private function buildSearchForm(FormBuilderInterface $builder, array $options)
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
                        case SearchCriteriaEnum::EXACT:
                        case SearchCriteriaEnum::CONTAINS:
                        case SearchCriteriaEnum::STARTS_WITH:
                        case SearchCriteriaEnum::ENDS_WITH:
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

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->unifiedBuildView($view, $form, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->unifiedConfigureOptions($resolver);
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
            SearchCriteriaEnum::ENDS_WITH
        ];
    }
}