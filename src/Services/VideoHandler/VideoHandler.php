<?php

namespace App\Services\VideoHandler;

final class VideoHandler implements VideoHandlerInterface
{
    const SUPPORTED = [
        'Youtube' => '#^https:\/\/youtu\.be\/[a-z0-9\-]+$#i',
    ];

    public static function isSupported(string $url, &$provider = null): bool
    {
        foreach (self::SUPPORTED as $provider => $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }

        return false;
    }

    public static function transformToReadableVideoURL(string $url): ?string
    {
        if (self::isSupported($url, $provider)) {
            switch ($provider) {
                case 'Youtube':
                    return self::transformYoutube($url);
                    break;
            }
        }

        return null;
    }

    private static function transformYoutube(string $url): string
    {
        $urlParts = explode('/', $url);
        $videoID = array_pop($urlParts);

        return "https://www.youtube.com/embed/{$videoID}";
    }
}
