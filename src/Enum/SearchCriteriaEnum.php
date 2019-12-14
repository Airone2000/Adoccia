<?php

namespace App\Enum;

final class SearchCriteriaEnum extends AbstractEnum
{
    const

        DISABLED = 'DISABLED';
    const

        IS_NULL = 'IS_NULL';
    const

        IS_NOT_NULL = 'IS_NOT_NULL';
    const

        EXACT = 'EXACT';
    const

        CONTAINS = 'CONTAINS';
    const

        STARTS_WITH = 'STARTS_WITH';
    const

        ENDS_WITH = 'ENDS_WITH';
    const

        EQUAL_TO = 'EQUAL_TO';
    const

        GREATER_THAN = 'GREATER_THAN';
    const

        LOWER_THAN = 'LOWER_THAN';
    const

        IS_POSITIVE = 'IS_POSITIVE';
    const

        IS_NEGATIVE = 'IS_NEGATIVE';
    const

        BETWEEN = 'BETWEEN';
    const

        // Specific to date
        YEAR_EQUAL_TO = 'YEAR_EQUAL_TO';
    const

        YEAR_GREATER_THAN = 'YEAR_GREATER_THAN';
    const

        YEAR_LESS_THAN = 'YEAR_LESS_THAN';
    const

        YEAR_BETWEEN = 'YEAR_BETWEEN';
    const

        MONTH_EQUAL_TO = 'MONTH_EQUAL_TO';
    const

        MONTH_GREATER_THAN = 'MONTH_GREATER_THAN';
    const

        MONTH_LESS_THAN = 'MONTH_LESS_THAN';
    const

        MONTH_BETWEEN = 'MONTH_BETWEEN';
    const

        DAY_EQUAL_TO = 'DAY_EQUAL_TO';
    const

        DAY_GREATER_THAN = 'DAY_GREATER_THAN';
    const

        DAY_LESS_THAN = 'DAY_LESS_THAN';
    const

        DAY_BETWEEN = 'DAY_BETWEEN';
    const

        // Specific to time
        TIME_EQUAL_TO = 'TIME_EQUAL_TO';
    const

        TIME_GREATER_THAN = 'TIME_GREATER_THAN';
    const

        TIME_LOWER_THAN = 'TIME_LOWER_THAN';
    const

        TIME_BETWEEN = 'TIME_BETWEEN';
    const

        HOUR_EQUAL_TO = 'HOUR_EQUAL_TO';
    const

        HOUR_LESS_THAN = 'HOUR_LESS_THAN';
    const

        HOUR_GREATER_THAN = 'HOUR_GREATER_THAN';
    const

        HOUR_BETWEEN = 'HOUR_BETWEEN';
    const

        MINUTE_EQUAL_TO = 'MINUTE_EQUAL_TO';
    const

        MINUTE_LESS_THAN = 'MINUTE_LESS_THAN';
    const

        MINUTE_GREATER_THAN = 'MINUTE_GREATER_THAN';
    const

        MINUTE_BETWEEN = 'MINUTE_BETWEEN';
    const

        SECOND_EQUAL_TO = 'SECOND_EQUAL_TO';
    const

        SECOND_LESS_THAN = 'SECOND_LESS_THAN';
    const

        SECOND_GREATER_THAN = 'SECOND_GREATER_THAN';
    const

        SECOND_BETWEEN = 'SECOND_BETWEEN';
    const

        // The value contains those values (and maybe more)
        IN_ARRAY = 'IN_ARRAY';
    const

        // The Value does not contain those values
        NOT_IN_ARRAY = 'NOT_IN_ARRAY';
    const

        // The value has exactly those values (and no more)
        IN_ARRAY_EXACT = 'IN_ARRAY_EXACT';
    const

        // Specific to button
        BUTTON_LABEL_IS_NULL = 'BUTTON_LABEL_IS_NULL';
    const

        BUTTON_LABEL_IS_NOT_NULL = 'BUTTON_LABEL_IS_NOT_NULL';
    const

        BUTTON_LABEL_EQUAL_TO = 'BUTTON_LABEL_EQUAL_TO';
    const

        BUTTON_LABEL_NOT_EQUAL_TO = 'BUTTON_LABEL_NOT_EQUAL_TO';
    const

        BUTTON_LABEL_CONTAINS = 'BUTTON_LABEL_CONTAINS';
    const

        BUTTON_LABEL_NOT_CONTAINS = 'BUTTON_LABEL_NOT_CONTAINS';
    const

        BUTTON_TARGET_IS_NULL = 'BUTTON_TARGET_IS_NULL';
    const

        BUTTON_TARGET_IS_NOT_NULL = 'BUTTON_TARGET_IS_NOT_NULL';
    const

        BUTTON_TARGET_EQUAL_TO = 'BUTTON_TARGET_EQUAL_TO';
    const

        BUTTON_TARGET_NOT_EQUAL_TO = 'BUTTON_TARGET_NOT_EQUAL_TO';
    const

        BUTTON_TARGET_CONTAINS = 'BUTTON_TARGET_CONTAINS';
    const

        BUTTON_TARGET_NOT_CONTAINS = 'BUTTON_TARGET_NOT_CONTAINS';
    const

        // Specific to FicheCreator
        CREATOR_IS = 'CREATOR_IS';
    const

        CREATOR_IS_NOT = 'CREATOR_IS_NOT';
    const

        // Specific to map
        MAP_AROUND = 'MAP_AROUND'
    ;
}
