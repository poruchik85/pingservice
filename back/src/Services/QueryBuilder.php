<?php

namespace Services;

use LogicException;

class QueryBuilder
{
    private const ORDER_DIRECTIONS = [
        "asc",
        "desc",
    ];

    /**
     * @param string $table
     * @param array $conditions
     * @param string $orderField
     * @param string $orderDirection
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $selectedFields
     *
     * @return string
     */
    public static function select(
        string $table,
        array $conditions,
        string $orderField = "id",
        string $orderDirection = 'asc',
        int $limit = null,
        int $offset = null,
        array $selectedFields = null
    ): string {
        if (!in_array($orderDirection, self::ORDER_DIRECTIONS)) {
            throw new LogicException("Wrong order direction");
        }
        $conditionString = self::generateConditionsString($conditions);
        $selectedString = $selectedFields ? implode(",", $selectedFields) : "*";
        $orderString = sprintf("order by %s %s", $orderField, $orderDirection);
        $sliceString = sprintf(
            "%s%s",
            $limit && $limit > 0 ? sprintf(" limit %d", $limit): "",
            $offset && $offset >= 0 ? sprintf(" offset %d", $offset): ""
        );

        return sprintf(
            /** @lang text */ "select %s from %s where %s %s%s;",
            $selectedString,
            $table,
            $conditionString,
            $orderString,
            $sliceString
        );
    }

    /**
     * @param string $table
     * @param array $data
     *
     * @return string
     */
    public static function insert(string $table, array $data) {
        $fields = [];
        $values = [];

        foreach ($data as $field => $value) {
            $fields[] = $field;
            $values[] = is_string($value) ? sprintf("'%s'", $value) : $value;
        }

        return sprintf(
            "insert into %s (%s) values (%s);",
            $table,
            implode(',', $fields),
            implode(',', $values)
        );
    }

    public static function update(string $table, array $conditions, array $data)
    {
        $conditionString = self::generateConditionsString($conditions);
        $updationString = implode(",", array_map(function ($field, $value) {
            $value = is_string($value) ? sprintf("'%s'", $value) : $value;

            return sprintf("%s = %s", $field, $value);
        }, array_keys($data), $data));

        return sprintf(
            /** @lang text */ "update %s set %s where %s;",
            $table,
            $updationString,
            $conditionString
        );
    }

    /**
     * @param array $conditions
     *
     * @return string
     */
    private static function generateConditionsString(array $conditions): string {
        foreach ($conditions as $key => $condition) {
            if (!is_string($key)) {
                throw new LogicException("Wrong conditions for query");
            }

            switch ($key) {
                case "and":
                case "or":
                    if (!is_array($condition)) {
                        throw new LogicException("Wrong conditions for query");
                    }

                    return sprintf("(%s)", implode(sprintf(" %s ", $key), array_map(function ($k, $item) {
                        return self::generateConditionsString([$k => $item]);
                    }, array_keys($condition), $condition)));

                    break;
                default:
                    if (is_array($condition)) {
                        return sprintf("(%s)", implode(" or ", array_map(function ($item) use ($key) {
                            $item = is_string($item) ? sprintf("'%s'", $item) : $item;

                            return sprintf("(%s = %s)", $key, $item);
                        }, $condition)));
                    }

                    if (!is_string($condition) && !is_int($condition) && !is_float($condition)) {
                        throw new LogicException("Wrong conditions for query");
                    }

                    $condition = is_string($condition) ? sprintf("'%s'", $condition) : $condition;
                    return sprintf("(%s = %s)", $key, $condition);

                    break;
            }
        }

        return "(true)";
    }
}
