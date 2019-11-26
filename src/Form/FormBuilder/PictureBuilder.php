<?php

namespace App\Form\FormBuilder;

use App\Entity\Picture;
use App\Entity\Widget;
use App\Form\FormBuilderType\PictureType;
use Symfony\Component\Validator\Constraints\NotNull;

final class PictureBuilder implements FormBuilderInterface
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        /* @var Picture|null $rawValue */
        $rawValue = $options['widgetValue'] ?? null;

        $builder
            ->add($widget->getId(), PictureType::class, [
                'widget' => $widget,
                'mode' => $options['mode'],
                'originalPicture' => $rawValue,
                'uniqueId' => uniqid('uid_'),
                'constraints' => $this->getConstraints($widget),
                'deletable' => !$widget->isRequired()
            ])
        ;
    }

    private function getConstraints(Widget $widget): array
    {
        $constraints = [];

        if ($widget->isRequired()) {
            $constraints[] = new NotNull();
        }

        return $constraints;
    }

    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var \App\Entity\Widget $widget */
        $widget = $options['widget'];
        $builder->add($widget->getImmutableId(), \App\Form\SearchType\PictureType::class, [
            'widget' => $widget
        ]);
    }
}