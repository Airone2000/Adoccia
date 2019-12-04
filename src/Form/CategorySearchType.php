<?php

namespace App\Form;

use App\Entity\CategorySearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CategorySearchType extends AbstractType
{
    const
        MODE_FULL = 'full',
        MODE_TITLE_ONLY = 'title_only',
        MODE_MORE_ONLY = 'more_only'
    ;

    const
        MODES = [
            self::MODE_FULL, self::MODE_TITLE_ONLY, self::MODE_MORE_ONLY
        ]
    ;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $mode = $options['mode'];
        if ($mode === self::MODE_FULL) {
            $this->addTitleField($builder);
            $this->addMoreOnlyFields($builder);
        }
        elseif ($mode === self::MODE_TITLE_ONLY) {
            $this->addTitleField($builder);
        }
        elseif ($mode === self::MODE_MORE_ONLY) {
            $this->addMoreOnlyFields($builder);
        }
    }

    private function addTitleField(FormBuilderInterface $builder): void
    {
        $builder
            ->add('title', null, [
            'required' => false,
            'attr' => [
                'placeholder' => 'Filtrer par nom ...',
                'autocomplete' => 'off'
            ]
        ]);
    }

    private function addMoreOnlyFields(FormBuilderInterface $builder): void
    {
        $builder
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
        $resolver->setRequired('mode');
        $resolver->setAllowedValues('mode', self::MODES);
    }
}