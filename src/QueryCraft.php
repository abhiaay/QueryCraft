<?php

namespace Abhiaay\QueryCraft;

use Abhiaay\QueryCraft\Enum\Operation;
use Illuminate\Database\Eloquent\Builder;
use Abhiaay\QueryCraft\Enum\Cast;

/**
 * @method static \Illuminate\Database\Eloquent\Builder craft(\Abhiaay\Filterable\Craft $craft)
 */
trait QueryCraft
{
    /**
     * @return [alias => column_name]
     */
    abstract public function filterableColumns(): array;

    /**
     * @return [alias => column_name]
     */
    abstract public function sortableColumns(): array;

    /**
     * cast value to given cast
     *
     * @return array<string,Cast>
     */
    public function castsQueryCraft(): array
    {
        return [];
    }

    /**
     * @todo add capabilities for and & or operation
     */
    public function scopeCraft(Builder $query, Craft $craft): Builder
    {
        $query = $this->filter($query, $craft);

        $query = $this->sort($query, $craft);

        return $query;
    }

    private function filter(Builder $query, Craft $craft): Builder
    {
        $filters = $craft->getFilterValues();

        // return immedietly if not has any filters
        if (empty($filters)) {
            return $query;
        }

        $filterableColumns = collect($this->filterableColumns());

        /**
         * @var FilterValue $filterValue
         */
        foreach ($filters as $filterValue) {

            // Check if the field is valid
            if (!$filterableColumn = $filterableColumns->get($filterValue->column)) {
                continue;
            }

            // Check the operator
            $operator = Operation::IS;
            if (isset($filterValue->operation)) {
                $operator = $filterValue->operation;
            }

            $value = $this->casting($filterValue);

            // Build the query
            switch ($operator) {
                case Operation::IS:
                case Operation::IS_NOT:
                case Operation::LIKE:
                case Operation::NOT_LIKE:
                case Operation::GREATER_THAN:
                case Operation::GREATER_THAN_EQUAL:
                case Operation::LOWER_THAN:
                case Operation::LOWER_THAN_EQUAL:
                case Operation::MODULO:
                case Operation::REGEX:
                case Operation::EXISTS:
                case Operation::TYPE:
                    $query->where($filterableColumn, $operator->getOperation(), $value);
                    break;
                case Operation::IN:
                    $query->whereIn($filterableColumn, $value);
                    break;
                case Operation::NOT_IN:
                    $query->whereNotIn($filterableColumn, $value);
                    break;
                case Operation::BETWEEN:
                    $query->whereBetween($filterableColumn, $value);
                    break;
            }
        }

        return $query;
    }

    private function sort(Builder $query, Craft $craft): Builder
    {
        $sorts = $craft->getSortValues();

        $sortableColumns = collect($this->sortableColumns());

        /**
         * @var SortValue $sort
         */
        foreach ($sorts as $sort) {

            // Check if the field is valid
            if (!$sortableColumn = $sortableColumns->get($sort->column)) {
                continue;
            }

            $query->orderBy($sortableColumn, $sort->sort->value);
        }

        return $query;
    }

    private function casting(FilterValue $filterValue): mixed
    {
        $casts = $this->castsQueryCraft();
        if($casts && !empty($casts) && in_array($filterValue->column, array_keys($casts))) {
            /**
             * @var Cast
             */
            $cast = $casts[$filterValue->column];
            return $cast->cast($filterValue->value);
        }
        return $filterValue->value;
    }
}
