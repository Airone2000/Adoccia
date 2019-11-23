<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Fiche;
use App\Entity\FormArea;
use App\Entity\Picture;
use App\Entity\Widget;
use App\Enum\FicheModeEnum;
use App\Enum\WidgetTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class FicheType extends AbstractType
{
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
                ->add('picture', AdvancedPictureType::class, [
                    'validation_groups' => $options['validation_groups'],
                    'required' => false
                ])
                ->add('published', CheckboxType::class)
            ;

            $this->setDefaultValueForPictureCoords($builder);
        }
    }

    private function setDefaultValueForPictureCoords(FormBuilderInterface $builder): void
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function(PostSubmitEvent $postSubmitEvent){
            $data = $postSubmitEvent->getData();
            $picture = $data['picture'] ?? null;
            if($picture instanceof Picture) {
                if ($picture->getCropCoords() === null) {
                    if ($picture->getUploadedFile() instanceof UploadedFile && $picture->getCropCoords() === null) {
                        $sizes = getimagesize($picture->getUploadedFile());
                        $size = min($sizes[0], $sizes[1]);
                        $x = ($sizes[0] - $size) / 2;
                        $y = ($sizes[1] - $size) / 2;
                        $defaultCropCoords = [
                            'width' => $size,
                            'height' => $size,
                            'x' => $x,
                            'y' => $y
                        ];
                        $picture->setCropCoords($defaultCropCoords);
                    }
                }
            }

            $data['picture'] = $picture;
            $postSubmitEvent->setData($data);
        });
    }

    /**
     * This method adds fields defined through the category's form builder.
     * @inheritdoc
     */
    private function addDynamicFields(FormBuilderInterface $builder, array $options)
    {
        /** @var Category $category */
        $category = $options['category'];

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
                self::$loadedDynamicFieldsBuilders[$builderClass] = new $builderClass();
            }
            else {
                throw new \LogicException("Builder class {$builderClass} does not exist.");
            }
        }

        self::$loadedDynamicFieldsBuilders[$builderClass]->buildForm($builder, [
            'mode' => $options['mode'],
            'widget' => $widget,
            'fiche' => $options['fiche']
        ]);
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