<?php

namespace App\Services\CategoryHandler;

use App\Entity\Category;
use App\Entity\User;

interface CategoryHandlerInterface
{
    public function setCreatedBy(Category $category, ?User $user = null, bool $autoPersist = false): self;
}