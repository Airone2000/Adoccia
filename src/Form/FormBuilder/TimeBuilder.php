<?php

namespace App\Form\FormBuilder;

use App\Entity\Widget;
use App\Enum\FicheModeEnum;
use App\Enum\TimeFormatEnum;
use App\Form\FormBuilderType\TimeType;
use App\Form\SearchType\TimeType as TimeTypeSearch;
use Symfony\Component\Form\CallbackTransformer;

final class TimeBuilder implements FormBuilderInterface
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $builder->add($widget->getId(), TimeType::class, [
            'widget' => $widget,
            'mode' => $options['mode'],
            'attr' => TimeType::getHTMLInputAttributes($widget) + [
                'required' => $widget->isRequired()
            ],
            'empty_data' => null
        ]);

        $builder->get($widget->getId())->addModelTransformer(new CallbackTransformer(
            function($value) use ($widget) {
                return TimeType::transformTo($widget, $value);
            },
            function($value) use ($widget) {
                return TimeType::transformFrom($widget, $value);
            }
        ));
    }

    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var \App\Entity\Widget $widget */
        $widget = $options['widget'];
        $builder->add($widget->getImmutableId(), TimeTypeSearch::class, [
            'widget' => $widget
        ]);
    }

}