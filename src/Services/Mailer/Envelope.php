<?php

namespace App\Services\Mailer;

class Envelope
{
    /**
     * @var string
     */
    public $fromName;
    /**
     * @var string
     */
    public $fromEmail;
    /**
     * @var string
     */
    public $toName;
    /**
     * @var string
     */
    public $toEmail;
    /**
     * @var string
     */
    public $subject;
    /**
     * @var string
     */
    public $message;

    public function __construct(
        string $toName, string $toEmail,
        string $subject,
        string $message,
        ?string $fromName = null, ?string $fromEmail = null
    ) {
        $this->fromName = $fromName ?? $_SERVER['EMAIL_SENDER_NAME'];
        $this->fromEmail = $fromEmail ?? $_SERVER['EMAIL_SENDER_EMAIL'];
        $this->toName = $toName;
        $this->toEmail = $toEmail;
        $this->subject = $subject;
        $this->message = $message;
    }
}
