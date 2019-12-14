<?php

namespace App\Repository;

use App\Entity\Form;
use App\Entity\FormArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class FormAreaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormArea::class);
    }

    public function getLastPositionedFormArea(Form $form): ?FormArea
    {
        try {
            $q = $this->createQueryBuilder('fa');

            return $q
                ->where('fa.form = :form')
                ->setParameter('form', $form)
                ->orderBy('fa.position', 'desc')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (\Exception $exception) {
            return null;
        }
    }
}
