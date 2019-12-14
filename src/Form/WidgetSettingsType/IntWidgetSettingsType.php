<?php

namespace App\Form\WidgetSettingsType;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class IntWidgetSettingsType extends AbstractWidgetSettingsType
{
    public function buildInModalForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('min', IntegerType::class, ['required' => false])
            ->add('max', IntegerType::class, ['required' => false])
            ->add('required')
            ->add('inputPlaceholder')
        ;
    }
}
