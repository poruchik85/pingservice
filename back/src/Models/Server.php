<?php

namespace Models;

/**
 * @property int id
 * @property string ip
 * @property string host
 * @property int group_id
 * @property string created_at
 */
class Server extends Model
{
    protected const FIELDS = [
        "id",
        "ip",
        "host",
        "group_id",
        "created_at",
    ];

    protected const REQUIRED_FIELDS = [
        "ip",
        "group_id",
    ];

    protected const IMMUTABLE_FIELDS = [
        "id"
    ];

    protected const TABLE_NAME = "servers";
}





