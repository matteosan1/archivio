#! /usr/bin/env python3

import os, sqlite3, sys, traceback, json
from utils import readConfiguration, GET

def curlFlBiblio(db):
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
        print (traceback.format_exc())
        sys.exit()

try:
    GLOBALS = readConfiguration()
    selection = curlFlBiblio("../sql/" + GLOBALS['DATABASENAME'])
    r = GET(GLOBALS, 'admin/cores?action=STATUS&core='+GLOBALS['SOLR_CORE']+'&indexInfo=true')
    rows = r['status'][GLOBALS['SOLR_CORE']]['index']['numDocs']

    command = '{}/select?q={}&fl=codice_archivio&wt=json&rows={}'.format(GLOBALS['SOLR_CORE'], selection, rows)
    r = GET(GLOBALS, command)
    ca = [ v['codice_archivio'] for v in r['response']['docs']]

    dir_copertine = GLOBALS['COVER_DIR']
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
    print (traceback.format_exc())
    sys.exit()
