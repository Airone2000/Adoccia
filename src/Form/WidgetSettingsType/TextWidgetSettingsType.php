<?php

namespace App\Form\WidgetSettingsType;

use Symfony\Component\Form\FormBuilderInterface;

final class TextWidgetSettingsType extends AbstractWidgetSettingsType
{

    public function buildInModalForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('minLength')
            ->add('maxLength')
            ->add('required')
            ->add('inputPlaceholder')
        ;
    }
}