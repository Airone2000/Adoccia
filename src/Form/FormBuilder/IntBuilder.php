<?php

namespace App\Form\FormBuilder;

use App\Entity\Widget;
use App\Enum\FicheModeEnum;
use App\Form\FormBuilderType\IntType;

final class IntBuilder implements FormBuilderInterface
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $builder->add($widget->getId(), IntType::class, [
            'widget' => $widget,
            'mode' => $options['mode'],
            'empty_data' => null,
            'attr' => [
                'min' => $widget->getMin(),
                'max' => $widget->getMax(),
                'required' => $widget->isRequired()
            ]
        ]);
    }

    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var \App\Entity\Widget $widget */
        $widget = $options['widget'];
        $builder->add($widget->getImmutableId(), \App\Form\SearchType\IntType::class, [
            'widget' => $widget
        ]);
    }
}