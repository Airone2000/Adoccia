<?php

namespace App\Security;

final class Permissions
{
    const

        APP_LOGIN = 0.1,

        CATEGORY_LIST = 1.1,
        CATEGORY_CREATE = 1.2

    ;


    public static function getConstants(): array
    {
        $rClass = new \ReflectionClass(__CLASS__);
        return $rClass->getConstants();
    }
}