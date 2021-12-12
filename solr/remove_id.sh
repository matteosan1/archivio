#! /bin/bash

CORE=$1
PORT=$2

curl -s -u solr:SolrRocks -k -cacert /opt/solr/server/etc/solr-ssl.pem  -X POST -H 'Content-type:application/json' --data-binary '{"delete-field" : { "name":"id" }}' https://localhost:$PORT/solr/$CORE/schema

