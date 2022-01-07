#! /usr/bin/env python3

import pandas as pd, sqlite3
import requests, argparse, json, subprocess, sys

from math import floor, ceil, log
from datetime import date
from os import remove, path, chmod, walk
from lxml import etree
from zipfile import ZipFile
from io import StringIO

from utils import readConfiguration, GET2, GET_nojson, POST

import warnings
warnings.filterwarnings("ignore")

#def get(url, cmd, params):
#    command_url = '{}/{}'.format(url, cmd)
#    r = requests.get(command_url, params=params)
#    return r

class ManageCore:
    def __init__(self):
        self.G = readConfiguration()

    def changeSchema(self, core):
        err = self.run_cmd(["bash", "../solr/new_fields.sh", core, self.G['SOLR_PORT'], self.G['SOLR_DIR']]).split('\n')
        for e in err:
            if "errorMessages" in e:
                print ("ERROR: ", e)
            else:
                print (e)

        err = self.run_cmd(["bash", "../solr/remove_id.sh", core, self.G['SOLR_PORT']]).split('\n')
        
        if err != ['']:
            print (err)

    def manageSolr(self, cmd):
        out = self.run_cmd([self.G['SOLR_DIR']+"/bin/solr", cmd, "-p", self.G['SOLR_PORT']])
        print (out)
    
    def changeUniqueKey(self, core_dir):
        try:
            myXML = self.G['SOLR_DATA'] + "/" + core_dir + "/conf/managed-schema"
            tree = etree.parse(myXML)
            root = tree.getroot()
            code = root.xpath('//schema/uniqueKey')
            if code:
                code[0].text = 'codice_archivio'
                etree.ElementTree(root).write(self.G['SOLR_DATA'] + '/' + core_dir + '/conf/managed-schema',
                                              pretty_print=True)
        except:
            pass
        
    def findMostRecentFile(self):
        for d, dummy, files in walk(self.G['BACKUP_DIR']):
            files = [d + f for f in files]
            files.sort(key=path.getmtime, reverse=True)
            for name in files:
                if path.basename(name).startswith('backup'):
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
        try:
            if filename_csv is not None:
                filename_csv = self.G['BACKUP_DIR'] + filename_csv
                if not path.isfile(filename_csv):
                    print ("Il file {} deve trovarsi nella directory di backup.".format(path.basename(filename_csv)))
                    sys.exit(404)
                print ("Importing the backup...")
                r = POST(self.G, '{}/update/csv?versions=true&commit=true'.format(self.G['SOLR_CORE']),
                         (),
                         {'Content-type':'text/plain', 'charset':'utf-8'},
                         filename_csv)
            
                if r['responseHeader']['status'] == 0:
                    ndocs = len(r['adds'])//2
                    print ("{} documenti aggiornati in {} ms".format(ndocs, r['responseHeader']['QTime']))
                else:
                    print (r['error']['msg'])
                    sys.exit(r['responseHeader']['status'])
            if filename_zip is not None:
                filename_zip = self.G['BACKUP_DIR'] + filename_zip
                if not path.isfile(filename_csv):
                    print ("Il file {} deve trovarsi nella directory di backup.".format(path.basename(filename_zip)))
                    sys.exit(404)
                with ZipFile(filename_zip, 'r') as zip_ref:
                    zip_ref.extractall(self.G['COVER_DIR'])
                print ("{} estratto in {}".format(path.basename(filename_zip), self.G['COVER_DIR']))
        except Exception as e:
            print (str(e))
            sys.exit(1)            

    def restore(self, new_core):
        core = 'core_' + str(date.today())
        print ("Creating temporary core...")
        self.run_cmd(['bash', '../solr/create_core.sh', core, self.G['SOLR_DIR'], self.G['SOLR_DATA']])
        print ("Modifying schema...")
        self.changeSchema(core)
        print ("Stopping solr...")
        self.manageSolr('stop')
        print ("Changing Unique Key...")
        self.changeUniqueKey(core)
        print ("Restarting solr...")
        self.manageSolr('start')

        filename = self.findMostRecentFile()
        if filename is not None:
            print ("Importing latest available backup...")
            out = self.run_cmd([self.G['SOLR_DIR'] + '/bin/post', '-c', core
                                , filename, '-p', self.G['SOLR_PORT']])
            #print (out)
            r = POST(self.G, '{}/update/csv?commit=true'.format(core),
                         (),
                         {'Content-type':'text/plain', 'charset':'utf-8'},
                         filename)
            if r['responseHeader']['status'] == 0:
                ndocs = len(r['adds'])//2
                print ("{} documenti aggiornati in {} ms".format(ndocs, r['responseHeader']['QTime']))
            else:
                print (r['error']['msg'])
                sys.exit(r['responseHeader']['status'])
            
            print ("Renaming core to {}...".format(new_core))
            j = GET2(self.G, "admin/cores",
                     (('action', 'RENAME'), ('core', core),
                      ('other', new_core)))

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
        try:
            conn = sqlite3.connect("../sql/" + self.G['DATABASENAME'])
            curs = conn.cursor()
            curs.execute("SELECT * FROM categories WHERE cgroup=1;")
            
            rows = curs.fetchall()
            types = " OR ".join(["tipologia:"+r[0] for r in rows])
            last_upload = upload_time + 'T00:00:00Z'
            
            print ('Backing up {} for biblio...'.format(self.G['SOLR_CORE']))
            lim = self.nRows()

            r = GET_nojson(self.G, '{}/select'.format(self.G['SOLR_CORE']),
                           (('q', types), ('sort', 'codice_archivio asc'),
                            ('wt', 'csv'), ('rows', lim), ('fl', 'codice_archivio,titolo,sottotitolo,prima_responsabilita,anno,altre_responsabilita,luogo,tipologia,descrizione,ente,edizione,serie,soggetto,cdd,note,timestamp,privato'), ('fq', 'timestamp:['+last_upload+' TO NOW]')))
            
            filename = ".." + self.G['BACKUP_DIR'] + "biblio_backup_{}.csv".format(date.today())
            df = pd.read_csv(StringIO(str(r.content,'utf-8')))
            df['_version_'] = [0] * len(df)
            if len(df) != 0:
                print ('Saving {} documents into {}'.format(len(df), filename))
                df.to_csv(filename, index=False)
                chmod(filename, 0o777)
            else:
                print ('No document to backup.')
            
            ncovers = 0
            zipfilename = '..{}covers_{}.zip'.format(self.G['BACKUP_DIR'], date.today())
            if len(df) != 0:
                if path.isfile(zipfilename):
                    remove(zipfilename)
                zipObj = ZipFile(zipfilename, 'w')
                for ca in df['codice_archivio']:
                    filename = self.G['FULL_COVER_DIR'] + ca + '.JPG'
                    if path.isfile(filename):
                        zipObj.write(filename)
                        ncovers += 1
                zipObj.close()
            if ncovers == 0:
                print ('No cover to backup.')
            else:
                print ('Saving {} covers into {}'.format(ncovers, zipfilename))
                chmod(zipfilename, 0o777)
        except Exception as e:
            print (e)
            sys.exit(2)

    def backup(self):
        print ('Backing up core: {}...'.format(self.G['SOLR_CORE']))
        lim = self.nRows()
    
        r = GET_nojson(self.G, '{}/query'.format(self.G['SOLR_CORE']),
                       (('q', '*'), ('sort', 'codice_archivio desc'),
                        ('wt', 'csv'), ('rows', lim)))
    
        open('temp.csv', 'wb').write(r.content)
        df = pd.read_csv('temp.csv', index_col='codice_archivio')
        df = df.drop('_version_', axis=1)
        filename = self.G['BACKUP_DIR'] + "backup_{}.csv".format(date.today())
        print ('Saving {}'.format(filename))
        df.to_csv(filename)
        remove('temp.csv')

