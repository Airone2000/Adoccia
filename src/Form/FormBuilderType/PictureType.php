<?php

namespace App\Form\FormBuilderType;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PictureType extends \App\Form\PictureType
{
    use FormBuilderTypeTrait;

    public function getBlockPrefix()
    {
        return 'fichit_picture';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->unifiedBuildView($view, $form, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->unifiedConfigureOptions($resolver);
    }

}