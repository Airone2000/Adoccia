<?php

namespace App\Validator;

use App\Entity\Value;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class HasMapShapeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value === null) return;

        /* @var $constraint \App\Validator\HasMapShape */
        $defaultMapValue = Value::DEFAULT_VALUE_OF_TYPE_MAP;

        if (!count(array_intersect_key($value, $defaultMapValue)) === count($defaultMapValue)) {
            $this->context->buildViolation('')->addViolation();
        }
    }
}
