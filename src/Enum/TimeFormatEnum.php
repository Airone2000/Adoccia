<?php

namespace App\Enum;

final class TimeFormatEnum extends AbstractEnum
{
    const
        HHMM = 'HH:MM',
        HHMMSS = 'HH:MM:SS'
    ;

    const DEFAULT_TIME_FORMAT = self::HHMM;

    public static $mapJsDateFormatToOtherDateFormat = [
        self::HHMM  => [
            'php' => 'H:i',
            'sql' => '%H:%i'
        ],
        self::HHMMSS => [
            'php' => 'H:i:s',
            'sql' => '%H:%i:%s'
        ]
    ];


    public static function toArray(): array
    {
        $data = parent::toArray();
        unset($data['DEFAULT_TIME_FORMAT']);
        return $data;
    }
}