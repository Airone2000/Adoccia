<?php

namespace App\Form\FormBuilder;

use App\Entity\Widget;
use App\Form\FormBuilderType\DateType;
use App\Form\FormBuilderType\StringType;
use App\Form\SearchType\DateType as DateTypeSearch;
use Symfony\Component\Form\CallbackTransformer;

final class DateBuilder implements FormBuilderInterface
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $builder->add($widget->getId(), StringType::class, [
            'widget' => $widget,
            'mode' => $options['mode'],
            'attr' => DateType::getHTMLInputAttributes($widget) + [
                'required' => $widget->isRequired()
            ],
            'empty_data' => null
        ]);

        $builder->get($widget->getId())->addModelTransformer(new CallbackTransformer(
            function($value) use ($widget){
                return DateType::transformTo($widget, $value);
            },
            function($value) use ($widget){
                return DateType::transformFrom($widget, $value);
            }
        ));
    }

    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var \App\Entity\Widget $widget */
        $widget = $options['widget'];
        $builder->add($widget->getImmutableId(), DateTypeSearch::class, [
            'widget' => $widget
        ]);
    }
}