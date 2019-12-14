<?php

namespace App\Form\WidgetSettingsType;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

final class RadioWidgetSettingsType extends AbstractWidgetSettingsType
{
    public function buildInModalForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('choices', TextareaType::class, [
                'attr' => [
                    'placeholder' => "Choix 1\nChoix 2\n...",
                ],
                'required' => false,
            ])
            ->add('required')
            ->add('multipleValues')

            ->get('choices')->addModelTransformer(new CallbackTransformer(
                function ($value) {
                    if (\is_array($value)) {
                        $value = implode("\n", $value);
                    }

                    return $value;
                },
                function ($value) {
                    if (\is_string($value)) {
                        $value = explode("\n", $value);
                        $value = array_map('trim', $value);
                    }

                    return $value;
                }
            ))
        ;
    }
}
