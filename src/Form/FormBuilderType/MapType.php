<?php

namespace App\Form\FormBuilderType;

use App\Enum\FicheModeEnum;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapType extends HiddenType
{
    use FormBuilderTypeTrait;

    public function getBlockPrefix()
    {
        return 'fichit_map';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->unifiedBuildView($view, $form, $options);
        $view->vars['attr']['class'] = 'value';

        $mapShouldDisplay = true;

        if (FicheModeEnum::DISPLAY === $view->vars['mode']) {
            // HasMarkers var
            // Produce this value for displaying or not the map
            // if no marker, then hide the map, else display it
            $hasMarkers = false;
            $value = json_decode($view->vars['value'], true);
            if (\is_array($value)) {
                $hasMarkers = !empty($value['markers']);
            }
            $view->vars['hasMarkers'] = $hasMarkers;
            $mapShouldDisplay = $hasMarkers;
        }

        $view->vars['mapShouldDisplay'] = $mapShouldDisplay;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->unifiedConfigureOptions($resolver);
    }
}
