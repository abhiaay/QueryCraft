<?php

namespace Abhiaay\QueryCraft;

use Abhiaay\QueryCraft\Enum\Operation;
use Abhiaay\QueryCraft\Enum\Sort;
use Illuminate\Http\Request;

/**
 * @method static Craft parse(Request $request)
 */
class Craft
{
    private static $instances = [];

    private static array $filterValues = [];

    private static array $sortValues = [];

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    protected function __construct()
    {
    }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone()
    {
    }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance(): Craft
    {
        $cls = static::class;

        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    protected function parse(Request $request)
    {
        // make empty the from previous
        self::$filterValues = [];
        self::$sortValues = [];

        if ($request->filled('filter')) {

            $filters = $request->input('filter');

            foreach ($filters as $column => $filter) {
                foreach ($filter as $operator => $value) {
                    $operation = Operation::tryFrom($operator);

                    if (in_array($operation, Operation::arrayable())) {
                        $value = explode(',', $value);
                    }

                    $this->addFilterValue(new FilterValue(
                        $column,
                        $operation,
                        $value
                    ));
                }
            }
        }

        if ($request->filled('sort')) {
            $sorts = $request->input('sort');
            if (is_array($sorts)) {
                throw new \Exception('Craft::parse() - sort cant be array must string');
            }

            $sorts = explode(',', $sorts);

            foreach ($sorts as $sort) {

                $sortColumn = explode('-', $sort);
                if (count($sortColumn) < 2) {
                    $column = $sort;
                    $sort = Sort::ASC;
                } else {
                    $sort = Sort::DESC;
                    $column = $sortColumn[1];
                }

                $this->addSortValue(new SortValue($column, $sort));
            }
        }

        return $this->getInstance();
    }

    public function addFilterValue(FilterValue $filterValue): void
    {
        self::$filterValues[] = $filterValue;
    }

    public function addSortValue(SortValue $sortValue): void
    {
        self::$sortValues[] = $sortValue;
    }

    public function getFilterValues(): array
    {
        return self::$filterValues;
    }

    public function getSortValues(): array
    {
        return self::$sortValues;
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array(array(self::getInstance(), $name), $arguments);
    }
}
