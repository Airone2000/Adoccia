<?php

namespace App\Services\VideoHandler;

interface VideoHandlerInterface
{
    public static function isSupported(string $url): bool;
}