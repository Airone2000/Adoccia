<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class HasMarkersCountBetweenValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint|HasMarkersCountBetween $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $minMarkers = $constraint->min;
        $maxMarkers = $constraint->max;

        if (!is_array($value)) {
            return;
        }

        if (!isset($value['markers'])) {
            return;
        }

        if ($minMarkers === null && $maxMarkers === null) {
            return;
        }

        $markersCount = count((array)$value['markers']);

        if (is_int($minMarkers)) {
            if ($markersCount < $minMarkers) {
                $this->context->buildViolation(
                    $constraint->minMessage, ['{{ value }}' => $minMarkers]
                )->addViolation();
            }
        }

        if (is_int($maxMarkers)) {
            if ($markersCount > $maxMarkers) {
                $this->context->buildViolation(
                    $constraint->maxMessage, ['{{ value }}' => $maxMarkers]
                )->addViolation();
            }
        }
    }
}
