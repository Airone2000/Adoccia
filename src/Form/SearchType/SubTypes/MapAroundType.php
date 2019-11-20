<?php

namespace App\Form\SearchType\SubTypes;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class MapAroundType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $uniqidId = uniqid();

        $builder
            ->add('distance', IntegerType::class, [
                'attr' => [
                    'min' => 0
                ],
                'constraints' => [
                    new GreaterThanOrEqual(['value' => 0])
                ],
                'required' => false
            ])
            ->add('unit', ChoiceType::class, [
                'choices' => ['km' => 'km', 'm' => 'm']
            ])
            ->add('location', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => "location_{$uniqidId} mapSearchLocationInput"
                ],
                'data' => null
            ])
            ->add('selectedLocation', TextType::class, [
                'attr' => [
                    'readonly' => true,
                    'class' => "selectedLocation_{$uniqidId} mapSearchSelectedLocation",
                    'style' => 'border:0;outline:0;'
                ],
                'label' => 'Votre choix : '
            ])
            ->add('findMe', ButtonType::class, [
                'attr' => [
                    'class' => 'findMe',
                    'data-uniqid' => $uniqidId
                ]
            ])
            ->add('latlng', HiddenType::class, [
                'attr' => [
                    'class' => "latlng_{$uniqidId} mapSearchLatLngInput"
                ]
            ])
        ;
    }
}