#! /bin/bash

CORE_DIR=$1
SOLR_DIR=$2
SOLR_DATA=$3

mkdir -p ${SOLR_DATA}/${CORE_DIR}
cp -r ${SOLR_DIR}/../server/solr/configsets/_default/conf ${SOLR_DATA}/${CORE_DIR}

${SOLR_DIR}/solr create -c ${CORE_DIR}
