<?php

namespace App\Validator;

use Doctrine\Common\Annotations\Annotation\Target;
use Symfony\Component\Validator\Constraint;
/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Enum extends Constraint
{
    public $enumClass;
    public $message;
}