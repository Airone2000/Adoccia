<?php

namespace App\Enum;

class AbstractEnum
{
    protected static $enumAsArray = [];

    public static function isset($needle): bool
    {
        $haystack = self::toArray();

        return \in_array($needle, $haystack, true);
    }

    public static function toArray(): array
    {
        $calledClass = static::class;
        if (empty(self::$enumAsArray[$calledClass])) {
            $reflection = new \ReflectionClass($calledClass);
            $result = $reflection->getConstants();
            self::$enumAsArray[$calledClass] = $result;
        }

        return self::$enumAsArray[$calledClass];
    }
}
