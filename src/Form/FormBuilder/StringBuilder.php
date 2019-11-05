<?php

namespace App\Form\FormBuilder;

use App\Entity\Widget;
use App\Enum\FicheModeEnum;
use App\Form\FormBuilderType\StringType;

final class StringBuilder implements FormBuilderInterface
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $builder->add($widget->getId(), StringType::class, [
            'widget' => $widget,
            'mode' => $options['mode'],
            'empty_data' => null,
            'attr' => [
                'minLength' => $widget->getMinLength(),
                'maxLength' => $widget->getMaxLength(),
                'required' => $widget->isRequired()
            ]
        ]);
    }

    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var \App\Entity\Widget $widget */
        $widget = $options['widget'];
        $builder->add($widget->getImmutableId(), \App\Form\SearchType\StringType::class, [
            'widget' => $widget
        ]);
    }
}