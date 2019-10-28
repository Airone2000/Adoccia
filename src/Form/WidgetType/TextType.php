<?php

namespace App\Form\WidgetType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TextType extends AbstractWidgetType
{
    public function getBlockPrefix()
    {
        return 'fichit_text';
    }

    public function getParent()
    {
        return TextareaType::class;
    }
}