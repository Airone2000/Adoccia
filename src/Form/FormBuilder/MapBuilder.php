<?php

namespace App\Form\FormBuilder;

use App\Entity\Value;
use App\Entity\Widget;
use App\Form\FormBuilderType\MapType;
use Symfony\Component\Form\CallbackTransformer;

final class MapBuilder implements FormBuilderInterface
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $builder
            ->add($widget->getId(), MapType::class, [
                'mode' =>$options['mode'],
                'widget' => $widget
            ])
            ->get($widget->getId())->addModelTransformer(new CallbackTransformer(
                function($value){
                    if (is_array($value)) {
                        return json_encode($value);
                    }
                    return json_encode(Value::DEFAULT_VALUE_OF_TYPE_MAP);
                },
                function($value){
                    if (is_string($value)) {
                        $value = json_decode($value, true) ?? null;
                        return $value;
                    }
                    return null;
                }
            ))
        ;

    }

    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];
        $builder
            ->add($widget->getImmutableId(), \App\Form\SearchType\MapType::class, [
                'widget' => $widget
            ])
        ;
    }
}