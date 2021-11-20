import pandas as pd
import requests, argparse, json, subprocess
from math import floor, ceil, log
from datetime import date
from os import remove

port = 8985
url = 'http://localhost:{}/solr'.format(port)
core = 'archivio2'

def get(cmd, params):
    command_url = '{}/{}'.format(url, cmd)
    r = requests.get(command_url, params=params)
    return r

def backup():
    j = get("admin/cores",
            (('action', 'STATUS'), ('core', core),
             ('indexInfo', 'true'))).json()
    nDocs = j['status'][core]['index']['numDocs']    
    oMag = floor(log(nDocs, 10))
    lim = ceil(nDocs/10**oMag)*10**oMag
    
    r = get('{}/query'.format(core),
            (('q', '*'), ('sort', 'codice_archivio desc'),
             ('wt', 'csv'), ('rows', '6000')))
    
    open('temp.csv', 'wb').write(r.content)
    df = pd.read_csv('temp.csv', index_col='codice_archivio')
    df = df.drop('_version_', axis=1)
    filename = "backup_{}.csv".format(date.today())
    df.to_csv(filename)
    remove('temp.csv')

def run_cmd(cmd): 
    process = subprocess.Popen(cmd,
                               stdout=subprocess.PIPE, 
                               stderr=subprocess.PIPE)
    stdout, stderr = process.communicate()
    return stdout, stderr

def restore():
    # crea nuovo core
    # aggiorna schema
    
if __name__ == '__main__':
    #parser = argparse.ArgumentParser(description='Backup and Restore for Archivio.')
    #parser.add_argument('integers', metavar='N', type=int, nargs='+',
    #                    help='an integer for the accumulator')
    #parser.add_argument('--sum', dest='accumulate', action='store_const',
    #                    const=sum, default=max,
    #                    help='sum the integers (default: find the max)')
    #
    #args = parser.parse_args()
    #print(args.accumulate(args.integers))
    action = 'backup'
    if action == 'backup':
        backup()
