<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\FormArea;
use App\Entity\Widget;
use App\Enum\WidgetTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class FicheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addImmutableFields($builder);
        $this->addDynamicFields($builder, $options);
    }

    /**
     * This method adds fields that are the same independently the fiche we are on.
     * @inheritdoc
     */
    private function addImmutableFields(FormBuilderInterface $builder)
    {
        $builder
            ->add('title', TextType::class)
        ;
    }

    /**
     * This method adds fields defined through the category's form builder.
     * @inheritdoc
     */
    private function addDynamicFields(FormBuilderInterface $builder, array $options)
    {
        /** @var Category $category */
        $category = $options['category'];

        /** @var FormArea $formArea */
        foreach ($category->getForm()->getAreas() as $formArea)
        {
            /** @var Widget $widget */
            $widget = $formArea->getWidget();
            $widgetType = $widget->getType();

            if (WidgetTypeEnum::isset($widgetType)) {
                $this->addDynamicField($builder, $widget);
            }
            else {
                throw new \LogicException("Unhandled widget of type \"{$widgetType}\".");
            }
        }
    }

    private function addDynamicField(FormBuilderInterface $builder, Widget $widget)
    {
        $name = 'widget_'.$widget->getId();
        $type = ucfirst(strtolower($widget->getType()));
        $typeClass = "App\Form\WidgetType\\{$type}Type";

        if (class_exists($typeClass)) {
            $builder
                ->add($name, $typeClass, [
                    'widget' => $widget,
                    'constraints' => $this->getDynamicFieldConstraints($widget),
                    'empty_data' => null
                ])
            ;
        }
        else {
         throw new \LogicException("WidgetType of type \"{$type}\" does not exist. Let's create the class \"{$typeClass}\"");
        }
    }

    private function getDynamicFieldConstraints(Widget $widget): array
    {
        $constraints = [];

        if ($widget->isRequiredSetting()) {
            $constraints[] = new NotBlank();
        }

        return $constraints;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['id'] = 'AddFiche_InnerForm_RowsWrapper';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('category', null);
        $resolver->setRequired('category');
        $resolver->setAllowedTypes('category', Category::class);
        $resolver->setDefault('error_bubbling', false);
    }
}