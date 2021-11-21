import pandas as pd
import requests, argparse, json, subprocess
from math import floor, ceil, log
from datetime import date
from os import remove, listdir, path
from lxml import etree

def get(url, cmd, params):
    command_url = '{}/{}'.format(url, cmd)
    r = requests.get(command_url, params=params)
    return r

def backup(url, core):
    print ('Backing up core: {}...'.format(core))
    j = get(url, "admin/cores",
            (('action', 'STATUS'), ('core', core),
             ('indexInfo', 'true'))).json()
    nDocs = j['status'][core]['index']['numDocs']    
    oMag = floor(log(nDocs, 10))
    lim = ceil(nDocs/10**oMag)*10**oMag
    
    r = get(url, '{}/query'.format(core),
            (('q', '*'), ('sort', 'codice_archivio desc'),
             ('wt', 'csv'), ('rows', '6000')))
    
    open('temp.csv', 'wb').write(r.content)
    df = pd.read_csv('temp.csv', index_col='codice_archivio')
    df = df.drop('_version_', axis=1)
    filename = "backup_{}.csv".format(date.today())
    print ('Saving {}'.format(filename))
    df.to_csv(filename)
    remove('temp.csv')

def run_cmd(cmd): 
    process = subprocess.Popen(cmd,
                               stdout=subprocess.PIPE, 
                               stderr=subprocess.PIPE)
    stdout, stderr = process.communicate()
    if stderr:
        return stderr.decode("utf-8")
    else:
        return stdout.decode("utf-8")

def findMostRecentFile(backup_dir):
    files = listdir(backup_dir)
    files.sort(key=path.getmtime, reverse=True)
    for name in files:
        if name.startswith('backup'):
            return name
    return None

def changeSchema(port, core):
    err = run_cmd(["bash", "./new_fields.sh", core, str(port)]).split('\n')
    for e in err:
        if "errorMessages" in e:
            print ("ERROR: ", e)
        else:
            print (e)

    err = run_cmd(["bash", "./remove_id.sh", core, str(port)]).split('\n')
    print (err)

def manageSolr(solr_dir, port, cmd):
    out = run_cmd([solr_dir+"/solr", cmd, "-p", str(port)])
    print (out)
    
def changeUniqueKey(solr_data, core_dir):
    myXML = solr_data+"/"+core_dir+"/conf/managed-schema"
    tree = etree.parse(myXML)
    root = tree.getroot()
    code = root.xpath('//schema/uniqueKey')
    if code:
        code[0].text = 'codice_archivio'
        etree.ElementTree(root).write(solr_data+'/'+core_dir+'/conf/managed-schema', pretty_print=True)
    
def restore(url, port, solr_dir, new_core, solr_data, backup_dir):
    core = 'core_' + str(date.today())
    print ("Creating temporary core...")
    run_cmd(['bash', './create_core.sh', core, solr_dir, solr_data])
    print ("Modifying schema...")
    changeSchema(args.port, core)
    print ("Stopping solr...")
    manageSolr(solr_dir, port, 'stop')
    print ("Changing Unique Key...")
    changeUniqueKey(solr_data, core)
    print ("Restarting solr...")
    manageSolr(solr_dir, port, 'start')
    filename = findMostRecentFile(backup_dir)
    if filename is not None:
        print ("Importing latest available backup...")
        out = run_cmd([solr_dir+'/post', '-c', core, filename, '-p', str(port)])
        print (out)
        print ("Renaming core to {}...".format(new_core))
        j = get(url, "admin/cores",
                (('action', 'RENAME'), ('core', core),
                 ('other', new_core))).json()
        print (j)
    else:
        print ("No backup file found in directory.")

    
if __name__ == '__main__':
    parser = argparse.ArgumentParser(description='Backup and Restore for Archivio.')
    parser.add_argument('--action', metavar='action', type=str, help='define action to perform')
    parser.add_argument('--port', '-p', metavar='port', type=int, help='port of solr connection', default=8985)
    parser.add_argument('--solr_dir', metavar='solr_dir', type=str, help='solr binary directory', default='/Users/sani/solr-8.6.1/bin')
    parser.add_argument('--solr_data', metavar='solr_data', type=str, help='solr data directory', default='/Users/sani/solr_data')
    parser.add_argument('--backup_dir', metavar='backup_dir', type=str, help='backup directory', default='/Users/sani/site/Usered/solr')
    parser.add_argument('--core', '-c', metavar='core', type=str, help='solr core')

    args = parser.parse_args()
    url = 'http://localhost:{}/solr'.format(args.port)

    if args.action == 'backup':
        if args.core is not None:
            backup(url, args.core)
        else:
            print ("A core name must be specified.")
    elif args.action == 'restore':
        if args.core is not None:
            restore(url, args.port, args.solr_dir, args.core, args.solr_data, args.backup_dir)
        else:
            print ("A core name must be specified.")

