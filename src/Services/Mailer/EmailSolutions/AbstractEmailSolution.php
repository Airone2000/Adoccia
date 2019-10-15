<?php

namespace App\Services\Mailer\EmailSolutions;

use Html2Text\Html2Text;

abstract class AbstractEmailSolution implements EmailSolutionInterface
{
    protected function getPlainTextMessage(string $message): string
    {
        $html2text = new Html2Text($message);
        return $html2text->getText();
    }
}