<?php

namespace App\Form\WidgetSettingsType;

use Symfony\Component\Form\FormBuilderInterface;

final class EmailWidgetSettingsType extends AbstractWidgetSettingsType
{

    public function buildInModalForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('required')
            ->add('inputPlaceholder')
        ;
    }
}