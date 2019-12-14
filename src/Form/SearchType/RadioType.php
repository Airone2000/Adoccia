<?php

namespace App\Form\SearchType;

use App\Entity\Widget;
use App\Enum\SearchCriteriaEnum;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class RadioType extends AbstractSearchType
{
    public function getBlockPrefix()
    {
        return 'fichit_radio';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $widget = $options['widget'];

        $builder
            ->add('criteria', ChoiceType::class, [
                'choices' => $this->getSearchCriterias($widget),
                'choice_label' => function ($value) {return "trans.{$value}"; },
                'choice_attr' => function ($value) {
                    $attr = [];
                    switch ($value) {
                        case SearchCriteriaEnum::IN_ARRAY:
                        case SearchCriteriaEnum::IN_ARRAY_EXACT:
                        case SearchCriteriaEnum::NOT_IN_ARRAY:
                            $attr['data-inputs'] = '.value';
                            break;
                    }

                    return $attr;
                },
            ])
            ->add('value', ChoiceType::class, [
                'required' => false,
                'choices' => \App\Form\FormBuilderType\RadioType::getChoices($widget),
                'choice_label' => function ($value, $label) { return $label; },
                'multiple' => true,
                'attr' => [
                    'class' => 'value hidden',
                ],
            ])
            ->get('value')->addModelTransformer(new CallbackTransformer(
                function ($value) {
                    if (null !== $value) {
                        $value = json_decode($value, true);
                    }

                    return $value;
                },
                function ($value) {
                    if (null !== $value) {
                        $value = json_encode($value);
                    }

                    return $value;
                }
            ))
        ;
    }

    private function getSearchCriterias(Widget $widget): array
    {
        $criterias = [
            SearchCriteriaEnum::DISABLED,
            SearchCriteriaEnum::IS_NULL,
            SearchCriteriaEnum::IS_NOT_NULL,
            SearchCriteriaEnum::IN_ARRAY,
            SearchCriteriaEnum::NOT_IN_ARRAY,
        ];

        if ($widget->hasMultipleValues()) {
            // Just after IN_ARRAY
            array_splice($criterias, 4, 0, SearchCriteriaEnum::IN_ARRAY_EXACT);
        }

        return $criterias;
    }
}
