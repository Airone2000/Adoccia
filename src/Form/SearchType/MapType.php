<?php

namespace App\Form\SearchType;

use App\Enum\SearchCriteriaEnum;
use App\Form\SearchType\SubTypes\MapAroundType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class MapType extends AbstractSearchType
{
    public function getBlockPrefix()
    {
        return 'fichit_map';
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
                        case SearchCriteriaEnum::MAP_AROUND:
                            $attr['data-inputs'] = '.mapAround';
                            break;
                    }

                    return $attr;
                },
            ])
            ->add('mapAround', MapAroundType::class, [
                'attr' => ['class' => 'sinput hidden mapAround'],
            ])
        ;
    }

    private function getSearchCriterias(): array
    {
        return [
            SearchCriteriaEnum::DISABLED,
            SearchCriteriaEnum::MAP_AROUND,
        ];
    }
}
