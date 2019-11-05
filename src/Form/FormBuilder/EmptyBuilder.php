<?php

namespace App\Form\FormBuilder;

use App\Entity\Widget;
use App\Form\FormBuilderType\EmptyType;

final class EmptyBuilder implements FormBuilderInterface
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $builder->add($widget->getId(), EmptyType::class, [
            'widget' => $widget,
            'mode' => $options['mode'],
            'empty_data' => null
        ]);
    }

    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $this->buildForm($builder, $options);
    }
}