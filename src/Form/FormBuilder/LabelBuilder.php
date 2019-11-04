<?php

namespace App\Form\FormBuilder;

use App\Entity\Widget;
use App\Enum\FicheModeEnum;
use App\Form\FormBuilderType\LabelType;

final class LabelBuilder implements FormBuilderInterface
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];
        $name = $options['mode'] === FicheModeEnum::SEARCH ? $widget->getImmutableId() : $widget->getId();
        $builder->add($name, LabelType::class, [
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