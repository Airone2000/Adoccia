<?php

namespace App\Form\WidgetSettingsType;

use Symfony\Component\Form\FormBuilderInterface;

final class StringWidgetSettingsType extends AbstractWidgetSettingsType
{

    public function buildInModalForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('minLengthSetting')
            ->add('maxLengthSetting')
            ->add('requiredSetting')
        ;
    }
}