<?php

namespace App\Security\Voter;

use App\Entity\Fiche;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class FicheVoter extends Voter
{
    const
        EDIT_FICHE = 'CAN_EDIT_FICHE',
        DELETE_FICHE = 'CAN_DELETE_FICHE'
    ;

    protected function supports($attribute, $subject)
    {
        if ($attribute === self::EDIT_FICHE && $subject instanceof Fiche) return true;
        if ($attribute === self::DELETE_FICHE && $subject instanceof Fiche) return true;
        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT_FICHE:
                return $this->canEditFiche($user, $subject);

            case self::DELETE_FICHE:
                return $this->canDeleteFiche($user, $subject);

        }

        return false;
    }

    private function canDeleteFiche(User $user, Fiche $fiche): bool
    {
        if (CategoryVoter::canSeeCategory($user, $fiche->getCategory())) {
            if ($user->isSuperAdmin()) return true;

            # Category creator can delete his own fiche
            if (($categoryCreator = $fiche->getCategory()->getCreatedBy()) instanceof User) {
                if ($categoryCreator->getId() === $user->getId()) return true;
            }

            # Fiche creator can delete his own fiche
            if ($fiche->getCreator() && $fiche->getCreator() === $user) return true;
        }
        return false;
    }

    private function canEditFiche(User $user, Fiche $fiche): bool
    {
        if (CategoryVoter::canSeeCategory($user, $fiche->getCategory())) {
            # SuperAdmin
            if ($user->isSuperAdmin()) return true;

            # Category creator
            if (($categoryCreator = $fiche->getCategory()->getCreatedBy()) instanceof User) {
                if ($categoryCreator->getId() === $user->getId()) return true;
            }

            # Fiche creator
            if ($fiche->getCreator() && $fiche->getCreator() === $user) return true;
        }
        return false;
    }

    public static function canSeeFiche(?User $user, Fiche $fiche): bool
    {
        if (CategoryVoter::canSeeCategory($user, $fiche->getCategory())) {
            if ($fiche->isPublished() && $fiche->isValid()) {
                return true;
            }

            if ($user instanceof User) {
                if ($user->isSuperAdmin()) return true;
                if ($fiche->getCreator() && $fiche->getCreator() === $user) return true;
            }
        }

        return false;
    }
}
