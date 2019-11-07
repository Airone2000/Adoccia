<?php

namespace App\Form\FormBuilderType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapType extends AbstractType
{
    use FormBuilderTypeTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

    public function getBlockPrefix()
    {
        return 'fichit_map';
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