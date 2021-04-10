<?php
    $GLOBALS['SOLR_CORE'] = 'archivio';
    $GLOBALS['SOLR_PORT'] = 8985;
    $GLOBALS['SOLR_BASE_URL'] = 'http://localhost:'.$GLOBALS['SOLR_PORT'].'/solr';
    $GLOBALS['SOLR_URL'] = $GLOBALS['SOLR_BASE_URL'].'/'.$GLOBALS['SOLR_CORE'].'/';
    $GLOBALS['SOLR_TEST'] = $GLOBALS['SOLR_BASE_URL'].'/admin/cores?action=STATUS';
    //$GLOBALS['SOLR_BIN'] = '/Users/sani/Downloads/solr-8.4.1/bin/solr status -p 8983';
    $GLOBALS['SOLR_BIN'] = '/Users/sani/solr-8.6.1/bin/post -c '.$GLOBALS['SOLR_CORE'].' -p '.$GLOBALS['SOLR_PORT'];
    $GLOBALS['MAX_ROWS'] = '5000';
    $GLOBALS['MAX_UPLOAD_BYTE'] = 200000000;
    $GLOBALS['OCLC_URL'] = "http://classify.oclc.org/classify2/Classify";

    $GLOBALS['DATABASENAME'] = "db_archivio.db";

    $GLOBALS['UPLOAD_DIR'] = '/var/www/myupload/';
    $GLOBALS['BACKUP_DIR'] = '/Users/sani/site/Usered/backup/';
    $GLOBALS['PHOTO_DIR'] = '/home/biblioteca/photo/';
    $GLOBALS['SLIDE_DIR'] = '/Users/sani/';      
    $GLOBALS['VIDEO_DIR'] = '/home/biblioteca/video/';
    $GLOBALS['COVER_DIR'] = '/Users/sani/site/Usered/covers/';
    $GLOBALS['EDOC_DIR'] = '/home/biblioteca/edoc/';
    $GLOBALS['THUMBNAILS_DIR'] = '/thumb/';

    $GLOBALS['OCR_BIN'] = '/usr/bin/tesseract';
    $GLOBALS['TIKA_APP'] = '/usr/local/bin/tika-app-1.24.1.jar';
    $GLOBALS['MERGE_PDF_BIN'] = '/usr/local/bin/gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=';
    $GLOBALS['PDF2IMAGE_BIN'] = '/usr/local/bin/pdf2image';
    $GLOBALS['CONVERT_BIN'] = '/usr/local/bin/convert';
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
#	            'username' => 'solr',
#	            'password' => 'SolrRocks'
            )
        )
    );
?>