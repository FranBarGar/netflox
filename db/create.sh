#!/bin/sh

if [ "$1" = "travis" ]; then
    psql -U postgres -c "CREATE DATABASE netflox_test;"
    psql -U postgres -c "CREATE USER netflox PASSWORD 'netflox' SUPERUSER;"
else
    sudo -u postgres dropdb --if-exists netflox
    sudo -u postgres dropdb --if-exists netflox_test
    sudo -u postgres dropuser --if-exists netflox
    sudo -u postgres psql -c "CREATE USER netflox PASSWORD 'netflox' SUPERUSER;"
    sudo -u postgres createdb -O netflox netflox
    sudo -u postgres psql -d netflox -c "CREATE EXTENSION pgcrypto;" 2>/dev/null
    sudo -u postgres createdb -O netflox netflox_test
    sudo -u postgres psql -d netflox_test -c "CREATE EXTENSION pgcrypto;" 2>/dev/null
    LINE="localhost:5432:*:netflox:netflox"
    FILE=~/.pgpass
    if [ ! -f $FILE ]; then
        touch $FILE
        chmod 600 $FILE
    fi
    if ! grep -qsF "$LINE" $FILE; then
        echo "$LINE" >> $FILE
    fi
fi
