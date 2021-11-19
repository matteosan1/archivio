<?php
    require_once "../view/config.php";
    require_once "../view/solr_client.php";
    require_once "../class/Member.php";
    
    function listCodiceArchivio($isBiblio="book_categories", $selection="*") {
        global $client;
        $m = new Member();
    
        $q = "(".$m->curlFlBiblio($isBiblio).") AND codice_archivio:".$selection;
        
        $query = $client->createSelect();
        $query->setQuery('codice_archivio:'.$q);
        $query->addSort('codice_archivio', $query::SORT_ASC);
        $query->setRows($GLOBALS['MAX_ROWS']);
        $query->setFields('codice_archivio');
        $resultset = $client->select($query);

        $result = array();
        $i = 0;
        foreach ($resultset as $document) {
            $result[$i] = $document->codice_archivio;
            $i += 1;
        }  
        //print_r ($result);
        return $result;
    }
    
    function lookForDuplicates($resourceName) {
        global $client;

        $query = $client->createSelect();

        $query->setQuery('resourceName:'.$resourceName);
        $query->setFields('codice_archivio');

        $error = "";
        try {
           $resultset = $client->select($query);
           $docs = $resultset->getDocuments();
        } catch (Solarium\Exception\HttpException $e) {
           $error = $e->getMessage();
           return -2;
        } catch (Exception $e) {
           $error = $e->getMessage();
           return -2;
        }

        if ($resultset->getNumFound() == 0) {
           return 1;
        } else {
           return -1;
        }
    }

    function getLastByIndex($search) {
        global $client;
        
        $query = $client->createSelect();

        $query->setQuery('codice_archivio:'.$search.'*');
        $query->setFields('codice_archivio');
        $query->addSort('codice_archivio', $query::SORT_ASC);    

        $resultset = $client->select($query);
        $docs = $resultset->getDocuments();
        $document = end($docs);

        if ($resultset->getNumFound() == 0) {
           $indice_codice_archivio = 0;
        } else {
           $codice_archivio_esploso = explode(".", $document['codice_archivio']);
           $indice_codice_archivio = end($codice_archivio_esploso);
        }
        
        return $indice_codice_archivio;
    }

    function utf8enc($array, $data, $book=1) {
        $helper = array();
        foreach ($array as $key => $value) {
            if (substr($key, -4, 4) == '_chg') {
                $key = str_replace("_chg", "", $key);
                if ($key == "src") {
                    if ($book == 1) {
                        $value = $GLOBALS['COVER_DIR'].$data['codice_archivio'].".JPG";
                    } else {
                        $value = $GLOBALS['THUMBNAILS_DIR'].$data['codice_archivio'].".JPG";
                    }
                } else { 
                    if (isset($data[$value])) {
                        $value = $data[$value];
                    } else {
                        $value = "";
                    }
                }
            }
            $helper[utf8_encode($key)] = is_array($value) ? utf8enc($value, $data, $book) : utf8_encode($value);
        }

        return $helper;
    }

    function findItem($cod) {
        global $client;
        $m = new Member();
        $libri = $m->getAllCategories("book_categories", true);
        $edoc = $m->getAllCategories("ebook_categories", true);
    
        $query = $client->createSelect();
        $query->setQuery('codice_archivio:'.$cod);
        
        $resultset = $client->select($query);
        if ($resultset->getNumFound() == 1) {
            $book = 0;
            $doc = $resultset->getDocuments();
            $res = array();
            foreach ($doc[0] as $field => $value) {
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
        
                $res[$field]  = $value;
            }

            $dir = "../view/json_form/";
            if ($res['tipologia'] == 'BOZZETTO') {
                $filename = $dir."update_bozzetto.json";        
            } else if ($res['tipologia'] == 'PERGAMENA') {
                $filename = $dir."update_pergamena.json";
            } else if ($res['tipologia'] == 'SONETTO') {
                $filename = $dir."update_sonetto.json";    
            } else if (in_array($res['tipologia'], $libri)) {
                $book = 1;
                $filename = $dir."update_libro.json";     
            } else if ($res['tipologia'] == 'VIDEO') {
                $filename = $dir."update_video.json";
            }
        } else {
            $res = array('error'=>"ERRORE CI SONO TROPPI DOCUMENTI");
        }
    
        $str = file_get_contents($filename);
        $json = utf8enc(json_decode($str, true), $res, $book);
    
        if ($res['tipologia'] == "BOZZETTO") {
            $m = new Member();
            $cat = $m->fillCombo("bozzetto_categories");
            $i = 0;
            foreach ($cat as $row) {
                $data = $row['name'];
                //           table      tr         td
                if (strtolower($res['categoria']) == strtolower($data))
                    $val = array("html" => $data, "selected" => "selected");
                else
                    $val = array("html" => $data);
                $json['html'][0]['html'][2]['html'][1]['html'][0]['options'][$i] = $val;
                $i++;
            }

            $tech = $m->fillCombo("bozzetto_techniques");
            $i = 0;
            foreach ($tech as $row) {
                $data = $row['name'];
                if (strtolower($res['tecnica']) == strtolower($data))
                    $val = array("html" => $data, "selected" => "selected");
                else
                    $val = array("html" => $data);
                //           table      tr         td      
                $json['html'][0]['html'][4]['html'][1]['html'][0]['options'][$i] = $val;
                $i++;
            }            
        } else if ($res['tipologia'] == "PERGAMENA") {
            $m = new Member();
            $tech = $m->fillCombo("pergamena_techniques");
            $i = 0;
            foreach ($tech as $row) {
                $data = $row['name'];
                if (strtolower($res['tecnica']) == strtolower($data))
                    $val = array("html" => $data, "selected" =>  "selected");
                else
                    $val = array("html" => $data);
                //           table      tr         td      
                $json['html'][0]['html'][3]['html'][1]['html'][0]['options'][$i] = $val;
                $i++;
            }            
        } else if ($res['tipologia'] == "SONETTO") {
            $m = new Member();
            $tech = $m->fillCombo("sonetto_events");
            $i = 0;
            foreach ($tech as $row) {
                $data = $row['name'];
                if ($res['ricorrenza'] == $data)
                    $val = array("html" => $data, "selected" =>  "selected");
                else
                    $val = array("html" => $data);    
                //           table      tr         td      
                $json['html'][0]['html'][4]['html'][1]['html'][0]['options'][$i] = $val;
                $i++;
            }
        }
    
        print_r (json_encode($json));
    }

    function newItem($type) {
        $dir = "../view/json_form/";
        if ($type == 'LIBRO') {
            $filename = $dir."insert_libro.json";     
        } else if ($type == "VERBALE") {
        } else if ($type == "SONETTO") {
            $filename = $dir."insert_sonetto.json";
        } else if ($type == "ARTICOLO_GIORNALE") {
        } else if ($type == "DELIBERA") {
        } else if ($type == "BOZZETTO") {
            $filename = $dir."insert_bozzetto.json";   
        } else if ($type == "PERGAMENA") {
            $filename = $dir."insert_pergamena.json"; 
        } else if ($type == "FOTOGRAFIA") {
            $filename = $dir."insert_photo.json";
        } else if ($type == "STAMPA" or $type == "LASTRA") {
            $filename = $dir."insert_stampa.json";
        } else if ($type == "VIDEO") {
            $filename = $dir."insert_video.json";
        }    

        $str = file_get_contents($filename);
        $json = json_decode($str, true);
    
        if ($type == "LIBRO") {
            $m = new Member();
            $prefissi = $m->getAllPrefissi();
            $categories = $m->getAllCategories('book_categories');
            foreach ($prefissi as $category) {
                $json['html'][0]['html'][0]['html'][1]['html'][0]['options'][$category['prefix']] =
                    array("html" => $category['prefix']);
            }

            foreach ($categories as $category) {
                $json['html'][0]['html'][1]['html'][1]['html'][0]['options'][$category['category']] =
                    array("html" => $category['category']);
            }
        } else if ($type == "FOTOGRAFIA") {
            $m = new Member();
            $l1tags = $m->getL1Tags();

            foreach ($l1tags as $row) {
                $id = $row['id'];
                $data = $row['name'];
                //           table      tr         td      
                $json['html'][0]['html'][1]['html'][1]['html'][0]['options'][$id] =
                    array("html" => $data);
            }            
        }  else if ($type == "STAMPA" or $type == "LASTRA") {
            if ($type == "STAMPA") {
                $json['html'][0]['html'][0]['html'][0]['html'] = "Stampa (JPG o TIFF): ";
            } else {
                $json['html'][0]['html'][0]['html'][0]['html'] = "Lastra (JPG o TIFF): ";
            }
    
            $m = new Member();
            $l1tags = $m->getL1Tags();

            foreach ($l1tags as $row) {
                $id = $row['id'];
                $data = $row['name'];
                //           table      tr         td      
                $json['html'][0]['html'][3]['html'][1]['html'][0]['options'][$id] =
                    array("html" => $data);
            }            
        } else if ($type == "BOZZETTO") {
            $m = new Member();
            $cat = $m->fillCombo("bozzetto_categories");
            $i = 0;
            foreach ($cat as $row) {
                $data = $row['name'];
                //           table      tr         td      
                $json['html'][0]['html'][0]['html'][1]['html'][0]['options'][$i] =
                    array("html" => $data);
                $i++;
            }
    
            $tech = $m->fillCombo("bozzetto_techniques");
            $i = 0;
            foreach ($tech as $row) {
                $data = $row['name'];
                //           table      tr         td      
                $json['html'][0]['html'][2]['html'][1]['html'][0]['options'][$i] =
                    array("html" => $data);
                $i++;
            }            
        } else if ($type == "PERGAMENA") {
            $m = new Member();
            $tech = $m->fillCombo("pergamena_techniques");
            $i = 0;
            foreach ($tech as $row) {
                $data = $row['name'];
                //           table      tr         td      
                $json['html'][0]['html'][1]['html'][1]['html'][0]['options'][$i] =
                    array("html" => $data);
                $i++;
            }
        } else if ($type == "SONETTO") {
            $m = new Member();
            $tech = $m->fillCombo("sonetto_events");
            $i = 0;
            foreach ($tech as $row) {
                $data = $row['name'];
                //           table      tr         td      
                $json['html'][0]['html'][4]['html'][1]['html'][0]['options'][$i] =
                    array("html" => $data);
                $i++;
            }
        }

        print_r (json_encode($json));
    }
    
    if (isset($_POST['func'])) {
      if ($_POST['func'] == 'find') {
         echo json_encode(findBook($_POST['sel']));
      } 
    }

    function curlOperationPOST($file) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL'].'update?commit=true&separator=%7C&encapsulator="');
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_POST,           true);
    	curl_setopt($ch, CURLOPT_POSTFIELDS,     $file); 
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/csv; charset=utf-8'));
        
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
    
    function restore($file, $isCsv=1) {
        if ($isCsv) {
            if ($file['size'] > 0) {
                $newfilename = $GLOBALS['BACKUP_DIR'].$file['name'];
                move_uploaded_file($file["tmp_name"], $newfilename);
                $command = $GLOBALS['SOLR_BIN'].' -params "separator=%7C" '.$newfilename;
                $output = array();
                exec($command, $output, $r);
                if ($r == 0) {
                    return 0;
                } else {
                    print_r(json_encode(array('error'=>implode('<br>', $output))));
                    return 1;
                }
            }
        } else {
            $zip = new ZipArchive;
            if ($zip->open($file["tmp_name"]) === TRUE) {
                $zip->extractTo($GLOBALS['COVER_DIR']);
                $zip->close();
                return 0; 
            } else {
                print_r (json_encode(array('error'=>"Problemi con il file zip")));
                return 1;
            }
        }
    }

    function curlOperationGET($URL) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //if ($type == 'json') {
        //    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
        //                                               'Accept: application/json'));
        //} else {
        //    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/csv'));
        //}

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    function backup($upload_time) {
        $m = new Member();
    
        $q_string = ($m->curlFlBiblio());
        $last_upload = date('Y-m-d\T\0\0\:\0\0\:\0\0\Z', strtotime($upload_time));
        $date_for_file = date('Y-m-d', strtotime($upload_time));

        $URL = $GLOBALS['SOLR_URL'].'select?csv.separator=%7C&fl=codice_archivio,titolo,sottotitolo,prima_responsabilita,anno,altre_responsabilita,luogo,tipologia,descrizione,ente,edizione,serie,soggetto,cdd,note,timestamp&sort=codice_archivio+asc&fq=timestamp:['.$last_upload.'%20TO%20NOW]&q='.urlencode($q_string).'&wt=csv&rows='.$GLOBALS['MAX_ROWS'];

        $data = curlOperationGET($URL, $last_upload);
        if ($data == false) {
            print_r(json_encode(array("error"=>"Ci sono problemi con il Server.")));
        }

        $csv_filename = $GLOBALS['BACKUP_DIR'].'backup_'.$date_for_file.'.csv';
        $csv_url = "http://localhost/backup/".'backup_'.$date_for_file.'.csv';
    
        file_put_contents($csv_filename, iconv(mb_detect_encoding($data, mb_detect_order(), true), "UTF-8", $data)); 


        $URL = $GLOBALS['SOLR_URL'].'select?fl=codice_archivio&fq=timestamp:['.$last_upload.'%20TO%20NOW]&q='.urlencode($q_string).'&wt=json&rows='.$GLOBALS['MAX_ROWS'];
        $data = json_decode(curlOperationGET($URL), true);
        if ($data == false) {
            print_r(json_encode(array("error"=>"Ci sono problemi con il Server.")));
        }
        
        $zip = new ZipArchive();
        $zip_filename = $GLOBALS['BACKUP_DIR'].'cover_'.$date_for_file.'.zip';
        $zip_url = "http://localhost/backup/".'cover_'.$date_for_file.'.zip';
        if ($zip->open($zip_filename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach($data['response']['docs'] as $entry) {
                $zip->addFile("/Users/sani/site/Usered/".$GLOBALS['COVER_DIR'].$entry['codice_archivio'].".JPG", $entry['codice_archivio'].".JPG");
            }
            $zip->close();
         } else {
            echo json_encode(array("error"=>"Non posso aprire ".$zip_filename));
            exit;
         }

        $res = array("result" => '<a href="'.$csv_url.'">Catalogo  CSV</a>&nbsp;'.' <a href="'.$zip_url.'">Copertine ZIP</a><br>');
        print_r (json_encode($res));
    }
    
if (isset($_POST['callback'])) {
    if ($_POST['callback'] == 'finditem') {
        findItem($_POST['codice_archivio']);
    } else if ($_POST['callback'] == 'newitem') {
        newItem($_POST['type']);
    } else if ($_POST['callback'] == 'backup') {
        backup($_POST['last_upload']);
    }
} else if (isset($_POST['func'])) {
    if ($_POST['func'] == 'restore') {
        if ($_FILES['filecsv']['error'] == 0) {
            $r = restore($_FILES['filecsv']);
            if ($r == 1) 
                return;
        }

        if ($_FILES['filezip']['error'] == 0) {
            $r = restore($_FILES['filezip'], 0);
            if ($r == 1) 
                return;
        }
        
        print_r (json_encode(array('result'=>'Catalogo importato correttamente.')));
    }
}
?>

