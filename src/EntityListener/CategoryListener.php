<?php

namespace App\EntityListener;

use App\Entity\Category;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CategoryListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    function prePersist(Category $category): void
    {
        $category->setCreatedBy($this->tokenStorage->getToken()->getUser());
    }
}