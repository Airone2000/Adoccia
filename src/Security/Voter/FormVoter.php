<?php

namespace App\Security\Voter;

use App\Entity\Category;
use App\Entity\Form;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class FormVoter extends Voter
{
    const SEE_DRAFT_FORM = 'SEE_DRAFT_FORM';
    const EDIT_DRAFT_FORM = 'EDIT_DRAFT_FORM';
    const ADD_FORM_AREA_TO_DRAFT_FORM = 'ADD_FORM_AREA_TO_DRAFT_FORM';
    const SORT_DRAFT_FORM_AREAS = 'SORT_DRAFT_FORM_AREAS';
    const PUBLISH_DRAFT_FORM = 'PUBLISH_DRAFT_FORM';
    const DELETE_DRAFT_FORM = 'DELETE_DRAFT_FORM';
    const PREVIEW_DRAFT_FORM = 'PREVIEW_DRAFT_FORM';

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    protected function supports($attribute, $subject)
    {
        if (self::SEE_DRAFT_FORM === $attribute && $subject instanceof Form) {
            return true;
        }
        if (self::EDIT_DRAFT_FORM === $attribute && $subject instanceof Form) {
            return true;
        }
        if (self::ADD_FORM_AREA_TO_DRAFT_FORM === $attribute && $subject instanceof Form) {
            return true;
        }
        if (self::SORT_DRAFT_FORM_AREAS === $attribute && $subject instanceof Form) {
            return true;
        }
        if (self::PUBLISH_DRAFT_FORM === $attribute && $subject instanceof Form) {
            return true;
        }
        if (self::DELETE_DRAFT_FORM === $attribute && $subject instanceof Form) {
            return true;
        }
        if (self::PREVIEW_DRAFT_FORM === $attribute && $subject instanceof Form) {
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
            case self::SEE_DRAFT_FORM:
                return $this->canSeeDraftForm($user, $subject);
            case self::EDIT_DRAFT_FORM:
                return $this->canEditDraftForm($user, $subject);
            case self::ADD_FORM_AREA_TO_DRAFT_FORM:
                return $this->canAddFormAreaToDraftForm($user, $subject);
            case self::SORT_DRAFT_FORM_AREAS:
                return $this->canSortDraftFormAreas($user, $subject);
            case self::PUBLISH_DRAFT_FORM:
                return $this->canPublishDraftForm($user, $subject);
            case self::DELETE_DRAFT_FORM:
                return $this->canDeleteDraftForm($user, $subject);
            case self::PREVIEW_DRAFT_FORM:
                return $this->canPreviewDraftForm($user, $subject);
        }

        return false;
    }

    private function canEditDraftForm(User $user, Form $form): bool
    {
        /** @var Category|null $category */
        $category = $this->categoryRepository->findOneBy(['draftForm' => $form]);

        if (($category instanceof Category) && ($category->getCreatedBy() instanceof User)) {
            // SuperAdmin can edit everything
            if ($user->isSuperAdmin()) {
                return true;
            }
            // Creator of category can edit his form
            if ($category->getCreatedBy()->getId() === $user->getId()) {
                return true;
            }
        }

        return false;
    }

    private function canPreviewDraftForm(User $user, Form $form): bool
    {
        return $this->canEditDraftForm($user, $form);
    }

    private function canDeleteDraftForm(User $user, Form $form): bool
    {
        return $this->canEditDraftForm($user, $form);
    }

    private function canPublishDraftForm(User $user, Form $form): bool
    {
        return $this->canEditDraftForm($user, $form);
    }

    private function canSeeDraftForm(User $user, Form $form): bool
    {
        return $this->canEditDraftForm($user, $form);
    }

    private function canAddFormAreaToDraftForm(User $user, Form $form): bool
    {
        return $this->canEditDraftForm($user, $form);
    }

    private function canSortDraftFormAreas(User $user, Form $form): bool
    {
        return $this->canEditDraftForm($user, $form);
    }
}
