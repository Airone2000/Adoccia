<?php

namespace App\Form\WidgetSettingsType;

use App\Entity\Widget;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbstractWidgetSettingsType extends AbstractType
{
    const
        # When we click the button "Configure this widget", a modal is opened
        MODE_IN_MODAL = 'in_modal',

        # Accessible directly from the area view in the form builder
        MODE_OFF_MODAL = 'off_modal',

        # Both in and off (see above)
        MODE_COMPLETE = 'complete'
    ;

    final public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (in_array($options['mode'], [self::MODE_IN_MODAL, self::MODE_COMPLETE])) {
            $this->buildInModalForm($builder, $options);
        }

        if (in_array($options['mode'], [self::MODE_OFF_MODAL, self::MODE_COMPLETE])) {
            $this->buildOffModalForm($builder, $options);
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['id'] = 'Widget_SettingsForm';
    }

    protected function buildInModalForm(FormBuilderInterface $builder, array $options)
    {
        // to surcharge but exists as empty to avoid error if not implemented by children
    }

    protected function buildOffModalForm(FormBuilderInterface $builder, array $options)
    {
        // to surcharge but exists as empty to avoid error if not implemented by children
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Widget::class);
        $resolver->setDefault('mode', self::MODE_COMPLETE);
    }
}