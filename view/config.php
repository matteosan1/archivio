<?php
$GLOBALS['VERSION'] = 'v2.1.0';
$GLOBALS['SOLR_CORE'] = 'archivio';
$GLOBALS['SOLR_PORT'] = '8983';
$GLOBALS['SOLR_BASE_URL'] = 'http://localhost:'.$GLOBALS['SOLR_PORT'].'/solr';
$GLOBALS['SOLR_URL'] = $GLOBALS['SOLR_BASE_URL'].'/'.$GLOBALS['SOLR_CORE'].'/';
$GLOBALS['SSL_CERT'] = 'solr-ssl.pem';
$GLOBALS['SSL_KEY'] = 'solr-ssl.key';
$GLOBALS['SOLR_TEST'] = $GLOBALS['SOLR_BASE_URL'].'/admin/cores?action=STATUS';
$GLOBALS['SOLR_DIR'] = '/home/sani/archivio/solr-9.4.1/';
$GLOBALS['SOLR_BIN'] = $GLOBALS['SOLR_DIR'].'/bin/post -c '.$GLOBALS['SOLR_CORE'].' -p '.$GLOBALS['SOLR_PORT'];
$GLOBALS['SOLR_DATA'] = '/var/solr/data';
$GLOBALS['MAX_UPLOAD_BYTE'] = '200000000';
$GLOBALS['OCLC_URL'] = 'http://classify.oclc.org/classify2/Classify';

$GLOBALS['DATABASENAME'] = "db_archivio.db";

$GLOBALS['UPLOAD_DIR'] = '/var/www/html/upload/'; 
$GLOBALS['BACKUP_DIR'] = '../backup/';
$GLOBALS['PHOTO_DIR'] = '/home/sani/archivio/photo/';
$GLOBALS['SLIDE_DIR'] = '/home/sani/archivio/photo/';      
$GLOBALS['VIDEO_DIR'] = '/home/sani/archivio/video/';
$GLOBALS['FULL_COVER_DIR'] = '/home/archivio/copertine/';
$GLOBALS['COVER_DIR'] = '../copertine/';
$GLOBALS['EDOC_DIR'] = '../edoc/';
$GLOBALS['THUMBNAILS_DIR'] = '../thumbnails/';

$GLOBALS['OCR_BIN'] = '/usr/bin/tesseract';
$GLOBALS['TIKA_APP'] = '/usr/local/bin/tika-app-2.9.1.jar';
$GLOBALS['MERGE_PDF_BIN'] = '/usr/bin/gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=';
$GLOBALS['GHOSTVIEW'] = '/usr/bin/gs';
$GLOBALS['CONVERT_BIN'] = '/usr/bin/convert';
$GLOBALS['PYTHON_BIN'] = '/usr/bin/python3';

$GLOBALS['HIGHLIGHT_BEGIN'] = '<u>';
$GLOBALS['HIGHLIGHT_END'] = '</u>';

$config = array(
    'endpoint' => array(
        'localhost' => array(
            'scheme' => 'http', # or https
            'host' => '127.0.0.1',
            'port' => $GLOBALS['SOLR_PORT'],
            'path' => '/',
            'core' => $GLOBALS['SOLR_CORE'],
            'username' => 'solr',
            'password' => 'SolrRocks'
        )
    )
);
?>
