<?php

namespace App\Enum;

final class WidgetTypeEnum extends AbstractEnum
{
    const
        DEFAULT_TYPE = 'empty',

        EMPTY = 'empty',
        LABEL = 'label',
        STRING = 'string',
        TEXT = 'text',
        INT = 'int',
        FLOAT = 'float',
        DATE = 'date'
    ;

    public static function getGroupedWidgetTypes(): array
    {
        return [
            'nonClassed' => [self::EMPTY, self::LABEL],
            'textual' => [self::STRING, self::TEXT],
            'number' => [self::INT, self::FLOAT],
            'time' => [self::DATE]
        ];
    }

    public static function nonWritableType(): array
    {
        return [
            self::EMPTY,
            self::LABEL
        ];
    }
}