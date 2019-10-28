<?php

namespace App\Enum;

final class SearchCriteriaEnum extends AbstractEnum
{
    const
        IS_NULL             = 'IS_NULL',
        IS_NOT_NULL         = 'IS_NOT_NULL',

        EXACT               = 'EXACT',

        CONTAINS            = 'CONTAINS',
        STARTS_WITH         = 'STARTS_WITH',
        ENDS_WITH           = 'ENDS_WITH',

        GREATER_THAN        = 'GREATER_THAN',
        LOWER_THAN          = 'LOWER_THAN',
        IS_POSITIVE         = 'IS_POSITIVE',
        IS_NEGATIVE         = 'IS_NEGATIVE'
    ;
}