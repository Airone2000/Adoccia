<?php

namespace App\Services\Mailer;

use App\Services\Mailer\EmailSolutions\EmailSolutionInterface;

final class Mailer implements MailerInterface
{
    /**
     * @var EmailSolutionInterface
     */
    private $emailSolution;

    public function __construct()
    {
        $this->emailSolution = $this->getEmailSolution();
    }

    public function send(Envelope $envelope, $logError = true): bool
    {
        $sent = $this->emailSolution->send(
            $envelope->fromName, $envelope->fromEmail,
            $envelope->toName, $envelope->toEmail,
            $envelope->subject,
            $envelope->message
        );

        if ($sent === false) {
            # Log in db for sending later
        }

        return $sent;
    }

    private function getEmailSolution(): EmailSolutionInterface
    {
        $emailSolutionClassName = $_SERVER['EMAIL_SOLUTION'];
        if (!is_string($emailSolutionClassName)) {
            throw new \LogicException('Environment variable EMAIL_SOLUTION is not properly set.');
        }

        $emailSolutionClassName = 'App\Services\Mailer\EmailSolutions\\' . ucfirst(strtolower($emailSolutionClassName)) . 'EmailSolution';
        if (class_exists($emailSolutionClassName)) {
            $this->emailSolution = new $emailSolutionClassName();
            return $this->emailSolution;
        }
        else {
            throw new \LogicException("Unable to create an instance of {$emailSolutionClassName}");
        }
    }
}