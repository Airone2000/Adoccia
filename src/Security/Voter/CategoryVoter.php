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
        ADD_FICHE_TO_CATEGORY = 'ADD_FICHE_TO_CATEGORY'
    ;

    protected function supports($attribute, $subject)
    {
        if ($attribute === self::ADD_FICHE_TO_CATEGORY && $subject instanceof Category) return true;
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

            default:
                return false;
        }
    }

    private function canAddFicheToCategory(User $user, Category $category): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($user->getId() === $category->getCreatedBy()->getId()) return true;
        return false;
    }
}
