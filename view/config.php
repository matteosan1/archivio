<?php
    $GLOBALS['SOLR_CORE'] = 'archivio2';
    $GLOBALS['SOLR_PORT'] = '8985';
    $GLOBALS['SOLR_BASE_URL'] = 'https://localhost:'.$GLOBALS['SOLR_PORT'].'/solr';
    $GLOBALS['SOLR_URL'] = $GLOBALS['SOLR_BASE_URL'].'/'.$GLOBALS['SOLR_CORE'].'/';
    $GLOBALS['SSL_CERT'] = 'solr-ssl.pem';
    $GLOBALS['SSL_KEY'] = 'solr-ssl.key';
    $GLOBALS['SOLR_TEST'] = $GLOBALS['SOLR_BASE_URL'].'/admin/cores?action=STATUS';
    $GLOBALS['SOLR_DIR'] = '/opt/solr/';
    $GLOBALS['SOLR_BIN'] = $GLOBALS['SOLR_DIR'].'/bin/post -c '.$GLOBALS['SOLR_CORE'].' -p '.$GLOBALS['SOLR_PORT'];
    $GLOBALS['SOLR_DATA'] = '/var/solr/data';
    $GLOBALS['MAX_UPLOAD_BYTE'] = '200000000';
    $GLOBALS['OCLC_URL'] = 'http://classify.oclc.org/classify2/Classify';

    $GLOBALS['DATABASENAME'] = "db_archivio.db";

    $GLOBALS['UPLOAD_DIR'] = '/Users/sani/site/Usered/view/myupload/'; 
    $GLOBALS['BACKUP_DIR'] = '/Users/sani/site/Usered/backup/';
    $GLOBALS['PHOTO_DIR'] = '/home/biblioteca/photo/';
    $GLOBALS['SLIDE_DIR'] = '/Users/sani/';      
    $GLOBALS['VIDEO_DIR'] = '/home/biblioteca/video/';
    $GLOBALS['COVER_DIR'] = '/Users/sani/site/Usered/covers/';
    $GLOBALS['EDOC_DIR'] = '/Users/sani/site/Usered/edoc/';
    $GLOBALS['THUMBNAILS_DIR'] = '/Users/sani/site/Usered/thumb/';

    $GLOBALS['OCR_BIN'] = '/usr/local/bin/tesseract';
    $GLOBALS['TIKA_APP'] = '/Users/sani/site/Usered/class/tika-app-2.1.0.jar';
    $GLOBALS['MERGE_PDF_BIN'] = '/usr/local/bin/gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=';
    $GLOBALS['GHOSTVIEW'] = '/usr/local/bin/gs';
    $GLOBALS['CONVERT_BIN'] = '/usr/local/bin/convert';
    $GLOBALS['PYTHON_BIN'] = '/Users/sani/opt/anaconda3/bin/python';

    $GLOBALS['HIGHLIGHT_BEGIN'] = '<u>';
    $GLOBALS['HIGHLIGHT_END'] = '</u>';
    
    $config = array(
        'endpoint' => array(
            'localhost' => array(
    	        'scheme' => 'https', # or https
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