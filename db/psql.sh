#!/bin/sh

[ "$1" = "test" ] && BD="_test"
psql -h localhost -U netflox -d netflox$BD
