#! /usr/bin/bash

CORE=$1
PORT=$2

curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"nome_cognome","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"ruolo","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"evento","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema

curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"committente","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"ricorrenza","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"dedica","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"dimensioni","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"curatore","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema

curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"categoria","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"tecnica","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema


curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"data","type":"pdate","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"tipo_delibera","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"argomento_breve","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"testo","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"unanimita","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"favorevoli","type":"pint","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"contrari","type":"pint","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"astenuti","type":"pint","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"straordinaria","type":"pint","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"capitolo","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"pagina","type":"pint","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"num_contestuale","type":"pint","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema


curl -X POST -H 'Content-type:application/json' --data-binary '{"delete-field" : { "name":"id" }}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"codice_$CORE","type":"string","docValues":false,"multiValued":false,"indexed":true,"stored":true, required:true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"update-field":{"name":"anno","type":"pint","docValues":true,"multiValued":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/prova5/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"private","type":"pint","docValues":true,"multiValued":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"cdd","type":"string","docValues":false,"multiValued":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"timestamp","type":"pdate","docValues":false,"multiValued":false,"indexed":true,"stored":true,"default":"NOW"}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"tipologia","type":"string","docValues":false,"multiValued":false,"indexed":true,"stored":true, required:true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"titolo","type":"text_general","docValues":false,"multiValued":false,"indexed":true,"stored":true, required:true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"sottotitolo","type":"text_general","docValues":false,"multiValued":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"prima_responsabilita","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"altre_responsabilita","type":"text_general","multiValued":false,"docValues":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"soggetto","type":"text_general","docValues":false,"multiValued":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"luogo","type":"string","docValues":false,"multiValued":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"edizione","type":"text_general","docValues":false,"multiValued":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"ente","type":"text_general","docValues":false,"multiValued":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"serie","type":"text_general","docValues":false,"multiValued":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"descrizione","type":"text_general","docValues":false,"multiValued":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
curl -X POST -H 'Content-type:application/json' --data-binary '{"add-field":{"name":"note","type":"text_general","docValues":false,"multiValued":false,"indexed":true,"stored":true}}' http://localhost:$PORT/solr/$CORE/schema
