<?php

namespace App\Form\WidgetType;

use App\Entity\Widget;
use App\Enum\DateFormatEnum;
use App\Enum\FicheModeEnum;
use App\Enum\SearchCriteriaEnum;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType as SfTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class DateType extends AbstractWidgetType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['mode'] === FicheModeEnum::SEARCH) {
            $this->buildSearchForm($builder, $options);
        }
    }

    public static function getDateTypePlaceholder(Widget $widget): ?string
    {
        if ($widget->getInputPlaceholder()) {
            $placeholder = $widget->getInputPlaceholder();
        }
        else {
            $placeholder = preg_replace('/[dmy]/i', '_', $widget->getDateFormat());
        }

        return $placeholder;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /**
         * @var Widget $widget
         */
        $widget = $options['widget'];

        $attr = $view->vars['attr'] ?? [];
        $attr['data-masked'] = 'true';
        $attr['data-inputmask-alias'] = 'datetime';
        $attr['data-inputmask-inputformat'] = $widget->getDateFormat();



        $attr['data-inputmask-placeholder'] = self::getDateTypePlaceholder($widget);
        $attr['inputmode'] = 'numeric';
        $view->vars['attr'] = $attr;
    }

    public function getBlockPrefix()
    {
        return 'fichit_date';
    }

    private function buildSearchForm(FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $builder
            ->add('criteria', ChoiceType::class, [
                'choices' => $this->getSearchCriterias(),
                'choice_label' => function(string $label) {
                    return $label;
                }
            ])
            ->add('value', SfTextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => $widget->getInputPlaceholder()
                ]
            ])
        ;
    }

    protected function getSearchCriterias(): array
    {
        return [
            SearchCriteriaEnum::DISABLED,
            SearchCriteriaEnum::IS_NULL,
            SearchCriteriaEnum::IS_NOT_NULL,
            SearchCriteriaEnum::EXACT
        ];
    }

    public static function transformFrom(Widget $widget, $value)
    {
        if (is_string($value)) {
            $dateFormat = DateFormatEnum::getPHPFormatForJsFormat($widget->getDateFormat());
            $datetime = \DateTime::createFromFormat($dateFormat, $value);
            if ($datetime !== false) {
                $datetime->setTime(0, 0, 0, 0);
                return $datetime;
            }
        }
        return null;
    }

    public static function transformTo(Widget $widget, $value)
    {
        if ($value instanceof \DateTime) {
            $dateFormat = DateFormatEnum::getPHPFormatForJsFormat($widget->getDateFormat());
            return $value->format($dateFormat);
        }
        return null;
    }

}