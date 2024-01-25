<?php

namespace Abhiaay\QueryCraft\Enum;

enum Operation: string
{
        // Has Operation
    case IS = 'is';
    case IS_NOT = '!is';
    case LIKE = 'like';
    case NOT_LIKE = '!like';
    case GREATER_THAN = 'gt';
    case GREATER_THAN_EQUAL = 'gte';
    case LOWER_THAN = 'lt';
    case LOWER_THAN_EQUAL = 'lte';
    case MODULO = 'mod';
    case REGEX = 'regex';
    case EXISTS = 'exists';
    case TYPE = 'type';

        // Not has Operation
    case IN = 'in';
    case NOT_IN = '!in';
    case BETWEEN = 'between';

    public function getOperation(): string
    {
        return match ($this) {
            self::IS => '=',
            self::IS_NOT => '<>',
            self::LIKE => 'like',
            self::NOT_LIKE => 'not like',
            self::GREATER_THAN => '>',
            self::GREATER_THAN_EQUAL => '>=',
            self::LOWER_THAN => '<',
            self::LOWER_THAN_EQUAL => '<=',
            self::MODULO => 'mod',
            self::REGEX => 'regexp',
            self::EXISTS => 'exists',
            self::TYPE => 'type',
            default => null,
        };
    }
}
