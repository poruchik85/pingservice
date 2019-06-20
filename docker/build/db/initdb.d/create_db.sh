#!/bin/bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
	CREATE USER vladis WITH SUPERUSER LOGIN PASSWORD 'vladis';
	CREATE DATABASE vladis;
	GRANT ALL PRIVILEGES ON DATABASE vladis TO vladis;
EOSQL

psql -v ON_ERROR_STOP=1 --username "vladis" --dbname "vladis" <<-EOSQL
	create table groups
    (
        id serial not null constraint groups_pkey primary key,
        name varchar(255),
        created_at timestamp(0)
    );

    alter table groups owner to vladis;

    create table servers
    (
        id serial not null constraint servers_pkey primary key,
        host varchar(255),
        ip varchar(40),
        created_at timestamp(0),
        group_id integer not null constraint servers_group_id_foreign references groups on delete cascade
    );

    alter table servers owner to vladis;

    create index servers_id_groups_id_index on servers (id, group_id);

    create table pings
    (
        id serial not null constraint pings_pkey primary key,
        created_at timestamp(0),
        server_id integer not null constraint pings_server_id_foreign references servers
    );

    alter table pings owner to vladis;

    create index pings_id_servers_id_index on pings (id, server_id);
EOSQL

