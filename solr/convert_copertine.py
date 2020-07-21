import os, sqlite3, traceback, requests, subprocess

from PIL import Image

conn = sqlite3.connect("../sql/db_archivio.db")
c = conn.cursor()
c.execute('SELECT prefix FROM codice_archivio')
prefix = [p[0] for p in c.fetchall()]

t = ("1",)
c.execute('SELECT category FROM categories WHERE cgroup=?', t)
categories = ["tipologia:"+cat[0] for cat in c.fetchall()]

selection = "+OR+".join(categories)

files = os.listdir(".")

in_copertine = []
for f in files:
    try:
        f = f.upper().replace(" ", "")
        in_copertine.append(f)
        
        for p in prefix:
            if f.startswith(p):
                break
        else:
            if not f[:4].isdigit():
                raise (ValueError("File con Prefisso sconosciuto {}".format(f)))

        index = f.split(".")[-1]
        if len(index) != 2:
            raise (ValueError("Manca lo zero iniziale: {}".format(indice)))
    except:
        print (traceback.format_exc())

    
    basewidth = 200
    img = Image.open(f)
    if img.size[0] != 200:
        wpercent = (basewidth/float(img.size[0]))
        hsize = int((float(img.size[1])*float(wpercent)))
        img = img.resize((basewidth, hsize), Image.ANTIALIAS)
        img.save(f)
        
r = requests.get("http://localhost:8983/solr/prova5/select?q=*&fl=codice_archivio&rows=10000&fq={}&sort=codice_archivio+asc".format(selection))

j = r.json()

in_db = []
for ca in j['response']['docs']:
    in_db.append(ca['codice_archivio'])

mancano = set(in_db) - set(in_copertine)

with open("copertine_mancanti.txt", "w") as f:
    for m in sorted(mancano):
        f.write(m+"\n")




