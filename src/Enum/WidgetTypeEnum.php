<?php

namespace App\Enum;

final class WidgetTypeEnum extends AbstractEnum
{
    const DEFAULT_TYPE = 'empty';
    const EMPTY = 'empty';
    const LABEL = 'label';
    const STRING = 'string';
    const TEXT = 'text';
    const INT = 'int';
    const FLOAT = 'float';
    const DATE = 'date';
    const TIME = 'time';
    const RADIO = 'radio';
    const BUTTON = 'button';
    const EMAIL = 'email';
    const MAP = 'map';
    const FICHE_CREATOR = 'ficheCreator';
    const VIDEO = 'video';
    const PICTURE = 'picture';

    public static function getGroupedWidgetTypes(): array
    {
        return [
            'nonClassed' => [self::EMPTY, self::LABEL],
            'textual' => [self::STRING, self::TEXT, self::EMAIL],
            'number' => [self::INT, self::FLOAT],
            'time' => [self::DATE, self::TIME],
            'choices' => [self::RADIO],
            'misc' => [self::BUTTON, self::MAP, self::FICHE_CREATOR, self::VIDEO, self::PICTURE],
        ];
    }

    public static function nonWritableType(): array
    {
        return [
            self::EMPTY,
            self::LABEL,
        ];
    }
}
