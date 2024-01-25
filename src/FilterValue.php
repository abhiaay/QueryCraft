<?php

namespace Abhiaay\QueryCraft;

use Abhiaay\QueryCraft\Enum\Operation;

class FilterValue
{
    public function __construct(
        public readonly string $column,
        public readonly Operation $operation,
        public readonly string|array|bool $value
    ) {
    }
}
