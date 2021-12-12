#! /usr/bin/env python3

import pandas as pd, sqlite3
import requests, argparse, json, subprocess, sys
from math import floor, ceil, log
from datetime import date
from os import remove, listdir, path
from lxml import etree
from zipfile import ZipFile

from utils import readConfiguration, GET2, GET_nojson, POST

#def get(url, cmd, params):
#    command_url = '{}/{}'.format(url, cmd)
#    r = requests.get(command_url, params=params)
#    return r

class ManageCore:
    def __init__(self):
        self.G = readConfiguration()

    def changeSchema(self):
        err = self.run_cmd(["bash", "./new_fields.sh",
                            self.G['SOLR_CORE'], self.G['SOLR_PORT']]).split('\n')
        for e in err:
            if "errorMessages" in e:
                print ("ERROR: ", e)
            else:
                print (e)

        err = self.run_cmd(["bash", "./remove_id.sh",
                            self.G['SOLR_CORE'], self.G['SOLR_PORT']]).split('\n')
        print (err)

    def manageSolr(self, cmd):
        out = self.run_cmd([self.G['SOLR_DIR']+"/bin/solr", cmd, "-p", self.G['SOLR_PORT']])
        print (out)
    
    def changeUniqueKey(self, core_dir):
        myXML = self.G['SOLR_DATA'] + "/" + core_dir + "/conf/managed-schema"
        tree = etree.parse(myXML)
        root = tree.getroot()
        code = root.xpath('//schema/uniqueKey')
        if code:
            code[0].text = 'codice_archivio'
            etree.ElementTree(root).write(self.G['SOLR_DATA'] + '/' + core_dir + '/conf/managed-schema',
                                          pretty_print=True)

    def findMostRecentFile(self):
        files = listdir(self.G['BACKUP_DIR'])
        files.sort(key=path.getmtime, reverse=True)
        for name in files:
            if name.startswith('backup'):
                return name
        return None

    def run_cmd(self, cmd): 
        process = subprocess.Popen(cmd,
                                   stdout=subprocess.PIPE, 
                                   stderr=subprocess.PIPE)
        stdout, stderr = process.communicate()
        if stderr:
            return stderr.decode("utf-8")
        else:
            return stdout.decode("utf-8")
        

    def restore_biblio(self, filename_csv, filename_zip):
        if filename_csv is not None:
            print ("Importing the backup...")
            r = POST(self.G, '{}/update/csv'.format(self.G['SOLR_CORE']),
                     (),
                     {'Content-type':'text/plain', 'charset':'utf-8'},
                     filename_csv)
        
            #out = self.run_cmd([self.G['SOLR_DIR']+'/bin/post', '-c',
            #                    self.G['SOLR_CORE'],
            #                    filename_csv,
            #                    '-p', self.G['SOLR_PORT']])
            print (r)

        if filename_zip is not None:
            with zipfile.ZipFile(filename_zip, 'r') as zip_ref:
                zip_ref.extractall(self.G['COVER_DIR'])

    def restore(self, new_core):
        core = 'core_' + str(date.today())
        print ("Creating temporary core...")
        self.run_cmd(['bash', './create_core.sh',
                      self.G['SOLR_CORE'], self.G['SOLR_DIR'], self.G['SOLR_DATA']])
        print ("Modifying schema...")
        self.changeSchema()
        print ("Stopping solr...")
        self.manageSolr('stop')
        print ("Changing Unique Key...")
        self.changeUniqueKey(core)
        print ("Restarting solr...")
        self.manageSolr('start')
        filename = self.findMostRecentFile()
        if filename is not None:
            print ("Importing latest available backup...")
            out = self.run_cmd([self.G['SOLR_DIR'] + '/bin/post', '-c',
                                self.G['SOLR_CORE'], filename, '-p', self.G['SOLR_PORT']])
            print (out)
            print ("Renaming core to {}...".format(new_core))
            j = GET2(self.G, "admin/cores",
                     (('action', 'RENAME'), ('core', core),
                      ('other', new_core)))
            print (j)
        else:
            print ("No backup file found in directory.")

    def nRows(self):
        j = GET2(self.G, "admin/cores",
                 (('action', 'STATUS'), ('core', self.G['SOLR_CORE']),
                  ('indexInfo', 'true')))
        
        nDocs = j['status'][self.G['SOLR_CORE']]['index']['numDocs']    
        oMag = floor(log(nDocs, 10))
        return ceil(nDocs/10**oMag)*10**oMag

    def backup_biblio(self, upload_time):
        conn = sqlite3.connect("../sql/" + self.G['DATABASENAME'])
        curs = conn.cursor()
        curs.execute("SELECT * FROM categories WHERE cgroup=1;")
    
        rows = curs.fetchall()
        types = " OR ".join(["tipologia:"+r[0] for r in rows])
        last_upload = upload_time + 'T00:00:00Z'

        print ('Backing up core: {} for biblio...'.format(self.G['SOLR_CORE']))
        lim = self.nRows()

        r = GET_nojson(self.G, '{}/select'.format(core),
                       (('q', types), ('sort', 'codice_archivio asc'),
                        ('wt', 'csv'), ('rows', lim), ('fl', 'codice_archivio,titolo,sottotitolo,prima_responsabilita,anno,altre_responsabilita,luogo,tipologia,descrizione,ente,edizione,serie,soggetto,cdd,note,timestamp'), ('fq', 'timestamp:['+last_upload+' TO NOW]')))

        filename = self.G['BACKUP_DIR'] + "/biblio_backup_{}.csv".format(date.today())
        print ('Saving {}'.format(filename))
        open(filename, 'wb').write(r.content)

        r = GET2(self.G, '{}/select'.format(self.G['SOLR_CORE']),
                 (('q', types), ('sort', 'codice_archivio asc'),
                  ('wt', 'json'), ('rows', lim), ('fl', 'codice_archivio'), ('fq', 'timestamp:['+last_upload+' TO NOW]')))
        response = r['response']['docs']
        cas = {k: [dic[k] for dic in response] for k in response[0]}
        zipObj = ZipFile('{}/covers_{}.zip'.format(self.G['BACKUP_DIR'], date.today()), 'w')
        for ca in cas['codice_archivio']:
            zipObj.write(self.G['BACKUP_DIR'] + "/" + ca + '.JPG')
        zipObj.close()

    def backup(self):
        print ('Backing up core: {}...'.format(self.G['SOLR_CORE']))
        lim = self.nRows()
    
        r = GET_nojson(self.G, '{}/query'.format(self.G['SOLR_CORE']),
                       (('q', '*'), ('sort', 'codice_archivio desc'),
                        ('wt', 'csv'), ('rows', lim)))
    
        open('temp.csv', 'wb').write(r.content)
        df = pd.read_csv('temp.csv', index_col='codice_archivio')
        df = df.drop('_version_', axis=1)
        filename = self.G['BACKUP_DIR'] + "/backup_{}.csv".format(date.today())
        print ('Saving {}'.format(filename))
        df.to_csv(filename)
        remove('temp.csv')

if __name__ == '__main__':
    m = ManageCore()
    
    parser = argparse.ArgumentParser(description='Backup and Restore for Archivio.')
    parser.add_argument('--action', metavar='action', type=str, help='define action to perform')
    parser.add_argument('-b', help="action just for biblio", action="store_true")
    parser.add_argument('--date', '-d', metavar='date', type=str, help='min date for backup', default='1900-01-01')
    parser.add_argument('--new-core', '-n', metavar='core', type=str, help='new core name')
    parser.add_argument('--fcvs', metavar='fcvs', type=str, help='cvs filename')
    parser.add_argument('--fzip', metavar='fzip', type=str, help='zip filename')
    
    args = parser.parse_args()
    if args.action == 'backup':
        if args.b:
            m.backup_biblio(args.date)
        else:
            m.backup()
    elif args.action == 'restore':
        if args.b:
            if args.fcsv or args.fzip:
                m.restore_biblio(args.fcsv, args.fzip)
            else:
                print ("Devi specificare un file CSV o uno ZIP.")    
        else:
            if args.core:
                m.restore(args.core)
            else:
                print ("Devi specificare il nome del nuovo core.")

