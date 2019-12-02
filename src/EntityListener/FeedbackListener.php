<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Feedback;

class FeedbackListener
{
    public function prePersist(Feedback $feedback)
    {
        $feedback->setCreatedAt(new \DateTimeImmutable());
    }
}
