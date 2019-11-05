<?php

namespace App\Form\FormBuilder;

use App\Entity\Widget;
use App\Form\SearchType\RadioType;
use Symfony\Component\Form\CallbackTransformer;

final class RadioBuilder implements FormBuilderInterface
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $builder->add($widget->getId(), \App\Form\FormBuilderType\RadioType::class, [
            'choices' => \App\Form\FormBuilderType\RadioType::getChoices($widget),
            'widget' => $widget,
            'mode' => $options['mode'],
            'empty_data' => null,
            'multiple' => $widget->hasMultipleValues(),
            'attr' => [
                'required' => $widget->isRequired()
            ]
        ]);

        if ($widget->hasMultipleValues()) {
            $builder->get($widget->getId())->addModelTransformer(new CallbackTransformer(
                function($value){ return explode(',', $value); },
                function($value){ return $value; }
            ));
        }
    }

    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var \App\Entity\Widget $widget */
        $widget = $options['widget'];
        $builder->add($widget->getImmutableId(), RadioType::class, [
            'widget' => $widget
        ]);
    }
}