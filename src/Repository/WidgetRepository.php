<?php

namespace App\Repository;

use App\Entity\Form;
use App\Entity\Value;
use App\Entity\Widget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method Widget|null find($id, $lockMode = null, $lockVersion = null)
 * @method Widget|null findOneBy(array $criteria, array $orderBy = null)
 * @method Widget[]    findAll()
 * @method Widget[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WidgetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Widget::class);
    }

    public function getArrayOfWidgetsIdForForm(Form $form)
    {
        $qb = $this->createQueryBuilder('w');
        return $qb
            ->select('w.id, w.immutableId')
            ->leftJoin('w.formArea', 'fa')
            ->where('fa.form = :form')
            ->setParameter('form', $form)
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function findByForm(Form $form): array
    {
        $qb = $this->createQueryBuilder('w');
        return $qb
            ->leftJoin('w.formArea', 'form_area')
            ->where('form_area.form = :form')
            ->setParameter('form', $form)
            ->getQuery()
            ->getResult()
        ;
    }
}
