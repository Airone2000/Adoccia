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
        INT = 'int'
    ;

    public static function getGroupedWidgetTypes(): array
    {
        return [
            'nonClassed' => [self::EMPTY, self::LABEL],
            'textual' => [self::STRING, self::TEXT],
            'number' => [self::INT]
        ];
    }
}