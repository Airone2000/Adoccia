<?php

namespace App\Repository;

use App\Entity\CategorySearch;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @method CategorySearch|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorySearch|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorySearch[]    findAll()
 * @method CategorySearch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorySearchRepository extends ServiceEntityRepository
{
    /**
     * @var ManagerRegistry
     */
    private $registry;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(ManagerRegistry $registry, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($registry, CategorySearch::class);
        $this->registry = $registry;
        $this->tokenStorage = $tokenStorage;
    }

    public function findOneByUserOrGuestUniqueID(?User $user, ?string $guid): CategorySearch
    {
        $default = new CategorySearch();
        if ($user === null && $guid === null) return $default;
        $qb = $this->createQueryBuilder('cs');

        $attribute = $user instanceof User ? 'user' : 'guestUniqueID';
        $reverseAttribute = $attribute === 'user' ? 'guestUniqueID' : 'user';
        $value = $attribute === 'user' ? $user : $guid;
        $parameterKey = uniqid(':key_');

        $categorySearch = $qb
            ->where("cs.{$attribute} = {$parameterKey}")
            ->setParameter($parameterKey, $value)
            ->andWhere("cs.{$reverseAttribute} IS NULL")
            ->setMaxResults(1)
            ->orderBy('cs.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        $categorySearch = $categorySearch[0] ?? new CategorySearch();
        call_user_func([$categorySearch, "set{$attribute}"], $value);

        if ($categorySearch->isNew()) {
            $this->_em->persist($categorySearch);
            $this->_em->flush($categorySearch);
        }

        return $categorySearch;
    }
}
