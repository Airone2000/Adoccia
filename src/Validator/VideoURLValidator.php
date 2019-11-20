<?php

namespace App\Validator;

use App\Services\VideoHandler\VideoHandler;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class VideoURLValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\VideoURL */

        if (null === $value || '' === $value) {
            return;
        }

        if (!VideoHandler::isSupported($value)) {
            $supported = array_keys(VideoHandler::SUPPORTED);
            $supported = implode(', ', $supported);
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->setParameter('{{ providers }}', $supported)
                ->addViolation();
        }
    }
}
