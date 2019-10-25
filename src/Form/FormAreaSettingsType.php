<?php

namespace App\Form;

use App\Entity\FormArea;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormAreaSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('marginTop')
            ->add('marginBottom')
            ->add('marginLeft')
            ->add('marginRight')
            ->add('paddingTop')
            ->add('paddingBottom')
            ->add('paddingLeft')
            ->add('paddingRight')
            ->add('borderTopWidth')
            ->add('borderTopColor')
            ->add('borderBottomWidth')
            ->add('borderBottomColor')
            ->add('borderLeftWidth')
            ->add('borderLeftColor')
            ->add('borderRightWidth')
            ->add('borderRightColor')
            ->add('backgroundColor')
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['id'] = 'FormArea_SettingsForm';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FormArea::class
        ]);
    }
}
