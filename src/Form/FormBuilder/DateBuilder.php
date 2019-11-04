<?php

namespace App\Form\FormBuilder;

use App\Entity\Widget;
use App\Enum\DateFormatEnum;
use App\Form\FormBuilderType\StringType;
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
            'attr' => $this->getWidgetAttributes($widget) + [
                'required' => $widget->isRequired()
            ],
            'empty_data' => null
        ]);

        $builder->get($widget->getId())->addModelTransformer(new CallbackTransformer(
            function($value) use ($widget){
                return self::transformTo($widget, $value);
            },
            function($value) use ($widget){
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
        $attr['data-inputmask-inputformat'] = $widget->getDateFormat();
        $attr['data-inputmask-placeholder'] = $this->getDateTypePlaceholder($widget);
        $attr['inputmode'] = 'numeric';
        return $attr;
    }

    private function getDateTypePlaceholder(Widget $widget): ?string
    {
        if ($widget->getInputPlaceholder()) {
            $placeholder = $widget->getInputPlaceholder();
        }
        else {
            $placeholder = preg_replace('/[dmy]/i', '_', $widget->getDateFormat());
        }

        return $placeholder;
    }

    public static function transformFrom(Widget $widget, $value)
    {
        if (is_string($value)) {
            $dateFormat = DateFormatEnum::getPHPFormatForJsFormat($widget->getDateFormat());
            $datetime = \DateTime::createFromFormat($dateFormat, $value);
            if ($datetime !== false) {
                $datetime->setTime(0, 0, 0, 0);
                return $datetime;
            }
        }
        return null;
    }

    public static function transformTo(Widget $widget, $value)
    {
        if ($value instanceof \DateTime) {
            $dateFormat = DateFormatEnum::getPHPFormatForJsFormat($widget->getDateFormat());
            return $value->format($dateFormat);
        }
        return null;
    }
}