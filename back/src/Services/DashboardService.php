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
    p.id
        as ping_id,
    p.success
        as success,
    p.created_at
        as ping_created_at,
    s.id
        as server_id,
    s.host
        as host,
    s.ip
        as ip,
    s.created_at
        as server_created_at,
    g.id
        as group_id,
    g.name
        as group_name,
    g.created_at
        as group_created_at
from
    pings p
    inner join servers s on p.server_id = s.id
    inner join groups g on s.group_id = g.id
order by g.created_at desc, s.created_at desc, p.created_at desc
;
sql;

        return $this->connector->execute($query, []);
    }
}
