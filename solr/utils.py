import requests
from requests.structures import CaseInsensitiveDict

def POST(GLOBALS, command, params, h=None, f=None):
    url = GLOBALS['SOLR_BASE_URL']
    cacert = GLOBALS['SOLR_DIR'] + '/server/etc/' + GLOBALS['SSL_CERT']
    
    headers = {
        'Accept-Encoding': 'gzip, deflate, sdch',
        'Accept-Language': 'en-US,en;q=0.8',
        'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36',
        'Accept': 'application/json',
        'Connection': 'keep-alive',
    }
    if headers is not None:
        headers.update(h)
    
    headers = CaseInsensitiveDict()
    headers["Authorization"] = "Basic c29scjpTb2xyUm9ja3M="

    data = open(f, 'rb').read()
    response = requests.post('{}/{}'.format(url, command),
                             params=params,
                             verify=cacert,
                             headers=headers,
                             data=data)
    print (response.content)
    return response.json()

def GET_nojson(GLOBALS, command, params):
    url = GLOBALS['SOLR_BASE_URL']
    cacert = GLOBALS['SOLR_DIR'] + '/server/etc/' + GLOBALS['SSL_CERT']
    
    headers = {
        'Accept-Encoding': 'gzip, deflate, sdch',
        'Accept-Language': 'en-US,en;q=0.8',
        'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36',
        'Accept': 'application/json',
        'Connection': 'keep-alive',
    }
    
    headers = CaseInsensitiveDict()
    headers["Authorization"] = "Basic c29scjpTb2xyUm9ja3M="
    
    response = requests.get('{}/{}'.format(url, command),
                            params=params,
                            verify=cacert,
                            headers=headers)
    return response

def GET2(GLOBALS, command, params):
    response = GET_nojson(GLOBALS, command, params)
    return response.json()
    
def GET(GLOBALS, command):
    url = GLOBALS['SOLR_BASE_URL']
    cacert = GLOBALS['SOLR_DIR'] + '/server/etc/' + GLOBALS['SSL_CERT']
    
    headers = {
        'Accept-Encoding': 'gzip, deflate, sdch',
        'Accept-Language': 'en-US,en;q=0.8',
        'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36',
        'Accept': 'application/json',
        'Connection': 'keep-alive',
    }
    
    headers = CaseInsensitiveDict()
    headers["Authorization"] = "Basic c29scjpTb2xyUm9ja3M="
    
    response = requests.get('{}/{}'.format(url, command),
                            verify=cacert,
                            headers=headers)
    return response.json()


def readConfiguration():
    GLOBALS = {}

    with open("../view/config.php", "r") as f:
        lines = f.readlines()

    params = []
    for l in lines:
        if l.lstrip() == "" or "?" in l:
            continue
        if "GLOBALS" not in l:
            break

        l = l.split("\n")[0]
        l = l.lstrip()
        l = l.replace("$", "");
        l = l.replace(".'", "+'");
        l = l.replace("'.", "'+");
        l = l[:-1]
        exec(l)

    return GLOBALS

