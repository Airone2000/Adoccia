<?php

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EntityExistsValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @var EntityExists $constraint
     * @inheritdoc
     */
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\EntityExists */

        if (null === $value || '' === $value) {
            return;
        }

        if ($constraint->class === null || $constraint->field === null) {
            throw new \LogicException("Both option 'class' and 'field' are required in EntityExistsValidator.");
        }

        try {
            $criteria = []; $criteria[$constraint->field] = $value;
            $entity = $this->entityManager->getRepository($constraint->class)->findOneBy($criteria);
        }
        catch (\Exception $exception) {
            $entity = null;
        }

        if ($entity === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ field }}', $constraint->field)
                ->setParameter('{{ class }}', $constraint->class)
                ->addViolation()
            ;
        }
    }
}
