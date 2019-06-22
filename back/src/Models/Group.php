<?php

namespace Models;

/**
 * @property int|null id
 * @property string|null name
 * @property string|null created_at
 */
class Group extends Model
{
    protected const FIELDS = [
        "id",
        "name",
        "created_at",
    ];

    protected const REQUIRED_FIELDS = [
        "name"
    ];

    protected const IMMUTABLE_FIELDS = [
        "id"
    ];

    protected const TABLE_NAME = "groups";

    public function getServers() {
        $query = <<<sql
select * from vladis.public.groups;
sql;

        self::$connector->select($query);

    }
}
