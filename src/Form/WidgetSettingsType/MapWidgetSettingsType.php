<?php

namespace App\Form\WidgetSettingsType;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class MapWidgetSettingsType extends AbstractWidgetSettingsType
{

    public function buildInModalForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('minMarkers', IntegerType::class, [
                'attr' => [
                    'min' => 0
                ],
                'required' => false
            ])
            ->add('maxMarkers', IntegerType::class, [
                'required' => false
            ])
        ;

        $builder
            ->get('minMarkers')->addModelTransformer(new CallbackTransformer(
                function($value){return $value;},
                function($value){
                    if ($value == 0) {return null;}
                    return $value;
                }
            ))
        ;

        $builder
            ->get('maxMarkers')->addModelTransformer(new CallbackTransformer(
                function($value){return $value;},
                function($value){
                    if ($value == 0) {return null;}
                    return $value;
                }
            ))
        ;
    }
}