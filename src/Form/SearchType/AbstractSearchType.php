<?php

namespace App\Form\SearchType;

use App\Entity\Category;
use App\Enum\FicheModeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbstractSearchType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired(['widget']);
        $resolver->setDefault('compound', true);
        $resolver->setDefault('mode', FicheModeEnum::SEARCH);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['widget'] = $options['widget'];
        $view->vars['mode'] = $options['mode'];
    }
}