#! /bin/bash

CORE=$1
PORT=$2

curl -s -X POST -H 'Content-type:application/json' --data-binary '{"delete-field" : { "name":"id" }}' http://localhost:$PORT/solr/$CORE/schema

