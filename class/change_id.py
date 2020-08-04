#! /usr/bin/env python

import requests, json, sys


if len(sys.argv) != 3:
    print ("Numero errato di parametri.")
    sys.exit(1)

index_field = "codice_archivio"
old_id = sys.argv[1]
new_id = sys.argv[2]
url = 'http://localhost:8983/solr/prova5/'
query = "q=id:{}&wt=json&fl=*".format(id)
header = {"Content-Type":"application/json"}

# Cerca il documento da copiare
r = requests.get("{}select?{}".format(url, query)).json()

num_found = r['response']['numFound']
if num_found != 1:
    print ("Selezione ambigua, ritornati {} records.".format(num_found))
    sys.exit(1)

# aggiorna il documento con il nuovo ID
doc = r['response']['docs'][0]
doc.update({"id":new_id})
del doc['_version_']
data = json.dumps({'add':{'doc':doc}})

# commita il nuovo documento 
res = requests.post(url + "update?commit=true", headers=header, data=data)
print (res.status_code)
if res.json()['responseHeader']['status'] != 0:
    print ("Errore nel caricamento del documento aggiornato.")
    sys.exit(1)

# cancella il vecchio documento
data = json.dumps({'delete':{index_field:old_id}})
res = requests.post(url + "update?commit=true", headers=header, data=data)
if res.json()['responseHeader']['status'] != 0:
    print ("Non è stato possibile cancellare il vecchio documento")
    sys.exit(1)

print ("Aggiornamento del documento {} a {} riuscito !".format(old_id, new_id))

    
