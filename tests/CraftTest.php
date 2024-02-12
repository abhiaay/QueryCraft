<?php

declare(strict_types=1);

namespace Abhiaay\Test;

use Abhiaay\QueryCraft\Craft;
use Abhiaay\QueryCraft\Enum\Operation;
use Abhiaay\QueryCraft\Enum\Sort;
use Abhiaay\QueryCraft\FilterValue;
use Abhiaay\QueryCraft\SortValue;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class CraftTest extends TestCase
{
    public function test_parse_return_craft_instance()
    {
        $request = new Request();

        $this->assertInstanceOf(Craft::class, Craft::parse($request));
    }

    public function test_parse_has_valid_filter_and_sort()
    {
        $request = new Request();
        $request->merge([
            'filter' => [
                'name' => [
                    'is' => 'testing',
                    'regex' => 'asd'
                ]
            ],
            'sort' => 'name,-created_at'
        ]);
        $craft = Craft::parse($request);

        // check if values is not empty array
        $this->assertNotEmpty($craft->getSortValues());
        $this->assertNotEmpty($craft->getFilterValues());

        // initialize expected result
        $filterValues = [
            new FilterValue('name', Operation::IS, 'testing'),
            new FilterValue('name', Operation::REGEX, 'asd')
        ];

        $sortValues = [
            new SortValue('name', Sort::ASC),
            new SortValue('created_at', Sort::DESC)
        ];

        // check
        $this->assertEquals($craft->getSortValues(), $sortValues);
        $this->assertEquals($craft->getFilterValues(), $filterValues);
    }

    public function test_parse_filter_not_use_previous_craft_parse()
    {

        // first request
        $request = new Request();
        $request->merge([
            'filter' => [
                'name' => [
                    'is' => 'testing',
                    'regex' => 'asd'
                ]
            ],
            'sort' => 'name,-created_at'
        ]);
        Craft::parse($request);

        // second request without any filter
        $request = new Request();

        $craftSecond = Craft::parse($request);

        $this->assertEmpty($craftSecond->getFilterValues());
        $this->assertEmpty($craftSecond->getSortValues());
    }
}
