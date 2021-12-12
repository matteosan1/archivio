#! /bin/bash

CORE_DIR=$1
SOLR_DIR=$2
SOLR_DATA=$3

mkdir -p ${SOLR_DATA}/${CORE_DIR}

if [ ! -f ${SOLR_DATA}/solr.xml ]; then
    cp ${SOLR_DIR}/server/solr/security.json ${SOLR_DATA}/.
    cp ${SOLR_DIR}/server/solr/solr.xml ${SOLR_DATA}/.
fi

cp -r ${SOLR_DIR}/server/solr/configsets/_default/conf ${SOLR_DATA}/${CORE_DIR}

${SOLR_DIR}/bin/solr create -c ${CORE_DIR}
