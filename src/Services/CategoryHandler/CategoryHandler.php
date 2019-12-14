<?php

namespace App\Services\CategoryHandler;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CategoryHandler implements CategoryHandlerInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage,
                                EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    public function setCreatedBy(Category $category, ?User $user = null, bool $autoPersist = false): CategoryHandlerInterface
    {
        if (null === $user) {
            $user = $this->tokenStorage->getToken()->getUser();
        }

        $category->setCreatedBy($user);

        if ($autoPersist) {
            if (null === $category->getId()) {
                $this->entityManager->persist($category);
            }
            $this->entityManager->flush($category);
        }

        return $this;
    }
}
