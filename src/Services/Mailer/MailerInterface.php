<?php

namespace App\Services\Mailer;

interface MailerInterface
{
    public function send(Envelope $envelope): bool;
}
