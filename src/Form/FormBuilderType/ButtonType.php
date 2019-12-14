<?php

namespace App\Form\FormBuilderType;

use App\Entity\Widget;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Url;

final class ButtonType extends AbstractType
{
    use FormBuilderTypeTrait;

    public function getBlockPrefix()
    {
        return 'fichit_button';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->unifiedBuildView($view, $form, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->unifiedConfigureOptions($resolver);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $builder
            ->add('label', TextType::class, [
                'required' => false, // Never required
                'attr' => [
                    'placeholder' => 'Label',
                    'style' => 'width:inherit;flex:0.5;',
                ],
            ])
            ->add('target', TextType::class, [
                'required' => $widget->isRequired(),
                'attr' => [
                    'placeholder' => 'Destination',
                    'style' => 'width:inherit;flex:1;',
                ],
                'constraints' => [
                    new Url(),
                ],
            ])
        ;
    }
}
