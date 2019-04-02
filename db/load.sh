#!/bin/sh

BASE_DIR=$(dirname "$(readlink -f "$0")")
if [ "$1" != "test" ]; then
    psql -h localhost -U netflox -d netflox < $BASE_DIR/netflox.sql
fi
# psql -h localhost -U netflox -d netflox_test < $BASE_DIR/netflox.sql
