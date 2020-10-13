<?php
$GLOBALS['SOLR_BASE_URL'] = 'http://localhost:8983/solr';
$GLOBALS['SOLR_URL'] = $GLOBALS['SOLR_BASE_URL'].'/archivio/';
$GLOBALS['SOLR_TEST'] = $GLOBALS['SOLR_BASE_URL'].'/admin/cores?action=STATUS';
$GLOBALS['MAX_ROWS'] = '5000';
$GLOBALS['MAX_UPLOAD_BYTE'] = 200000000;
$GLOBALS['OCLC_URL'] = "http://classify.oclc.org/classify2/Classify";

$GLOBALS['DATABASENAME'] = "db_archivio.db";

$GLOBALS['UPLOAD_DIR'] = '/var/www/myupload/';
$GLOBALS['BACKUP_DIR'] = '/home/archivio/backup/';
$GLOBALS['PHOTO_DIR'] = '/home/archivio/photo/';
$GLOBALS['VIDEO_DIR'] = '/home/archivio/video/';
$GLOBALS['COVER_DIR'] = '/home/archivio/copertine/';
$GLOBALS['EDOC_DIR'] = '/home/archivio/edoc/';
$GLOBALS['THUMBNAILS_DIR'] = '/home/archivio/thumbnails/';

$GLOBALS['SOLR_BIN'] = '/home/archivio/Downloads/solr-8.4.1/bin/solr status -p 8983';
$GLOBALS['OCR_BIN'] = '/usr/bin/tesseract';
$GLOBALS['TIKA_APP'] = '/usr/local/bin/tika-app-1.24.1.jar';
$GLOBALS['MERGE_PDF_BIN'] = '/usr/local/bin/gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=';
$GLOBALS['PDF2IMAGE_BIN'] = '/usr/local/bin/pdf2image';
$GLOBALS['CONVERT_BIN'] = '/usr/local/bin/convert';
$GLOBALS['PYTHON_BIN'] = '/usr/bin/python3';
?>