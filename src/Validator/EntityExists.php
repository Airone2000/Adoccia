<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EntityExists extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'This value does not match any instance of {{ class }}::{{ field }}.';

    public $class = null;

    public $field = 'id';
}
