import pandas as pd

df = pd.read_csv(open("biblioteca.csv"))

df = df.fillna("")
a = []
for i in range(len(df)):
    if df.loc[i, 'tipologia'] != 'LIBRO':
        a.append((df.loc[i, 'tipologia'], ".".join(df.loc[i, 'codice_archivio'].split(".")[:-2])))

print (set(a))




 ('PERIODICO', 'PER.TUF'),
 ('PERIODICO', 'PER.STU'),
 ('RIVISTA', 'PER. PAS'),
 ('PERIODICO', 'PER.ULI'),
 ('PERIODICO', 'PER.ILP'),
 ('PERIODICO', 'PER.LAD'),
 ('PERIODICO', 'PER.4GI'),
 ('PERIODICO', 'PER.NGM'),
 ('PERIODICO', 'PER.DIA'),
 ('PERIODICO', 'PER.BOL'),
 ('PERIODICO', 'PER.JAC'),
 ('RIVISTA', 'PER.NBT'),

 Scusate messaggio lungo, ma spero chiaro.
 Stavo guardando di modificare la creazione del codice_archivio.
 Al momento abbiamo 43 categorie:

-----,CON.AQU,CON.BRU...CON.TOR,CON.VAL,CON.OND.TAR,CON.BRU.TOR,CON.PAN.SEL,CON.LEO.TAR,LIT,MAN,NUN,PER.ACC,PER.MAN,PER.PAL,PER.BUL,PER.LAB,PER.LAC,PER.TUF,PER.STU,PER.PAS,PER.ULI,PER.ILP,PER.LAD,PER.4GI,PER.NGM,PER.DIA,PER.BOL,PER.JAC,PER.NBT

Spesso non c'e` relazione fra questa sigla del codice archivio e la tipologia (es. un manoscritto e una pubblicazione di contrada hanno entrambi CON.IST.XXXX.YYY).
Mi domando: ha senso specificare cosi` tanto nel codice archivio ? Ai fini delle ricerche il codice archivio non andrebbe usato percui in linea di principio potrebbe essere solo anno.xx senza altri fronzoli.
Proposta: da ora in poi lascerei il codice archivio minimale XXXX.YY (senza CON, PER o altre cose) tanto e` possibile ricercare periodici dei Rozzi tramite altri campi esistenti. Ovviamente non toccherei i codici dei libri gia` inseriti. Se poi mi dite che e` necessario lascio i prefissi esistenti scritti sopra, senza problemi. Eviterei comunque di aggiungerne altre (per esempio per nuovi periodici) perche` mi sembra abbastanza inutile (es. PER_LAC per me e` e per chiunque non sia addetto ai lavori e` abbastanza poco parlante capisco solo che e` un periodico o una rivista, lo stesso vale per le pubblicazioni miste fra contrade tipo CON.LEO.TAR, un codice_archivio CON.1995.01 o CON.LEO.TAR.1995.01 non aiuta la ricerca e a mio avviso nemmeno la catalogazione, ma ditemi voi)
