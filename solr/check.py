import pandas as pd, numpy as np

df_nuovi = pd.read_excel("~/nuovi.xlsx")
df_nuovi = df_nuovi.fillna('')

df = pd.read_csv("pippo.csv")
df = df_nuovi.fillna('')

to_drop = []
for i_n in range(len(df_nuovi)):
    ca_nuovi = df_nuovi.loc[i_n, "CODICE ARCHIVIO"]
    if ca_nuovi == "":
        continue
    for i in range(len(df)):
        ca = df.iloc[i, 1]
        if ca == "":
            continue
        #print ("CA",ca)
        if ca_nuovi == ca:
            to_drop.append(i_n)
            break
        elif ca_nuovi == ca.replace(" ", ""):
            print ("{}, rimosso perche` esiste ma con spazi ({}).".format(ca_nuovi, ca))
            to_drop.append(i_n)
            break
        elif ca_nuovi == ca.replace(".0", "."):
            print ("{}, rimosso perche` esiste senza zeri ({}).".format(ca_nuovi, ca))
            to_drop.append(i_n)
            break
    else:
        print (ca)

