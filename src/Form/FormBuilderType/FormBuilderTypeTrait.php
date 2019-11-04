<?php

namespace App\Form\FormBuilderType;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

trait  FormBuilderTypeTrait
{
    public function unifiedBuildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['widget'] = $options['widget'];
        $view->vars['mode'] = $options['mode'];
    }

    public function unifiedConfigureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired('widget');
        $resolver->setRequired('mode');
    }
}