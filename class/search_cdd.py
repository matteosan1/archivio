#! /usr/local/bin/python
import requests, sys

from io import StringIO, BytesIO
import xml.etree.ElementTree as ET
from utils import readConfiguration

def findall(root):
    c = root.findall('./oclc:recommendations/oclc:ddc/oclc:mostRecent', ns)
    if len(c) != 0:
        return (c[0].attrib['sfa'])
    else:
        c = root.findall('./oclc:recommendations/oclc:ddc/oclc:latestEdition', ns)
        if len(c) != 0:
            return (c[0].attrib['sfa'])
        else:
            c = root.findall('./oclc:recommendations/oclc:ddc/oclc:mostPopular', ns)
            if len(c) != 0:
                return (c[0].attrib['sfa'])
    return None

try:
    G = readConfiguration()

    author = sys.argv[1]
    title = sys.argv[2]
    
    params = (('title', title), ("author", author), ("summary","true"))
    r = requests.get(G['OCLC_URL'], params=params)
    root = ET.parse(BytesIO(r.content)).getroot()
    code = root[0].attrib['code']
    #root = ET.fromstring(xml)

    ns = {'oclc': 'http://classify.oclc.org'}

    if code == '0' or code == '2':
        ddc = findall(root)
        if ddc:
            print (ddc)
            sys.exit(0)
        else:
            sys.exit(1)
    elif code == '4':
        works = {}
        for c in root.findall('./oclc:works/oclc:work', ns):
            if 'DDC' in c.attrib['schemes']:
                works[c.attrib['wi']] = int(c.attrib['holdings'])

        works = dict(sorted(works.items(), key=lambda item: item[1], reverse=True))
        for k in works:
            r = requests.get(G['OCLC_URL']+"?wi="+k+"&summary=true")
            root = ET.parse(BytesIO(r.content)).getroot()
            code = root[0].attrib['code']
            if code == '0' or code == '2':
                ddc = findall(root)
                if ddc:
                    print (ddc)
                    sys.exit(0)
        sys.exit(1)
    else:
        print ("Il server OCLC non risponde.")
        sys.exit(1)
except Exception as e:
    if str(e) == "'code'":
        print ("CDD non trovato.")
    else:
        print ("Errore Generale: ", str(e))
    sys.exit(1)
