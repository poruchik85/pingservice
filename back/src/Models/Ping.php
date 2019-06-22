<?php

namespace Models;

/**
 * @property int id
 * @property int server_id
 * @property string created_at
 * @property bool success
 */
class Ping extends Model
{
    protected const FIELDS = [
        "id",
        "server_id",
        "created_at",
        "success"
    ];

    protected const REQUIRED_FIELDS = [
        "server_id",
    ];

    protected const IMMUTABLE_FIELDS = [
        "id"
    ];

    protected const TABLE_NAME = "pings";
}





