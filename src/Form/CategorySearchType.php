<?php

namespace App\Form;

use App\Entity\CategorySearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CategorySearchType extends AbstractType
{
    const MODE_FULL = 'full';
    const MODE_TITLE_ONLY = 'title_only';
    const MODE_MORE_ONLY = 'more_only';
    const
        MODES = [
            self::MODE_FULL, self::MODE_TITLE_ONLY, self::MODE_MORE_ONLY,
        ]
    ;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $mode = $options['mode'];
        if (self::MODE_FULL === $mode) {
            $this->addTitleField($builder);
            $this->addMoreOnlyFields($builder);
        } elseif (self::MODE_TITLE_ONLY === $mode) {
            $this->addTitleField($builder);
        } elseif (self::MODE_MORE_ONLY === $mode) {
            $this->addMoreOnlyFields($builder);
        }
    }

    private function addTitleField(FormBuilderInterface $builder): void
    {
        $builder
            ->add('title', null, [
            'required' => false,
            'attr' => [
                'placeholder' => 'category.search.type.title.placeholder',
                'autocomplete' => 'off',
            ],
        ]);
    }

    private function addMoreOnlyFields(FormBuilderInterface $builder): void
    {
        $builder
            ->add('orderBy', ChoiceType::class, [
            'choices' => [
                'created_at.desc' => 'created_at_desc',
                'created_at.asc' => 'created_at_asc',
                'name.asc' => 'name_asc',
                'name.desc' => 'name_desc',
            ],
            'choice_label' => function ($label, $value) {
                return "category.search.type.order_by.{$value}";
            },
        ])
            ->add('filter', ChoiceType::class, [
                'choices' => [
                    'all' => 'all',
                    'mine' => 'mine',
                ],
                'choice_label' => function ($value) {
                    return "category.search.type.filter.{$value}";
                },
                'attr' => [
                    'class' => 'uk-select uk-form-small',
                ],
            ])
            ->add('itemsPerPage', ChoiceType::class, [
                'choices' => [30 => 30, 90 => 90, 200 => 200, 700 => 700],
                'choice_label' => function ($value) {
                    return "{$value} collections par page";
                },
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
