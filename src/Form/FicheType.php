<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Fiche;
use App\Entity\FormArea;
use App\Entity\Value;
use App\Entity\Widget;
use App\Enum\FicheModeEnum;
use App\Enum\WidgetTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class FicheType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Caches references to builders to prevent
     * recreate instance for each dynamic field
     * @var array
     */
    private static $loadedDynamicFieldsBuilders = [];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addImmutableFields($builder, $options);
        $this->addDynamicFields($builder, $options);
    }

    /**
     * This method adds fields that are the same independently the fiche we are on.
     * @inheritdoc
     */
    private function addImmutableFields(FormBuilderInterface $builder, array $options)
    {
        if ($options['is_form_preview'] === true) return;

        /* @var Fiche $fiche */
        $fiche = $options['fiche'];

        if ($options['mode'] === FicheModeEnum::EDITION) {
            $builder
                ->add('title', TextType::class, [
                    'constraints' => [
                        new NotBlank(),
                        new Length(['max' => 255])
                    ]
                ])
                ->add('picture', PictureType::class, [
                    'originalPicture' => $fiche->getPicture(),
                    'uniqueId' => uniqid('uid_')
                ])
                ->add('published', CheckboxType::class)
            ;
        }
    }

    /**
     * This method adds fields defined through the category's form builder.
     * @inheritdoc
     */
    private function addDynamicFields(FormBuilderInterface $builder, array $options)
    {
        /** @var Category $category */
        $category = $options['category'];
        $this->loadValuesInOptions($options);

        $form = $options['is_form_preview'] === true ? $category->getDraftForm() : $category->getForm();

        /** @var FormArea $formArea */
        foreach ($form->getAreas() as $formArea)
        {
            /** @var Widget $widget */
            $widget = $formArea->getWidget();
            $widgetType = $widget->getType();

            if (WidgetTypeEnum::isset($widgetType)) {
                $this->addDynamicField($builder, $widget, $options);
            }
            else {
                throw new \LogicException("Unhandled widget of type \"{$widgetType}\".");
            }
        }
    }

    private function addDynamicField(FormBuilderInterface $builder, Widget $widget, array $options)
    {
        $type = ucfirst(strtolower($widget->getType()));
        $builderClass = "App\Form\FormBuilder\\{$type}Builder";

        /* @var \App\Form\FormBuilder\FormBuilderInterface[] $loadedBuilders */
        if (!isset(self::$loadedDynamicFieldsBuilders[$builderClass])) {
            if (class_exists($builderClass)) {
                self::$loadedDynamicFieldsBuilders[$builderClass] = new $builderClass([
                    'em' => $this->entityManager
                ]);
            }
            else {
                throw new \LogicException("Builder class {$builderClass} does not exist.");
            }
        }

        self::$loadedDynamicFieldsBuilders[$builderClass]->buildForm($builder, [
            'mode' => $options['mode'],
            'widget' => $widget,
            'fiche' => $options['fiche'],
            'widgetValue' => $options['mapValueToWidget'][$widget->getId()] ?? null
        ]);
    }

    private function loadValuesInOptions(array &$options): void
    {
        /* @var Fiche $fiche */
        $fiche = $options['fiche'];
        $values = $fiche->getValues();
        $mapValueToWidget = [];

        /* @var Value $value */
        foreach ($values as $value) {
            $mapValueToWidget[$value->getWidget()->getId()] = $value;
        }

        $options['mapValueToWidget'] = $mapValueToWidget;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['id'] = 'InnerFiche_RowsWrapper';
        $view->vars['mode'] = $options['mode'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('category', null);
        $resolver->setRequired('category');
        $resolver->setAllowedTypes('category', Category::class);
        $resolver->setDefault('error_bubbling', false);
        $resolver->setDefault('mode', FicheModeEnum::DISPLAY);
        $resolver->setDefault('is_form_preview', false);
        $resolver->setRequired('fiche');
        $resolver->setAllowedTypes('fiche', Fiche::class);
    }
}