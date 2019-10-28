<?php

namespace App\Form\WidgetType;

class EmptyType extends AbstractWidgetType
{
    public function getBlockPrefix()
    {
        return 'fichit_empty';
    }
}