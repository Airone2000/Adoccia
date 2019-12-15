<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Feedback;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FeedbackListener
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(Feedback $feedback)
    {
        $feedback->setCreatedAt(new \DateTimeImmutable());

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $feedback->setAuthor($user);
    }
}
