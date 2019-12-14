<?php

namespace App\Services\Mailer\EmailSolutions;

use Mailjet\Client;
use Mailjet\Resources;

final class MailjetEmailSolution extends AbstractEmailSolution
{
    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client(
            $_SERVER['MAILJET_API_KEY'],
            $_SERVER['MAILJET_SECRET_KEY'],
            true, ['version' => 'v3.1'])
        ;
    }

    public function send(string $fromName, string $fromEmail, string $toName, string $toEmail, string $subject, string $message): bool
    {
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $fromEmail,
                        'Name' => $fromName,
                    ],
                    'To' => [
                        [
                            'Email' => $toEmail,
                            'Name' => $toName,
                        ],
                    ],
                    'Subject' => $subject,
                    'TextPart' => $this->getPlainTextMessage($message),
                    'HTMLPart' => $message,
                ],
            ],
        ];

        $response = $this->client->post(Resources::$Email, ['body' => $body]);

        return $response->success();
    }
}
