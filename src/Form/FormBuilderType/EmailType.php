<?php

namespace App\Form\FormBuilderType;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class EmailType extends StringType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $attr = $view->vars['attr'];
        $attr['data-masked'] = 'true';
        $attr['data-inputmask-alias'] = 'email';
        $view->vars['attr'] = $attr;
    }
}
