<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Form;
use App\Entity\FormArea;
use App\Entity\Widget;
use App\Enum\FicheModeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchInCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addImmutableFields($builder, $options);
        $this->addDynamicFields($builder, $options);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['id'] = 'InnerFiche_RowsWrapper';
        $view->vars['mode'] = $options['mode'];
    }

    private function addImmutableFields(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                $builder->create('title', FormType::class)
                    ->add('criteria', ChoiceType::class, [
                        'choices' => []
                    ])
                    ->add('value', TextType::class, [
                        'required' => false
                    ])
            )
        ;
    }

    private function addDynamicFields(FormBuilderInterface $builder, array $options)
    {
        /** @var Category $category */
        $category = $options['category'];
        /** @var Form $form */
        $form = $category->getForm();

        /** @var FormArea $formArea */
        foreach ($form->getAreas() as $formArea)
        {
            /** @var Widget $widget */
            $widget = $formArea->getWidget();

            $type = ucfirst(strtolower($widget->getType()));
            $typeClass = "App\Form\WidgetType\\{$type}Type";

            if (class_exists($typeClass)) {
                $builder->add($widget->getId(), $typeClass, [
                    'widget' => $widget,
                    'mode' => $options['mode']
                ]);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('category');
        $resolver->setAllowedTypes('category', Category::class);

        $resolver->setDefault('mode', FicheModeEnum::SEARCH);
        $resolver->setAllowedValues('mode', FicheModeEnum::SEARCH);
    }
}