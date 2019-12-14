<?php

namespace App\Form\FormBuilderType;

use App\Entity\Widget;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RadioType extends AbstractType
{
    use FormBuilderTypeTrait;

    public function getBlockPrefix()
    {
        return 'fichit_radio';
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->unifiedBuildView($view, $form, $options);

        /* @var Widget $widget */
        $widget = $options['widget'];

        // Build the list of values
        $choices = array_flip(self::getChoices($widget));
        $selectedChoices = array_flip((array) $view->vars['value']);
        $selectedValues = array_values(array_intersect_key($choices, $selectedChoices));
        $view->vars['transformed_value_to_display'] = implode(', ', $selectedValues);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->unifiedConfigureOptions($resolver);
    }

    public static function getChoices(Widget $widget): array
    {
        $choicesKey = array_map(function ($value) {
            return hash('sha256', (string) $value);
        }, $widget->getChoices());

        $choices = array_combine($widget->getChoices(), $choicesKey) ?? [];

        return $choices;
    }
}
