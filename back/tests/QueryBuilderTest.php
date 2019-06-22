<?php

use PHPUnit\Framework\TestCase;
use Services\QueryBuilder;

class QueryBuilderTest extends TestCase
{
    public function conditionsDataProvider() {
        return [
            [[], "(true)"],
            [["id" => 1], "(id = 1)"],
            [["id" => [1, 2]], "((id = 1) or (id = 2))"],
            [[
                "and" => [
                    "id" => [1, 2],
                    "name" => "group",
                ]
            ], "(((id = 1) or (id = 2)) and (name = 'group'))"],
            [[
                "or" => [
                    "and" => [
                        "id" => 1,
                        "name" => "group",
                    ],
                    "name" => "group1",
                ]
            ], "(((id = 1) and (name = 'group')) or (name = 'group1'))"],
        ];
    }

    /**
     * @dataProvider conditionsDataProvider
     *
     * @param array $parameter
     * @param string $expected
     *
     * @throws ReflectionException
     */
    public function testGenerateConditionString(array $parameter, string $expected): void {
        $reflector = new ReflectionClass(QueryBuilder::class);
        $method = $reflector->getMethod("generateConditionsString");
        $method->setAccessible(true);

        $conditionString = $method->invokeArgs(null, [$parameter]);

        $this->assertEquals(
            $conditionString,
            $expected
        );
    }

    public function testSimpleSelect(): void
    {
        $query = QueryBuilder::select(
            "groups",
            ["id" => 1]
        );

        $this->assertEquals(
            "select * from groups where (id = 1) order by id asc;",
            $query
        );
    }

    public function testFullSelect(): void
    {
        $query = QueryBuilder::select(
            "groups",
            [
                "or" => [
                    "and" => [
                        "id" => 1,
                        "name" => "group",
                    ],
                    "name" => "group1",
                ]
            ],
            "created_at",
            "desc",
            5,
            1,
            ["name", "created_at"]
        );

        $this->assertEquals(
            "select name,created_at from groups where (((id = 1) and (name = 'group')) or (name = 'group1')) order by created_at desc limit 5 offset 1;",
            $query
        );
    }

    public function testSimpleInsert(): void
    {
        $query = QueryBuilder::insert(
            "groups",
            [
                "id" => 23,
                "name" => "group1",
                "created_at" => "2019-06-20 13:04:44",
            ]
        );

        $this->assertEquals(
            "insert into groups (id,name,created_at) values (23,'group1','2019-06-20 13:04:44');",
            $query
        );
    }

    public function testSimpleUpdate(): void
    {
        $query = QueryBuilder::update(
            "groups",
            ["id" => [1, 2]],
            [
                "id" => 23,
                "name" => "group1",
                "created_at" => "2019-06-20 13:04:44",
            ]
        );

        $this->assertEquals(
            "update groups set id = 23,name = 'group1',created_at = '2019-06-20 13:04:44' where ((id = 1) or (id = 2));",
            $query
        );
    }
}
