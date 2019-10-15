<?php

namespace App\Services\Mailer\EmailSolutions;

interface EmailSolutionInterface
{
    public function send(string $fromName, string $fromEmail, string $toName, string $toEmail, string $subject, string $message): bool;
}