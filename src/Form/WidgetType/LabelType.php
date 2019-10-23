<?php

namespace App\Form\WidgetType;

use Proxies\__CG__\App\Entity\Widget;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LabelType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('widget')
            ->setAllowedTypes('widget', Widget::class)
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        /** @var \App\Entity\Widget $widget */
        $widget = $options['widget'];
        $view->vars['widget'] = $widget;
    }

    public function getBlockPrefix()
    {
        return 'fichit_label';
    }
}