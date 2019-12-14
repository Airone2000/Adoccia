<?php

namespace App\EntityListener;

use App\Entity\Category;
use App\Entity\Picture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CategoryListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    public function prePersist(Category $category): void
    {
        if ('bin/console' !== $_SERVER['PHP_SELF']) {
            $category->setCreatedBy($this->tokenStorage->getToken()->getUser());
        }
    }

    public function preUpdate(Category $category, PreUpdateEventArgs $preUpdateEventArgs): void
    {
        $changeSet = $preUpdateEventArgs->getEntityChangeSet();
        $this->deleteOldPicture($changeSet, $preUpdateEventArgs->getEntityManager());
    }

    private function deleteOldPicture(array $changeSet, EntityManagerInterface $entityManager): void
    {
        if (isset($changeSet['picture'])) {
            [$oldPicture] = $changeSet['picture'];
            if ($oldPicture instanceof Picture) {
                $entityManager->remove($oldPicture); // Will flush ...
            }
        }
    }
}
