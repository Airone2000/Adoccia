<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AdvancedPictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('uploadedFile', FileType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'file'
                ]
            ])
            ->add('cropCoords', HiddenType::class, [
                'attr' => [
                    'class' => 'cropCoords'
                ]
            ])
        ;

        $builder->get('cropCoords')->addModelTransformer(new CallbackTransformer(
            function($value){return $value;},
            function($value) use ($builder) {
                if ($value !== null) {
                    return json_decode($value, true);
                }
                return null;
            }
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['picture'] = $options['picture'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Picture::class
        ]);

        $resolver->setRequired('picture');
        $resolver->setAllowedTypes('picture', Picture::class);
    }

    public function getBlockPrefix()
    {
        return 'advanced_picture';
    }
}