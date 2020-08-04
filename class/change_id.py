#! /usr/bin/env python3

import requests, json, sys

if len(sys.argv) != 3:
    print ("Numero errato di parametri.")
    sys.exit(21)

index_field = "codice_archivio"
old_id = sys.argv[1]
new_id = sys.argv[2]
url = 'http://localhost:8983/solr/prova5/'
query = "q={}:{}&wt=json&fl=*".format(index_field, old_id)
header = {"Content-Type":"application/json"}

# Cerca il documento da copiare
r = requests.get("{}select?{}".format(url, query)).json()

if "error" in r.keys():
    print (r["error"]['msg'])
    sys.exit(11)
    
num_found = r['response']['numFound']
if num_found != 1:
    print ("Selezione ambigua, ritornati {} records.".format(num_found))
    sys.exit(10)

# aggiorna il documento con il nuovo ID
doc = r['response']['docs'][0]
doc.update({index_field:new_id})
del doc['_version_']
data = json.dumps({'add':{'doc':doc}})

# commita il nuovo documento 
res = requests.post(url + "update?commit=true", headers=header, data=data)
if res.json()['responseHeader']['status'] != 0:
    print ("Errore nel caricamento del documento aggiornato.")
    sys.exit(2)

# cancella il vecchio documento
data = json.dumps({"delete":{"query":index_field+":"+old_id}})
res = requests.post(url + "update?commit=true", headers=header, data=data).json()

if res['responseHeader']['status'] != 0:
    print ("Non è stato possibile cancellare il vecchio documento")
    sys.exit(3)

print ("Aggiornamento del documento {} a {} riuscito !".format(old_id, new_id))

    
