<?php

namespace App\Security\Voter;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryVoter extends Voter
{
    const ACCESS_ALL_CATEGORIES = 'ACCESS_ALL_CATEGORIES';
    const ADD_FICHE_TO_CATEGORY = 'ADD_FICHE_TO_CATEGORY';
    const EDIT_CATEGORY = 'EDIT_CATEGORY';
    const EDIT_CATEGORY_FORM = 'EDIT_CATEGORY_FORM';
    const DELETE_CATEGORY = 'DELETE_CATEGORY';

    protected function supports($attribute, $subject)
    {
        if (self::ACCESS_ALL_CATEGORIES === $attribute) {
            return true;
        }
        if (self::ADD_FICHE_TO_CATEGORY === $attribute && $subject instanceof Category) {
            return true;
        }
        if (self::EDIT_CATEGORY === $attribute && $subject instanceof Category) {
            return true;
        }
        if (self::EDIT_CATEGORY_FORM === $attribute && $subject instanceof Category) {
            return true;
        }
        if (self::DELETE_CATEGORY === $attribute && $subject instanceof Category) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::ADD_FICHE_TO_CATEGORY:
                return $this->canAddFicheToCategory($user, $subject);

            case self::EDIT_CATEGORY:
                return $this->canEditCategory($user, $subject);

            case self::EDIT_CATEGORY_FORM:
                return $this->canEditCategoryForm($user, $subject);

            case self::DELETE_CATEGORY:
                return $this->canDeleteCategory($user, $subject);

            case self::ACCESS_ALL_CATEGORIES:
                return self::canAccessAllCategories($user);

            default:
                return false;
        }
    }

    public static function canAccessAllCategories(?User $user): bool
    {
        if ($user instanceof User && $user->isSuperAdmin()) {
            return true;
        }

        return false;
    }

    public static function canSearchInCategory(?User $user, Category $category): bool
    {
        return self::canSeeCategory($user, $category);
    }

    public static function canListCategoryFiches(?User $user, Category $category): bool
    {
        return self::canSeeCategory($user, $category);
    }

    public static function canSeeCategory(?User $user, Category $category): bool
    {
        if ($category->isOnline() && $category->isPublic()) {
            return true;
        }
        if ($user instanceof User) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            if ($category->getCreatedBy() && $category->getCreatedBy() === $user) {
                return true;
            }
        }

        return false;
    }

    private function canDeleteCategory(User $user, Category $category): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        if ($category->getCreatedBy() && $category->getCreatedBy()->getId() === $user->getId()) {
            return true;
        }

        return false;
    }

    private function canEditCategoryForm(User $user, Category $category): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        if ($category->getCreatedBy() && $category->getCreatedBy()->getId() === $user->getId()) {
            return true;
        }

        return false;
    }

    private function canEditCategory(User $user, Category $category): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        if ($category->getCreatedBy() && $category->getCreatedBy()->getId() === $user->getId()) {
            return true;
        }

        return false;
    }

    private function canAddFicheToCategory(User $user, Category $category): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        if ($category->getCreatedBy() && $category->getCreatedBy()->getId() === $user->getId()) {
            return true;
        }

        return false;
    }
}
