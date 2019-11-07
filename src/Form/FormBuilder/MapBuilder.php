<?php

namespace App\Form\FormBuilder;

use App\Entity\Widget;
use App\Form\FormBuilderType\MapType;

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