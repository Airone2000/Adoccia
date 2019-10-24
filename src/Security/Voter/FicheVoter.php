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
        SEE_FICHE = 'CAN_SEE_FICHE'
    ;

    protected function supports($attribute, $subject)
    {
        if ($attribute === self::SEE_FICHE && $subject instanceof Fiche) return true;
        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::SEE_FICHE:
                return $this->canSeeFiche($user, $subject);

        }

        return false;
    }

    private function canSeeFiche(User $user, Fiche $fiche): bool
    {
        # SuperAdmin
        if ($user->isSuperAdmin()) return true;

        # Category creator
        if (($categoryCreator = $fiche->getCategory()->getCreatedBy()) instanceof User) {
            if ($categoryCreator->getId() === $user->getId()) return true;
        }

        return false;
    }
}
