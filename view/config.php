<?php
$GLOBALS['SOLR_URL'] = 'http://localhost:8983/solr/prova5/';
$GLOBALS['MAX_ROWS'] = '5000';
$GLOBALS['MAX_UPLOAD_BYTE'] = 200000000;

$GLOBALS['DATABASENAME'] = "db_archivio.db";

$GLOBALS['UPLOAD_DIR'] = '/usr/local/var/www/myupload/';
$GLOBALS['PHOTO_DIR'] = '/Users/sani/myupload/photo/';
$GLOBALS['VIDEO_DIR'] = '/Users/sani/myupload/video/';
$GLOBALS['COVER_DIR'] = '/Users/sani/myupload/';
$GLOBALS['EDOC_DIR'] = '/Users/sani/myupload/edoc/';
$GLOBALS['THUMBNAILS_DIR'] = '/Users/sani/myupload/thumbnails/';

$GLOBALS['OCR_BIN'] = '/usr/local/bin/tesseract';
$GLOBALS['TIKA_APP'] = '/usr/local/var/www/tika-app-1.23.jar';
$GLOBALS['MERGE_PDF_BIN'] = 'ghostscript -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite 
-sOutputFile=';
$GLOBALS['PDF2IMAGE_BIN'] = '/usr/local/bin/pdf2image';
$GLOBALS['CONVERT_BIN'] = '/usr/local/bin/convert';
?>