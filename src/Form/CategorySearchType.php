<?php

namespace App\Form;

use App\Entity\CategorySearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CategorySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Filtrer par nom ...',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('orderBy', ChoiceType::class, [
                'choices' => [
                    'created_at_desc' => 'created_at_desc',
                    'created_at_asc' => 'created_at_asc',
                    'name_asc' => 'name_asc',
                    'name_desc' => 'name_desc'
                ],
                'choice_label' => function($value) {
                    return "CategorySearchType.orderBy.{$value}";
                }
            ])
            ->add('filter', ChoiceType::class, [
                'choices' => [
                    'all' => 'all',
                    'mine' => 'mine'
                ],
                'choice_label' => function($value) {
                    return "CategorySearchType.filter.{$value}";
                },
                'attr' => [
                    'class' => 'uk-select uk-form-small'
                ]
            ])
            ->add('itemsPerPage', ChoiceType::class, [
                'choices' => [30 => 30, 90 => 90, 200 => 200, 700 => 700],
                'choice_label' => function($value) {
                    return "{$value} collections par page";
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', CategorySearch::class);
        $resolver->setDefault('label', false);
        $resolver->setDefault('csrf_protection', false);
    }
}