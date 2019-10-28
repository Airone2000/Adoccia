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
}