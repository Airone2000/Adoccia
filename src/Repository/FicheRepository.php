<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Fiche;
use App\Entity\User;
use App\Enum\SearchCriteriaEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @method Fiche|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fiche|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fiche[]    findAll()
 * @method Fiche[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FicheRepository extends ServiceEntityRepository
{
    /**
     * @var WidgetRepository
     */
    private $widgetRepository;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(ManagerRegistry $registry, WidgetRepository $widgetRepository, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($registry, Fiche::class);
        $this->widgetRepository = $widgetRepository;
        $this->tokenStorage = $tokenStorage;
    }

    private function getUser(): ?User
    {
        if ($this->tokenStorage->getToken() instanceof AnonymousToken) {
            return null;
        }
        /* @var User|null $user */
        $user = $this->tokenStorage->getToken()->getUser();
        return $user;
    }

    public function getCategoryFiches(Category $category, array $moreCriterias = [])
    {
        $qb = $this->createQueryBuilder('f');

        return $qb->getQuery()->getResult();
    }

    /**
     * Used by the CategoryFinder to retrieve fiches having values that match user criterias
     * @param QueryBuilder $queryBuilder
     * @param array $values
     * @param int $havingCount
     * @return QueryBuilder
     */
    public function getFicheByValues(QueryBuilder $queryBuilder, array $values, int $havingCount): QueryBuilder
    {
        $this->getForUser($this->getUser(), $queryBuilder);
        return $queryBuilder
            ->leftJoin('f.values', 'v')
            ->andWhere('v.id IN (:values)')
            ->setParameter('values', $values)
            ->having('COUNT(v) = :nOfMatchingValues')
            ->setParameter('nOfMatchingValues', $havingCount)
            ->groupBy('f')
        ;
    }

    public function filterByTitle(QueryBuilder $queryBuilder, string $criteria, ?string $value)
    {
        if (is_null($value)) return;
        if (!SearchCriteriaEnum::isset($criteria)) return;

        switch ($criteria) {
            case SearchCriteriaEnum::DISABLED:
                // Nothing
                return;
            case SearchCriteriaEnum::CONTAINS:
                $value = explode(',', $value);
                $value = $this->removeNullOrBlankValuesFromArray($value);
                $queryBuilder
                    ->andWhere('REGEXP(f.title, :title) = 1')
                    ->setParameter('title', implode('|', $value))
                ;
                return;
            case SearchCriteriaEnum::EXACT:
                $value = explode(',', $value);
                $value = $this->removeNullOrBlankValuesFromArray($value);
                $queryBuilder
                    ->andWhere('f.title IN (:titles)')
                    ->setParameter('titles', $value)
                ;
                return;
            case SearchCriteriaEnum::STARTS_WITH:
                $value = explode(',', $value);
                $value = $this->removeNullOrBlankValuesFromArray($value);
                $queryBuilder
                    ->andWhere('REGEXP(f.title, :title) = 1')
                    ->setParameter('title', '^('.implode('|', $value).')')
                ;
                return;
            case SearchCriteriaEnum::ENDS_WITH:
                $value = explode(',', $value);
                $value = $this->removeNullOrBlankValuesFromArray($value);
                $queryBuilder
                    ->andWhere('REGEXP(f.title, :title) = 1')
                    ->setParameter('title', '('.implode('|', $value).')$')
                ;
                return;
        }
    }

    public function findFicheByCategoryAndId($categoryId, $ficheId)
    {
        $qb = $this->createQueryBuilder('f');
        return $qb
            ->where('f.category = :category')
            ->setParameter('category', $categoryId)
            ->andWhere('f.id = :id')
            ->setParameter('id', $ficheId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    private function removeNullOrBlankValuesFromArray(array $tab)
    {
        $tab = array_map('trim', $tab);
        $tab = array_filter($tab, function($val){
            return !($val === '' || $val === null);
        });
        return $tab;
    }

    public function getCreatorsForCategory(Category $category): array
    {
        # SubQuery
        $qDistinctUser = $this->createQueryBuilder('f');
        $qDistinctUser
            ->select('DISTINCT(f.creator)')
            ->where('f.category = :category')
        ;

        # Main query with subQuery appended
        $qUsers = $this->getEntityManager()->createQueryBuilder();
        $users = $qUsers
            ->select('u.id, u.username')
            ->from(User::class, 'u', 'u.id')
            ->where('u IN ('. $qDistinctUser->getDQL() .')')
            # /!\ SubQuery parameter must be set on the parent query
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult()
        ;

        return $users;
    }

    private function getForUser(?User $user, QueryBuilder $queryBuilder): void
    {
        $q = '(f.published = 1 AND f.valid = 1)';
        if ($user instanceof User) {
            $q = "({$q} OR f.creator = :user)";
            $queryBuilder->setParameter('user', $user);
        }
        $queryBuilder->andWhere($q);
    }

    public function findAllForCategoryAndUser(Category $category, ?User $user, int $page = 1, int $items = 30): Paginator
    {
        $qb = $this->createQueryBuilder('f');
        $this->getForUser($user, $qb);
        $qb
            ->setFirstResult(($page - 1) * $items)
            ->setMaxResults($items)
        ;
        $paginator = new Paginator($qb, false);
        return $paginator;
    }

    public function getOneForUserByCategoryAndId(?User $user, $categoryId, $ficheId): ?Fiche
    {
        $user = $user ?? $this->getUser();
        $qb = $this->createQueryBuilder('f');
        $this->getForUser($user, $qb);
        return $qb
            ->andWhere('f.id = :fid')
            ->andWhere('f.category = :cid')
            ->setParameter('fid', $ficheId)
            ->setParameter('cid', $categoryId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
