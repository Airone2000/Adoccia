<?php

namespace App\Form\WidgetSettingsType;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;

final class FloatWidgetSettingsType extends AbstractWidgetSettingsType
{

    public function buildInModalForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('min', IntegerType::class, ['required' => false])
            ->add('max', IntegerType::class, ['required' => false])
            ->add('decimalCount', IntegerType::class, [
                'required' => false
            ])
            ->add('required')
            ->add('inputPlaceholder')
        ;
    }
}