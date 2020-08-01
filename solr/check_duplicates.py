import requests, os, sqlite3, sys
import pandas as pd, numpy as np

db = "../sql/db_archivio.db"
url = 'http://localhost:8983/solr/prova5/select'
rows = 2000000

def curlFlBiblio():
    global db
    
    conn = sqlite3.connect(db)
    c = conn.cursor()

    c.execute("SELECT * FROM categories WHERE cgroup=1;")
    rows = c.fetchall()

    sel = []
    for r in rows:
        sel.append("tipologia:{}".format(r[0]))

    return "+OR+".join(sel)

dir_copertine = '/Users/sani/Pictures/copertine'

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

df = pd.read_excel("~/nuovi.xlsx")
df = df.fillna('')

to_drop = []
print (len(df))
for c in sorted(ca):
    for i in range(len(df)):
        codice_archivio = df.iloc[i, 1]
        if c == codice_archivio:
            to_drop.append(i)
        elif c == codice_archivio.replace(" ", ""):
            print ("{}, rimosso perche` esiste ma con spazi ({}).".format(c, codice_archivio))
            to_drop.append(i)
        elif c == codice_archivio.replace(".0", "."):
            print ("{}, rimosso perche` esiste senza zeri ({}).".format(c, codice_archivio))
            to_drop.append(i)

df = df.drop(to_drop, axis=0)
print (len(df))
