#!/bin/bash


create_db() {
psql -v ON_ERROR_STOP=1  <<-EOSQL
    CREATE DATABASE $1;
    GRANT ALL PRIVILEGES ON DATABASE $1 TO postgres;
    UPDATE pg_database SET datallowconn = true WHERE datname = '$1';
EOSQL
}
################

create_db $DB_NAME;
create_db $DB_NAME_test;
