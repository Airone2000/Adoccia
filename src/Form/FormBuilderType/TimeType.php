<?php

namespace App\Form\FormBuilderType;

use App\Entity\Widget;
use App\Enum\TimeFormatEnum;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TimeType extends TextType
{
    use FormBuilderTypeTrait;

    public function getBlockPrefix()
    {
        return 'fichit_time';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->unifiedBuildView($view, $form, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->unifiedConfigureOptions($resolver);
    }

    public static function getHTMLInputAttributes(Widget $widget, array $attr = []): array
    {
        $attr['data-masked'] = 'true';
        $attr['data-inputmask-alias'] = 'datetime';
        $attr['data-inputmask-inputformat'] = $widget->getTimeFormat();
        $attr['data-inputmask-placeholder'] = self::getTimeTypePlaceholder($widget);
        $attr['inputmode'] = 'numeric';

        return $attr;
    }

    public static function getTimeTypePlaceholder(Widget $widget): ?string
    {
        if ($widget->getInputPlaceholder()) {
            $placeholder = $widget->getInputPlaceholder();
        } else {
            $placeholder = preg_replace('/[hms]/i', '_', $widget->getTimeFormat());
        }

        return $placeholder;
    }

    public static function transformFrom(Widget $widget, $value)
    {
        if (\is_string($value)) {
            $timeFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['php'];
            $datetime = \DateTime::createFromFormat($timeFormat, $value);
            if (false !== $datetime) {
                $datetime->setDate(0, 0, 0);

                return $datetime;
            }
        }

        return null;
    }

    public static function transformTo(Widget $widget, $value)
    {
        if (\is_string($value)) {
            $value = \DateTime::createFromFormat('H:i:s', $value);
            $value->setDate(0, 0, 0);
        }

        if ($value instanceof \DateTime) {
            $timeFormat = TimeFormatEnum::$mapJsDateFormatToOtherDateFormat[$widget->getTimeFormat()]['php'];

            return $value->format($timeFormat);
        }

        return null;
    }
}
