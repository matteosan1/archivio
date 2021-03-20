<?php
$GLOBALS['SOLR_BASE_URL'] = 'http://localhost:8983/solr';
$GLOBALS['SOLR_URL'] = $GLOBALS['SOLR_BASE_URL'].'/prova5/';
$GLOBALS['SOLR_TEST'] = $GLOBALS['SOLR_BASE_URL'].'/admin/cores?action=STATUS';
$GLOBALS['MAX_ROWS'] = '5000';
$GLOBALS['MAX_UPLOAD_BYTE'] = 200000000;
$GLOBALS['OCLC_URL'] = "http://classify.oclc.org/classify2/Classify";

$GLOBALS['DATABASENAME'] = "db_archivio.db";

$GLOBALS['UPLOAD_DIR'] = '/var/www/myupload/';
$GLOBALS['BACKUP_DIR'] = '/home/biblioteca/backup/';
$GLOBALS['PHOTO_DIR'] = '/home/biblioteca/photo/';
$GLOBALS['SLIDE_DIR'] = '/Users/sani/';      
$GLOBALS['VIDEO_DIR'] = '/home/biblioteca/video/';
$GLOBALS['COVER_DIR'] = '/home/biblioteca/copertine/';
$GLOBALS['EDOC_DIR'] = '/home/biblioteca/edoc/';
$GLOBALS['THUMBNAILS_DIR'] = '/home/biblioteca/thumbnails/';

$GLOBALS['SOLR_BIN'] = '/Users/sani/Downloads/solr-8.4.1/bin/solr status -p 8983';
$GLOBALS['OCR_BIN'] = '/usr/bin/tesseract';
$GLOBALS['TIKA_APP'] = '/usr/local/bin/tika-app-1.24.1.jar';
$GLOBALS['MERGE_PDF_BIN'] = '/usr/local/bin/gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=';
$GLOBALS['PDF2IMAGE_BIN'] = '/usr/local/bin/pdf2image';
$GLOBALS['CONVERT_BIN'] = '/usr/local/bin/convert';

$config = array(
    'endpoint' => array(
            'localhost' => array(
    	    'scheme' => 'http', # or https
            'host' => '127.0.0.1',
            'port' => 8983,
            'path' => '/',
            'core' => 'prova5',
	    'username' => 'solr',
	    'password' => 'SolrRocks'
        )
    )
);

?>