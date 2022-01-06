#! /usr/bin/python3
import json, sys, datetime
from subprocess import Popen, PIPE
from utils import readConfiguration
#import locale
#locale.setlocale(locale.LC_ALL, 'en_EN.utf8')

filename = sys.argv[1]
G = readConfiguration()

command = "/usr/bin/java -jar " + G['TIKA_APP'] + " -j -t -J '" + filename + "'"

with Popen(command, stdout=PIPE, stderr=PIPE, shell=True) as process:
    output, error = process.communicate()

if process.returncode != 0:
    print (json.dumps(error.decode("utf-8")))
    sys.exit(process.returncode)

try:
    d = json.loads(output.decode("utf-8"))
    d = d[0]
    time = d['X-TIKA:parse_time_millis']
    payload = {}
    
    if "pdf" in d['Content-Type']:
        keys = {'Content-Length':'size',
                'Content-Type':'type',
                'Creation-Date':'cdate',
                'Last-Modified':'mdate',
                'X-TIKA:content':'testo',
                'resourceName':'resourceName',
                'xmpTPg:NPages':'pagine'}
        
        for k, v in keys.items():
            if k in d:
                payload[v] = d[k].strip(' \n')
            else:
                payload[v] = ""
        payload['note'] = ''
        payload['titolo'] = ''
        payload['autore'] = ''
    elif 'word' in d['Content-Type']:
        keys = {'Author':'autore', 'Content-Length':'size', 'Content-Type':'type', 'Creation-Date':'cdate', 'Last-Modified':'mdate', 'Page-Count':'pagine', 'Word-Count':'parole', 'X-TIKA:content':'testo', 'resourceName':'resourceName'}
        for k, v in keys.items():
            if k in d:
                payload[v] = d[k].strip(' \n')
            else:
                payload[v] = ""
        payload['note'] = ''
        payload['titolo'] = ''
    elif "jpeg" in d['Content-Type']:
        keys = {'Content-Length':'size', 'Content-Type':'type', 'File Modified Date':'cdate', 'X-TIKA:content':'testo', 'resourceName':'resourceName'}
        for k, v in keys.items():
            payload[v] = d[k]
        payload['note'] = ''
        payload['titolo'] = ''
        payload['autore'] = ''

        cdate = datetime.datetime.strptime(payload['cdate'][:19]+" "+payload['cdate'][-4:], "%a %b %d %H:%M:%S %Y")
        payload['cdate'] = cdate.strftime("%Y-%m-%d")

    with open('../upload/tika.output', 'w', encoding='utf8') as json_file:
        json.dump(payload, json_file, ensure_ascii=False)
    sys.exit(0)
except Exception as e:
    with open('../upload/tika.error', 'w') as f:
        f.write(str(e)+'\n')
    sys.exit(1)
