<?php

namespace App\Form\FormBuilder;

use App\Entity\Category;
use App\Entity\Fiche;
use App\Entity\User;
use App\Entity\Widget;
use App\Form\FormBuilderType\FichecreatorType;
use Symfony\Component\Form\CallbackTransformer;

final class FichecreatorBuilder implements FormBuilderInterface
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        /* @var Fiche $fiche */
        $fiche = $options['fiche'];

        $builder
            ->add($widget->getId(), FichecreatorType::class, [
                'mode' => $options['mode'],
                'widget' => $widget,
            ])
            ->get($widget->getId())->addModelTransformer(new CallbackTransformer(
                function () use ($fiche) {
                    if (($creator = $fiche->getCreator()) instanceof User) {
                        return $creator->getUsername();
                    }

                    return null;
                },
                function () {}
            ))
        ;
    }

    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Category $category */
        $category = $options['category'];

        /* @var \App\Entity\Widget $widget */
        $widget = $options['widget'];
        $builder->add($widget->getImmutableId(), \App\Form\SearchType\FichecreatorType::class, [
            'widget' => $widget,
            'category' => $category,
        ]);
    }
}
