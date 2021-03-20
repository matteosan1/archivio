import pandas as pd, requests, json

filename = "backup_2020-05-01.csv"
url = "http://localhost:8983/solr/archivio/update?commit=true&wt=json"
header = {"Content-Type": "application/json"}
    
def checkSeparator(filename):
    with open(filename, "r") as f:
        header = f.readline()

    sep =  header.split("codice_archivio")[1][0]
    print ("Rilevato il separatore {}".format(sep))
    return sep

errori = pd.DataFrame()
sep = checkSeparator(filename)
df = pd.read_csv(filename, sep=sep)
df = df.fillna('')

for i in range(len(df)):
    doc = df.iloc[i].to_dict()
    print (doc)
    if doc['anno'] == "":
        doc['anno'] = doc['codice_archivio'].split(".")[0]
    doc.update({"anno":int(doc["anno"])})
    print (df.loc[i, "codice_archivio"])

    data = json.dumps({"add":{"doc":doc}})
    
    r = requests.post(url, headers=header, data=data).json()
    print (r)
    #except:
    #    errori.append(doc, ignore_index=True)

#errori.to_csv("errori.csv", sep="|")
    
