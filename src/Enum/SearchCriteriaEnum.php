<?php

namespace App\Enum;

final class SearchCriteriaEnum extends AbstractEnum
{
    const

        DISABLED            = 'DISABLED',

        IS_NULL             = 'IS_NULL',
        IS_NOT_NULL         = 'IS_NOT_NULL',

        EXACT               = 'EXACT',

        CONTAINS            = 'CONTAINS',
        STARTS_WITH         = 'STARTS_WITH',
        ENDS_WITH           = 'ENDS_WITH',

        EQUAL_TO            = 'EQUAL_TO',
        GREATER_THAN        = 'GREATER_THAN',
        LOWER_THAN          = 'LOWER_THAN',
        IS_POSITIVE         = 'IS_POSITIVE',
        IS_NEGATIVE         = 'IS_NEGATIVE',
        BETWEEN             = 'BETWEEN',

        # Specific to date
        YEAR_EQUAL_TO       = 'YEAR_EQUAL_TO',
        YEAR_GREATER_THAN   = 'YEAR_GREATER_THAN',
        YEAR_LESS_THAN      = 'YEAR_LESS_THAN',
        YEAR_BETWEEN        = 'YEAR_BETWEEN',
        MONTH_EQUAL_TO      = 'MONTH_EQUAL_TO',
        MONTH_GREATER_THAN  = 'MONTH_GREATER_THAN',
        MONTH_LESS_THAN     = 'MONTH_LESS_THAN',
        MONTH_BETWEEN       = 'MONTH_BETWEEN',
        DAY_EQUAL_TO        = 'DAY_EQUAL_TO',
        DAY_GREATER_THAN    = 'DAY_GREATER_THAN',
        DAY_LESS_THAN       = 'DAY_LESS_THAN',
        DAY_BETWEEN         = 'DAY_BETWEEN',

        # Specific to time
        TIME_EQUAL_TO       = 'TIME_EQUAL_TO',
        TIME_GREATER_THAN   = 'TIME_GREATER_THAN',
        TIME_LOWER_THAN     = 'TIME_LOWER_THAN',
        TIME_BETWEEN        = 'TIME_BETWEEN',
        HOUR_EQUAL_TO       = 'HOUR_EQUAL_TO',
        HOUR_LESS_THAN      = 'HOUR_LESS_THAN',
        HOUR_GREATER_THAN   = 'HOUR_GREATER_THAN',
        HOUR_BETWEEN        = 'HOUR_BETWEEN',
        MINUTE_EQUAL_TO     = 'MINUTE_EQUAL_TO',
        MINUTE_LESS_THAN    = 'MINUTE_LESS_THAN',
        MINUTE_GREATER_THAN = 'MINUTE_GREATER_THAN',
        MINUTE_BETWEEN      = 'MINUTE_BETWEEN',
        SECOND_EQUAL_TO     = 'SECOND_EQUAL_TO',
        SECOND_LESS_THAN    = 'SECOND_LESS_THAN',
        SECOND_GREATER_THAN = 'SECOND_GREATER_THAN',
        SECOND_BETWEEN      = 'SECOND_BETWEEN',

        # The value contains those values (and maybe more)
        IN_ARRAY            = 'IN_ARRAY',
        # The Value does not contain those values
        NOT_IN_ARRAY        = 'NOT_IN_ARRAY',
        # The value has exactly those values (and no more)
        IN_ARRAY_EXACT      = 'IN_ARRAY_EXACT',


        # Specific to button
        BUTTON_LABEL_IS_NULL = 'BUTTON_LABEL_IS_NULL',
        BUTTON_LABEL_IS_NOT_NULL = 'BUTTON_LABEL_IS_NOT_NULL',
        BUTTON_LABEL_EQUAL_TO = 'BUTTON_LABEL_EQUAL_TO',
        BUTTON_LABEL_NOT_EQUAL_TO = 'BUTTON_LABEL_NOT_EQUAL_TO',
        BUTTON_LABEL_CONTAINS = 'BUTTON_LABEL_CONTAINS',
        BUTTON_LABEL_NOT_CONTAINS = 'BUTTON_LABEL_NOT_CONTAINS',
        BUTTON_TARGET_IS_NULL = 'BUTTON_TARGET_IS_NULL',
        BUTTON_TARGET_IS_NOT_NULL = 'BUTTON_TARGET_IS_NOT_NULL',
        BUTTON_TARGET_EQUAL_TO = 'BUTTON_TARGET_EQUAL_TO',
        BUTTON_TARGET_NOT_EQUAL_TO = 'BUTTON_TARGET_NOT_EQUAL_TO',
        BUTTON_TARGET_CONTAINS = 'BUTTON_TARGET_CONTAINS',
        BUTTON_TARGET_NOT_CONTAINS = 'BUTTON_TARGET_NOT_CONTAINS'
    ;
}