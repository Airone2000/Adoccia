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
        string $fromName, string $fromEmail,
        string $toName, string $toEmail,
        string $subject,
        string $message
    )
    {
        $this->fromName = $fromName;
        $this->fromEmail = $fromEmail;
        $this->toName = $toName;
        $this->toEmail = $toEmail;
        $this->subject = $subject;
        $this->message = $message;
    }
}