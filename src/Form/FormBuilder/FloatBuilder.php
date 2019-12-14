<?php

namespace App\Form\FormBuilder;

use App\Entity\Widget;
use App\Form\FormBuilderType\FloatType;
use Symfony\Component\Form\CallbackTransformer;

final class FloatBuilder implements FormBuilderInterface
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $builder->add($widget->getId(), FloatType::class, [
            'widget' => $widget,
            'mode' => $options['mode'],
            'empty_data' => null,
            'attr' => [
                'min' => $widget->getMin(),
                'max' => $widget->getMax(),
                'required' => $widget->isRequired(),
            ],
            'html5' => true,
        ]);

        $builder->get($widget->getId())->addModelTransformer(new CallbackTransformer(
            function ($value) use ($widget) {return $this->transformToFloat($widget, $value); },
            function ($value) use ($widget) {return $this->transformToFloat($widget, $value); }
        ));
    }

    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var \App\Entity\Widget $widget */
        $widget = $options['widget'];
        $builder->add($widget->getImmutableId(), \App\Form\SearchType\FloatType::class, [
            'widget' => $widget,
        ]);
    }

    private function transformToFloat(Widget $widget, $value)
    {
        if (null !== $value) {
            $decimalCount = $widget->getDecimalCount();
            $value = (float) number_format($value, $decimalCount, '.', '');
        }

        return $value;
    }
}
