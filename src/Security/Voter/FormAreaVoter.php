<?php

namespace App\Security\Voter;

use App\Entity\FormArea;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class FormAreaVoter extends Voter
{
    const DELETE_FORM_AREA = 'DELETE_FORM_AREA';
    const SET_FORM_AREA_WIDTH = 'SET_FORM_AREA_WIDTH';
    const SET_FORM_AREA_SETTINGS = 'SET_FORM_AREA_SETTINGS';

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    protected function supports($attribute, $subject)
    {
        if (self::DELETE_FORM_AREA === $attribute && $subject instanceof FormArea) {
            return true;
        }
        if (self::SET_FORM_AREA_WIDTH === $attribute && $subject instanceof FormArea) {
            return true;
        }
        if (self::SET_FORM_AREA_SETTINGS === $attribute && $subject instanceof FormArea) {
            return true;
        }

        return false;
    }

    /**
     * @var FormArea
     *               {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User|null $user */
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::DELETE_FORM_AREA:
            case self::SET_FORM_AREA_WIDTH:
            case self::SET_FORM_AREA_SETTINGS:
                return $this->authorizationChecker->isGranted(FormVoter::EDIT_DRAFT_FORM, $subject->getForm());
        }

        return false;
    }
}
