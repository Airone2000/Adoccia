<?php

namespace App\Form\FormBuilder;

use App\Entity\Widget;
use App\Form\FormBuilderType\StringType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class StringBuilder implements FormBuilderInterface
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $builder->add($widget->getId(), $this->getBuilderTypeClass(), [
            'widget' => $widget,
            'mode' => $options['mode'],
            'empty_data' => null,
            'attr' => [
                'minLength' => $widget->getMinLength(),
                'maxLength' => $widget->getMaxLength(),
                'required' => $widget->isRequired(),
            ],
            'constraints' => $this->getConstraints($widget),
        ]);
    }

    protected function getBuilderTypeClass()
    {
        return StringType::class;
    }

    protected function getConstraints(Widget $widget): array
    {
        $constraints = [];

        if (null !== $widget->getMinLength()) {
            $constraints[] = new Length(['min' => $widget->getMinLength()]);
        }

        if (null !== $widget->getMaxLength()) {
            $constraints[] = new Length(['max' => $widget->getMaxLength()]);
        }

        if ($widget->isRequired()) {
            $constraints[] = new NotBlank(['allowNull' => true]);
        }

        return $constraints;
    }

    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var \App\Entity\Widget $widget */
        $widget = $options['widget'];
        $builder->add($widget->getImmutableId(), \App\Form\SearchType\StringType::class, [
            'widget' => $widget,
        ]);
    }
}
