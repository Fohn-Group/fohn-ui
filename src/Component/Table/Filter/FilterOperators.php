<?php

declare(strict_types=1);
/**
 *  Supported operator by Table Filter.
 */

namespace Fohn\Ui\Component\Table\Filter;

class FilterOperators
{
    public const TEXT_CONTAINS = 'contains';
    public const TEXT_NOT_CONTAINS = 'notContains';
    public const TEXT_STARTS_WITH = 'startsWith';
    public const TEXT_NOT_STARTS_WITH = 'notStartsWith';
    public const TEXT_ENDS_WITH = 'endsWith';
    public const TEXT_NOT_ENDS_WITH = 'notEndsWith';
    public const TEXT_EQUALS = 'equals';
    public const EQUAL = '=';
    public const GREATER = '>';
    public const GREATER_EQUAL = '>=';
    public const LESS = '<';
    public const LESS_EQUAL = '<=';
    public const NOT_EQUAL = '!=';
    public const IN = 'IN';
    public const NOT_IN = 'NOT IN';
    public const LIKE = 'LIKE';
    public const NOT_LIKE = 'NOT LIKE';
    public const IS = 'is';
    public const IS_NOT = 'isNot';
    public const IS_AFTER = 'isAfter';
    public const IS_ON_OR_AFTER = 'isOnOrAfter';
    public const IS_BEFORE = 'isBefore';
    public const IS_ON_OR_BEFORE = 'isOnOrBefore';
    public const IS_EMPTY = 'isEmpty';
    public const IS_NOT_EMPTY = 'isNotEmpty';
    public const IS_ANY_OF = 'isAnyOf';
}
