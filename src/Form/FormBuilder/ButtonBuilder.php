<?php

namespace App\Form\FormBuilder;

use App\Entity\Widget;
use App\Form\FormBuilderType\ButtonType;

final class ButtonBuilder implements FormBuilderInterface
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $builder
            ->add($widget->getId(), ButtonType::class, [
                'widget' => $widget,
                'mode' => $options['mode'],
            ])
        ;
    }

    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var \App\Entity\Widget $widget */
        $widget = $options['widget'];
        $builder->add($widget->getImmutableId(), \App\Form\SearchType\ButtonType::class, [
            'widget' => $widget,
        ]);
    }
}
