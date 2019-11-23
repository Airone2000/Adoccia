<?php

namespace App\Form\FormBuilderType;

use App\Form\AdvancedPictureType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PictureType extends AdvancedPictureType
{
    use FormBuilderTypeTrait;

    public function getBlockPrefix()
    {
        return 'fichit_picture';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->unifiedBuildView($view, $form, $options);
        $view->vars['aspectRatio'] = $options['aspectRatio'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->unifiedConfigureOptions($resolver);
        $resolver
            ->setDefault('aspectRatio', null)
        ;
    }
}