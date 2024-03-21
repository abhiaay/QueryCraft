<?php

namespace Abhiaay\QueryCraft\Enum;

enum Cast
{
    case INTEGER;
    case STRING;
    case DOUBLE;

    public function cast($value)
    {
        return match($this) {
            self::INTEGER => (int) $value,
            self::STRING => (string) $value,
            self::DOUBLE => (double) $value
        };
    }
}
