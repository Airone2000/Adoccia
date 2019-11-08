<?php

namespace App\Form\FormBuilderType;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapType extends HiddenType
{
    use FormBuilderTypeTrait;

    public function getBlockPrefix()
    {
        return 'fichit_map';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->unifiedBuildView($view, $form, $options);
        $view->vars['attr']['class'] = 'value';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->unifiedConfigureOptions($resolver);
    }

}