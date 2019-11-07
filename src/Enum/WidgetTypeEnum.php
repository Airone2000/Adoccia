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
        DATE = 'date',
        TIME = 'time',
        RADIO = 'radio',
        BUTTON = 'button',
        EMAIL = 'email',
        MAP = 'map',
        FICHE_CREATOR = 'ficheCreator'
    ;

    public static function getGroupedWidgetTypes(): array
    {
        return [
            'nonClassed' => [self::EMPTY, self::LABEL],
            'textual' => [self::STRING, self::TEXT, self::EMAIL],
            'number' => [self::INT, self::FLOAT],
            'time' => [self::DATE, self::TIME],
            'choices' => [self::RADIO],
            'misc' => [self::BUTTON, self::MAP, self::FICHE_CREATOR]
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