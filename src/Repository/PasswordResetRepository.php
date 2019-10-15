<?php

namespace App\Repository;

use App\Entity\PasswordReset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class PasswordResetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordReset::class);
    }

    public function findOneNonUsedAndExpiredByToken(?string $token): ?PasswordReset
    {
        if ($token === null) {
            return null;
        }

        $q = $this->createQueryBuilder('pr');
        $passwordReset = $q
            ->where('pr.expiresAt > :now')
            ->setParameter('now', new \DateTime())
            ->andWhere('pr.token = :token')
            ->setParameter('token', $token)
            ->andWhere('pr.passwordChangedAt is NULL')
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $passwordReset;
    }

}
