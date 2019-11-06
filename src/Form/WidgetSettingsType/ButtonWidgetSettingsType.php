<?php

namespace App\Form\WidgetSettingsType;

use Symfony\Component\Form\FormBuilderInterface;

final class ButtonWidgetSettingsType extends AbstractWidgetSettingsType
{

    protected function buildInModalForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('required')
        ;
    }
}