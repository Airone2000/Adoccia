<?php

namespace App\Security\Voter;

use App\Entity\Form;
use App\Entity\User;
use App\Entity\Widget;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class WidgetVoter extends Voter
{
    const
        CHANGE_WIDGET_TYPE = 'CHANGE_WIDGET_TYPE',
        GET_WIDGET_SETTING_VIEW = 'GET_WIDGET_SETTING_VIEW',
        SET_WIDGET_SETTING = 'SET_WIDGET_SETTING'
    ;
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
        if ($attribute === self::CHANGE_WIDGET_TYPE && $subject instanceof Widget) return true;
        if ($attribute === self::GET_WIDGET_SETTING_VIEW && $subject instanceof Widget) return true;
        if ($attribute === self::SET_WIDGET_SETTING && $subject instanceof Widget) return true;
        return false;
    }

    /**
     * @var Widget $subject
     * @inheritdoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User|null $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::CHANGE_WIDGET_TYPE:
            case self::GET_WIDGET_SETTING_VIEW:
            case self::SET_WIDGET_SETTING:
                /** @var Form $form */
                $form = $subject->getFormArea()->getForm();
                return $this->authorizationChecker->isGranted(FormVoter::EDIT_DRAFT_FORM, $form);

        }

        return false;
    }
}
