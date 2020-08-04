#! /usr/bin/env python3

import requests, os, sqlite3, sys, traceback, json

db = "../sql/db_archivio.db"
url = 'http://localhost:8983/solr/prova5/select'
dir_copertine = '/home/biblioteca/copertine'    
rows = 2000000

def curlFlBiblio():
    global db
    try:
        conn = sqlite3.connect(db)
        c = conn.cursor()
        
        c.execute("SELECT category FROM categories WHERE cgroup=1;")
        rows = c.fetchall()

        sel = []
        for r in rows:
            sel.append("tipologia:{}".format(r[0]))
        
        return "+OR+".join(sel)
    except:
        print (traceback.exc())
        sys.exit()

try:
    headers = {
        'Accept-Encoding': 'gzip, deflate, sdch',
        'Accept-Language': 'en-US,en;q=0.8',
        'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36',
        'Accept': 'application/json',
        'Connection': 'keep-alive',
    }
    
    selection = curlFlBiblio()
    response = requests.get('{}?q={}&fl=codice_archivio&wt=json&rows={}'.format(url, selection, rows), headers=headers)
    
    r = response.json()

    ca = [ v['codice_archivio'] for v in r['response']['docs']]
    
    co = []
    for a, b, c in os.walk(dir_copertine):
        for f in c:
            if f.endswith(".JPG") or f.endswith(".jpg") or f.endswith(".JPEG") or f.endswith(".jpeg"):
                co.append(f.split(".JPG")[0])

    ca = set(ca)
    co = set(co)
    mancanti = sorted(list(ca - co))
    non_assegnate = sorted(list(co - ca))
    print ("Copertine mancanti ({}):".format(len(mancanti)))
    print (json.dumps(mancanti))
    
    print ("Copertine non assegnate ({}):".format(len(non_assegnate)))
    print (json.dumps(non_assegnate))
except:
    print (traceback.exc())
    sys.exit()
