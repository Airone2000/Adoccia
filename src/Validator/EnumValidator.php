<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EnumValidator extends ConstraintValidator
{
    /**
     * @param \Symfony\Component\Validator\Constraint|Enum$constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value) {
            return;
        }

        if (!class_exists($constraint->enumClass)) {
            throw new \LogicException(sprintf('Enum class does not exist: %', $constraint->enumClass));
        }

        /** @var \App\Enum\AbstractEnum $enumClass */
        $enumClass = $constraint->enumClass;
        $existingValue = \call_user_func([$enumClass, 'isset'], $value);

        if (!$existingValue) {
            $values = \call_user_func([$enumClass, 'toArray'], $value);
            $values = implode(', ', $values);
            $this->context->buildViolation(sprintf('Wrong value. Supported values: %s', $values))->addViolation();
        }
    }
}
