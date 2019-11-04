<?php

namespace App\Form\FormBuilder;

use App\Entity\Widget;
use App\Enum\TimeFormatEnum;
use App\Form\FormBuilderType\TimeType;
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
            'attr' => $this->getWidgetAttributes($widget) + [
                'required' => $widget->isRequired()
            ],
            'empty_data' => null
        ]);

        $builder->get($widget->getId())->addModelTransformer(new CallbackTransformer(
            function($value) use ($widget) {
                return self::transformTo($widget, $value);
            },
            function($value) use ($widget) {
                return self::transformFrom($widget, $value);
            }
        ));
    }

    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        // TODO: Implement buildSearchForm() method.
    }

    private function getWidgetAttributes(Widget $widget): array
    {
        $attr = [];
        $attr['data-masked'] = 'true';
        $attr['data-inputmask-alias'] = 'datetime';
        $attr['data-inputmask-inputformat'] = $widget->getTimeFormat();
        $attr['data-inputmask-placeholder'] = $this->getTimeTypePlaceholder($widget);
        $attr['inputmode'] = 'numeric';
        return $attr;
    }

    private function getTimeTypePlaceholder(Widget $widget): ?string
    {
        if ($widget->getInputPlaceholder()) {
            $placeholder = $widget->getInputPlaceholder();
        }
        else {
            $placeholder = preg_replace('/[hms]/i', '_', $widget->getTimeFormat());
        }

        return $placeholder;
    }

    public static function transformFrom(Widget $widget, $value)
    {
        if (is_string($value)) {
            $timeFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['php'];
            $datetime = \DateTime::createFromFormat($timeFormat, $value);
            if ($datetime !== false) {
                $datetime->setDate(0,0,0);
                return $datetime;
            }
        }
        return null;
    }

    public static function transformTo(Widget $widget, $value)
    {
        if ($value instanceof \DateTime) {
            $timeFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['php'];
            return $value->format($timeFormat);
        }
        return null;
    }
}