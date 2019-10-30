<?php

namespace App\Form\WidgetType;

use App\Entity\Widget;
use App\Enum\FicheModeEnum;
use App\Enum\SearchCriteriaEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbstractWidgetType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('widget')
            ->setAllowedTypes('widget', Widget::class)
            ->setDefault('mode', null)
            ->setAllowedValues('mode', FicheModeEnum::toArray())
            ->setDefault('compound', false);
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        /** @var \App\Entity\Widget $widget */
        $widget = $options['widget'];
        $view->vars['widget'] = $widget;
    }

    protected function getSearchCriterias(): array
    {
        return [
            SearchCriteriaEnum::IS_NULL,
            SearchCriteriaEnum::EXACT
        ];
    }

    /**
     * This method allows to transform the value before inserting it in the widget
     * @param Widget $widget
     * @param mixed $value
     * @return mixed The value transformed
     */
    public static function transformTo(Widget $widget, $value)
    {
        return $value;
    }

    /**
     * This method allows to transform the value from the form
     * @param Widget $widget
     * @param mixed $value
     * @return mixed The value transformed
     */
    public static function transformFrom(Widget $widget, $value)
    {
        return $value;
    }
}