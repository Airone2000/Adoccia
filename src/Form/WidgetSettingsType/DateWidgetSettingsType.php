<?php

namespace App\Form\WidgetSettingsType;

use App\Enum\DateFormatEnum;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class DateWidgetSettingsType extends AbstractWidgetSettingsType
{

    public function buildInModalForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('required')
            ->add('inputPlaceholder')
            ->add('dateFormat', ChoiceType::class, [
                'choices' => DateFormatEnum::toArray(),
                'choice_label' => function(string $value) {
                    return "trans.{$value}";
                }
            ])
        ;
    }
}