<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Form;
use App\Entity\Widget;
use App\Enum\FicheModeEnum;
use App\Enum\SearchCriteriaEnum;
use App\Repository\WidgetRepository;
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
    /**
     * Caches references to builders to prevent
     * recreate instance for each dynamic field.
     *
     * @var \App\Form\FormBuilder\FormBuilderInterface[]
     */
    private static $loadedDynamicFieldsBuilders = [];
    /**
     * @var WidgetRepository
     */
    private $widgetRepository;

    public function __construct(WidgetRepository $widgetRepository)
    {
        $this->widgetRepository = $widgetRepository;
    }

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
                        'choices' => [
                            SearchCriteriaEnum::DISABLED,
                            SearchCriteriaEnum::CONTAINS,
                            SearchCriteriaEnum::EXACT,
                            SearchCriteriaEnum::STARTS_WITH,
                            SearchCriteriaEnum::ENDS_WITH,
                        ],
                        'choice_label' => function (string $value) {
                            return "trans.{$value}";
                        },
                        'attr' => [
                            'class' => 'searchOnTitleCriteria',
                        ],
                        'choice_attr' => function (string $value) {
                            $attr = [];
                            switch ($value) {
                                case SearchCriteriaEnum::CONTAINS:
                                case SearchCriteriaEnum::EXACT:
                                case SearchCriteriaEnum::STARTS_WITH:
                                case SearchCriteriaEnum::ENDS_WITH:
                                    $attr['data-inputs'] = '.value';
                                    break;
                            }

                            return $attr;
                        },
                    ])
                    ->add('value', TextType::class, [
                        'required' => false,
                        'attr' => [
                            'placeholder' => 'Rechercher dans le titre',
                            'class' => 'value hidden',
                        ],
                        'label' => false,
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
        /** @var Widget[] $widgets */
        $widgets = $this->widgetRepository->findByForm($form);

        foreach ($widgets as $widget) {
            $type = ucfirst(mb_strtolower($widget->getType()));
            $builderClass = "App\Form\FormBuilder\\{$type}Builder";

            /* @var \App\Form\FormBuilder\FormBuilderInterface[] $loadedBuilders */
            if (!isset(self::$loadedDynamicFieldsBuilders[$builderClass])) {
                if (class_exists($builderClass)) {
                    self::$loadedDynamicFieldsBuilders[$builderClass] = new $builderClass();
                } else {
                    throw new \LogicException("Builder class {$builderClass} does not exist.");
                }
            }

            self::$loadedDynamicFieldsBuilders[$builderClass]->buildSearchForm($builder, [
                'mode' => $options['mode'],
                'widget' => $widget,
                'category' => $category,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('category');
        $resolver->setAllowedTypes('category', Category::class);

        $resolver->setDefault('mode', FicheModeEnum::SEARCH);
        $resolver->setAllowedValues('mode', FicheModeEnum::SEARCH);

        $resolver->setDefault('compound', true);
    }
}
