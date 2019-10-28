<?php

namespace App\Services\CategoryFinder;

use App\Entity\Category;

interface CategoryFinderInterface
{
    public function search(Category $category, array $criterias): array;
}