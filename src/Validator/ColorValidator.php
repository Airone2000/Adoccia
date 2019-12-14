<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ColorValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\Color */

        if (null === $value || '' === $value) {
            return;
        }

        // Valid pattern (#fff, #000000, ...)
        if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $value)) {
            return;
        }

        // String (orange, blue, red, ...)
        if (preg_match('/^[a-z]+$/i', $value)) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->addViolation()
        ;
    }
}
