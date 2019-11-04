<?php

namespace App\Form\FormBuilder;

use App\Entity\Widget;
use App\Form\FormBuilderType\RadioType;

final class RadioBuilder implements FormBuilderInterface
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $builder->add($widget->getId(), RadioType::class, [
            'choices' => $this->getChoices($widget),
            'widget' => $widget,
            'mode' => $options['mode'],
            'empty_data' => null,
            'multiple' => false,
            'attr' => [
                'required' => $widget->isRequired()
            ]
        ]);
    }

    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        // TODO: Implement buildSearchForm() method.
    }

    private function getChoices(Widget $widget): array
    {
        $choicesKey = array_map(function($value){
            return hash('sha256', (string)$value);
        }, $widget->getChoices());

        $choices = array_combine($widget->getChoices(), $choicesKey) ?? [];
        return $choices;
    }
}