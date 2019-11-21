<?php

namespace App\Security\Voter;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryVoter extends Voter
{
    const
        ADD_FICHE_TO_CATEGORY = 'ADD_FICHE_TO_CATEGORY',
        EDIT_CATEGORY = 'EDIT_CATEGORY',
        EDIT_CATEGORY_FORM = 'EDIT_CATEGORY_FORM',
        DELETE_CATEGORY = 'DELETE_CATEGORY'
    ;

    protected function supports($attribute, $subject)
    {
        if ($attribute === self::ADD_FICHE_TO_CATEGORY && $subject instanceof Category) return true;
        if ($attribute === self::EDIT_CATEGORY && $subject instanceof Category) return true;
        if ($attribute === self::EDIT_CATEGORY_FORM && $subject instanceof Category) return true;
        if ($attribute === self::DELETE_CATEGORY && $subject instanceof Category) return true;
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

            default:
                return false;
        }
    }

    public static function canSeeCategory(?User $user, Category $category): bool
    {
        if ($category->isOnline() && $category->isPublic()) {
            return true;
        }
        if ($user instanceof User) {
            if ($user->isSuperAdmin()) return true;
            if ($category->getCreatedBy() && $category->getCreatedBy() === $user) return true;
        }
        return false;
    }

    private function canDeleteCategory(User $user, Category $category): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($category->getCreatedBy() && $category->getCreatedBy()->getId() === $user->getId()) return true;
        return false;
    }

    private function canEditCategoryForm(User $user, Category $category): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($category->getCreatedBy() && $category->getCreatedBy()->getId() === $user->getId()) return true;
        return false;
    }

    private function canEditCategory(User $user, Category $category): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($category->getCreatedBy() &&  $category->getCreatedBy()->getId() === $user->getId()) return true;
        return false;
    }

    private function canAddFicheToCategory(User $user, Category $category): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($category->getCreatedBy() && $category->getCreatedBy()->getId() === $user->getId()) return true;
        return false;
    }
}
