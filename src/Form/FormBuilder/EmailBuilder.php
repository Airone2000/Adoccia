<?php

namespace App\Form\FormBuilder;

use App\Form\FormBuilderType\EmailType;

final class EmailBuilder extends StringBuilder
{
    protected function getBuilderTypeClass()
    {
        return EmailType::class;
    }
}
