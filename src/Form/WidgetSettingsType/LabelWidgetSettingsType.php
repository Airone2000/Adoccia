<?php

namespace App\Form\WidgetSettingsType;

use App\Enum\TextAlignPositionEnum;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

final class LabelWidgetSettingsType extends AbstractWidgetSettingsType
{

    protected function buildInModalForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('textAlign', ChoiceType::class, [
                'choices' => TextAlignPositionEnum::toArray(),
                'choice_label' => function(string $value){
                    return "translate.{$value}";
                }
            ])
        ;
    }

    /**
     * Part of form outside of the modal / at the formArea level
     * @inheritdoc
     */
    protected function buildOffModalForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('innerText', TextareaType::class)
        ;
    }
}