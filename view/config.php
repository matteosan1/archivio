<?php
$GLOBALS['SOLR_BASE_URL'] = 'http://localhost:8983/solr';
$GLOBALS['SOLR_URL'] = $GLOBALS['SOLR_BASE_URL'].'/prova5/';
$GLOBALS['SOLR_TEST'] = $GLOBALS['SOLR_BASE_URL'].'/admin/cores?action=STATUS';
$GLOBALS['MAX_ROWS'] = '5000';
$GLOBALS['MAX_UPLOAD_BYTE'] = 200000000;

$GLOBALS['DATABASENAME'] = "db_archivio.db";

$GLOBALS['UPLOAD_DIR'] = '/usr/local/var/www/myupload/';
$GLOBALS['PHOTO_DIR'] = '/Users/sani/myupload/photo/';
$GLOBALS['VIDEO_DIR'] = '/Users/sani/myupload/video/';
$GLOBALS['COVER_DIR'] = '/Users/sani/myupload/';
$GLOBALS['EDOC_DIR'] = '/Users/sani/myupload/';
$GLOBALS['THUMBNAILS_DIR'] = '/Users/sani/myupload/thumbnails/';

$GLOBALS['SOLR_BIN'] = '/Users/sani/Downloads/solr-8.4.1/bin/solr status -p 8983';
$GLOBALS['OCR_BIN'] = '/usr/local/bin/tesseract';
$GLOBALS['TIKA_APP'] = '/usr/local/var/www/tika-app-1.23.jar';
$GLOBALS['MERGE_PDF_BIN'] = '/usr/local/bin/gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=';
$GLOBALS['PDF2IMAGE_BIN'] = '/usr/local/bin/pdf2image';
$GLOBALS['CONVERT_BIN'] = '/usr/local/bin/convert';
?>