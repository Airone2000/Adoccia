<?php

namespace App\Form\SearchType;

use App\Enum\SearchCriteriaEnum;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

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
                'choice_label' => function($value){ return "trans.{$value}"; },
                'choice_attr' => function(string $value) {
                    $attr = [];
                    switch ($value) {
                        case SearchCriteriaEnum::MAP_AROUND:
                        case SearchCriteriaEnum::MAP_LABEL_CONTAINS:
                            $attr['data-inputs'] = '.distance,.unit';
                            break;
                    }
                    return $attr;
                }
            ])
            ->add('distance', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'distance hidden',
                    'min' => 0
                ]
            ])
            ->add('unit', ChoiceType::class, [
                'choices' => ['km' => 'km', 'm' => 'm'],
                'attr' => [
                    'class' => 'unit hidden'
                ]
            ])
        ;
    }

    private function getSearchCriterias(): array
    {
        return [
            SearchCriteriaEnum::DISABLED,
            SearchCriteriaEnum::MAP_AROUND,
            SearchCriteriaEnum::MAP_LABEL_CONTAINS
        ];
    }
}