if __name__ == '__main__':
    m = ManageCore()
    
    parser = argparse.ArgumentParser(description='Backup and Restore for Archivio.')
    parser.add_argument('--action', metavar='action', type=str, help='define action to perform')
    parser.add_argument('-b', help="action just for biblio", action="store_true")
    parser.add_argument('--date', '-d', metavar='date', type=str, help='min date for backup', default='1900-01-01')
    parser.add_argument('--new-core', '-n', type=str, help='new core name')
    parser.add_argument('--fcsv', metavar='fcsv', type=str, help='csv filename')
    parser.add_argument('--fzip', metavar='fzip', type=str, help='zip filename')
    
    args = parser.parse_args()
    
    if args.action == 'backup':
        if args.b:
            m.backup_biblio(args.date)
            sys.exit(0)
        else:
            m.backup()
            sys.exit(0)        
    elif args.action == 'restore':
        if args.b:
            if args.fcsv or args.fzip:
                m.restore_biblio(args.fcsv, args.fzip)
                sys.exit(0)
            else:
                print ("Devi specificare un file CSV o uno ZIP.")
                sys.exit(1)
        else:
            if args.new_core:
                m.restore(args.new_core)
                sys.exit(0)
            else:
                print ("Devi specificare il nome del nuovo core.")
                sys.exit(1)

