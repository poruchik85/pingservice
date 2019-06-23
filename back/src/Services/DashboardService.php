<?php

namespace Services;

class DashboardService
{
    /**
     * @var Connector $connector
     */
    protected $connector;

    /**
     * @param Connector $connector
     */
    public function __construct(Connector $connector) {
        $this->connector = $connector;
    }

    /**
     * @return array
     */
    public function getData() {
        $query = <<<sql
select 
    id,
    name,
    created_at
from
    groups
order by created_at asc
;
sql;
        $groups = $this->connector->execute($query);

        $query = <<<sql
select 
    id,
    name,
    ip,
    created_at,
    group_id
from
    servers
order by created_at asc
;
sql;
        $servers = $this->connector->execute($query);

        $query = <<<sql
select 
    id,
    created_at,
    server_id,
    success
from
    pings
order by created_at asc
;
sql;
        $pings = $this->connector->execute($query);

        $servers = array_map(function ($server) use ($pings) {
            $server["pings"] = array_values(array_filter($pings, function ($ping) use ($server) {
                return $ping["server_id"] === $server["id"];
            }));

            return $server;
        }, $servers);

        $groups = array_map(function ($group) use ($servers) {
            $group["servers"] = array_values(array_filter($servers, function ($server) use ($group) {
                return $server["group_id"] === $group["id"];
            }));

            return $group;
        }, $groups);

        return $groups;
    }

    public function ping() {

    }
}
