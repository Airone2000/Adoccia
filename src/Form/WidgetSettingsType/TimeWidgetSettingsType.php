<?php

namespace App\Form\WidgetSettingsType;

use App\Enum\DateFormatEnum;
use App\Enum\TimeFormatEnum;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class TimeWidgetSettingsType extends AbstractWidgetSettingsType
{

    public function buildInModalForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('required')
            ->add('inputPlaceholder')
            ->add('timeFormat', ChoiceType::class, [
                'choices' => TimeFormatEnum::toArray(),
                'choice_label' => function(string $value) {
                    return "trans.{$value}";
                }
            ])
        ;
    }
}