<?php

namespace App\Form\FormBuilderType;

use App\Entity\Widget;
use App\Enum\DateFormatEnum;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateType extends TextType
{
    use FormBuilderTypeTrait;

    public function getBlockPrefix()
    {
        return 'fichit_date';
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
        $attr['data-inputmask-inputformat'] = $widget->getDateFormat();
        $attr['data-inputmask-placeholder'] = self::getDateTypePlaceholder($widget);
        $attr['inputmode'] = 'numeric';
        return $attr;
    }

    public static function getDateTypePlaceholder(Widget $widget): ?string
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
        if (is_string($value) && preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $value)) {
            // Let's assume it can be a value like Y-m-d (default value)
            $value = \DateTime::createFromFormat('Y-m-d', $value);
        }

        if ($value instanceof \DateTime) {
            $dateFormat = DateFormatEnum::getPHPFormatForJsFormat($widget->getDateFormat());
            return $value->format($dateFormat);
        }
        return null;
    }
}