<?php

namespace App\Enum;

final class DateFormatEnum extends AbstractEnum
{
    const
        DDMMYYYY = 'dd/mm/yyyy';
    const
        DDMMYY = 'dd/mm/yy';
    const
        MMDDYYYY = 'mm/dd/yyyy';
    const
        MMDDYY = 'mm/dd/yy'
    ;

    private static $mapJsDateFormatToPHPDateFormat = [
        self::DDMMYYYY => 'd/m/Y',
        self::DDMMYY => 'd/m/y',
        self::MMDDYYYY => 'm/d/Y',
        self::MMDDYY => 'm/d/y',
    ];

    const DEFAULT_DATE_FORMAT = self::DDMMYYYY;

    public static function getPHPFormatForJsFormat(string $format): string
    {
        $format = self::$mapJsDateFormatToPHPDateFormat[$format] ?? null;
        if (null === $format) {
            throw new \LogicException("No PHP date format defined for {$format} in ".__CLASS__);
        }

        return $format;
    }

    public static function toArray(): array
    {
        $data = parent::toArray();
        unset($data['DEFAULT_DATE_FORMAT']);

        return $data;
    }
}
