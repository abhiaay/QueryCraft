<?php

namespace Abhiaay\QueryCraft;

use Abhiaay\QueryCraft\Enum\Sort;

class SortValue
{
    public function __construct(
        public readonly string $column,
        public readonly Sort $sort
    ) {
    }
}
