<?php

require_once "../view/config.php";
require_once "../view/solr_client.php";
require_once "../class/Member.php";
require_once "../view/session.php";

function listCodiceArchivio($isBiblio="book_categories", $selection="*") {
    global $client;
    $m = new Member();

    $q = "(".$m->curlFlBiblio($isBiblio).") AND codice_archivio:".$selection;

    $query = $client->createSelect();
    $query->setQuery('codice_archivio:'.$q);
    $query->addSort('codice_archivio', $query::SORT_ASC);
    $query->setRows($_SESSION['rows']); //GLOBALS['MAX_ROWS']);
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
    $query->setRows(500);
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
        //$helper[utf8_encode($key)] = is_array($value) ? utf8enc($value, $data, $book) : utf8_encode($value);
	$helper[$key] = is_array($value) ? utf8enc($value, $data, $book) : $value;
    }

    return $helper;
}

function findItem($cod) {
    // FIXME 
    $faldone_cats = array('STATUTI_E_REGOLAMENTI','LIBRI_VERBALI_E_DELIBERAZIONI','ELEZIONI',
     		          'ORATORIO_E_AFFARI_DI_CULTO','AFFARI_INTERNI','PROTETTORATO','BENI_IMMOBILI',
		  	  'PATRIMONIO_ARTISTICO_MUSEO_ARCHIVIO','COSTUMI','ECONOMATO','PROTOCOLLI_CORRISPONDENZA',
		      	  'CARRIERE_E_PUBBLICI_SPETTACOLI','CELEBRAZIONI_RICORRENZE_ATTIVITA_CULTURALI',
		      	  'TERRITORIO','CONTABILITA_GENERALE','MISCELLANEA','PRESIDENTI_SOCIETA',
		      	  'SOC_PUBBLICHE_RAPPRESENTANZE','SOC_IL_RISORGIMENTO','SOC_UNIONE',
		      	  'SOC_AVANGUARDISTA','SOC_IL_LEONE','CIRCOLO_IL_LEONE');

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

	    //print ($value);
            $res[$field] = ($value);
        }
        //exit;
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
        } else if ($res['tipologia'] == "DELIBERA") {
            $filename = $dir."update_delibera.json";
	} else if ($res['tipologia'] == "VERBALE") {
	    $filename = $dir."update_verbale.json";
        } else if ($res['tipologia'] == 'VIDEO') {
            $filename = $dir."update_video.json";
        } else if ($res['tipologia'] == 'MONTURATO') {
            $filename = $dir."update_vestizione.json";
	} else if ($res['tipologia'] == 'DOCUMENTO') {
	    $filename = $dir."update_doc.json";
	} else if (in_array($res['tipologia'], $faldone_cats)) {
	    $filename = $dir."update_faldoni.json";
        }
    } else {
        $res = array('error'=>"ERRORE CI SONO TROPPI DOCUMENTI");
    }

    if (array_key_exists('data', $res)) {
        $res['data'] = substr($res['data'], 0, 10);
    }

    $str = file_get_contents($filename);
    //print_r (json_decode($str, true));
    $json = utf8enc(json_decode($str, true), $res, $book);
    //print_r($res);

    if ($res['tipologia'] == "BOZZETTO") {
	$json = addOptionsUpd($res['categoria'], $m, 'bozzetto_categories', 'name', 'id', $json, 2, 1, 0);
        $json = addOptionsUpd($res['tecnica'], $m, 'bozzetto_techniques', 'name', 'id', $json, 4, 1, 0);
    } else if ($res['tipologia'] == "DELIBERA") {
	$json = addOptionsUpd($res['tipo_delibera'], $m, 'delibera_categories', 'name', 'id', $json, 4, 1, 0);

        if ($res['unanimita'] === 0) {
            unset($json['html'][0]['html'][7]['html'][1]['html'][0]['checked']);
        }
        if ($res['straordinaria'] == 0) {
            unset($json['html'][0]['html'][6]['html'][1]['html'][0]['checked']);
        }
    } else if ($res['tipologia'] == "VERBALE") {
	$json = addOptionsUpd($res['tipo_verbale'], $m, 'verbale_categories', 'name', 'id', $json, 2, 1, 0);
    } else if ($res['tipologia'] == "PERGAMENA") {
	$json = addOptionsUpd($res['tecnica'], $m, 'pergamena_techniques', 'name', 'id', $json, 3, 1, 0);
    } else if ($res['tipologia'] == "SONETTO") {
	$json = addOptionsUpd($res['ricorrenza'], $m, 'sonetto_events', 'name', 'id', $json, 5, 1, 0);
    } else if ($res['tipologia'] == "MONTURATO") {
	$json = addOptionsUpd($res['evento'], $m, 'ricorrenze', 'ricorrenza', 'ricorrenza', $json, 2, 1, 0);
	$json = addOptionsUpd($res['ruolo'], $m, 'ruoli_monturati', 'ruolo', 'ruolo', $json, 3, 1, 0);
    } else if (in_array($res['tipologia'], $faldone_cats)) {
        $json = addOptionsUpd($res['argomento_breve'], $m, "faldoni_categories", 'name', 'name', $json, 0, 1, 0, "", $res['tipologia']); 
    }
    print_r (json_encode($json));
}

function addOptionsUpd($r, $m, $db, $field, $ord, $json, $i2, $i3, $i4, $custom_idx="", $selection="") {
    $cat = $m->fillCombo($db, $field, $ord, $selection);
    $i = 0;
    foreach ($cat as $row) {
	$data = $row[$field];

	if (strtolower($r) == strtolower($data)) {
	    $val = array("html" => $data, "selected" => "selected");
	} else {
	    $val = array("html" => $data);
	}
	//           table      tr         td
	if ($custom_idx == "") {
	    $json['html'][0]['html'][$i2]['html'][$i3]['html'][$i4]['options'][$i] = $val;
	} else {
	    $json['html'][0]['html'][$i2]['html'][$i3]['html'][$i4]['options'][$row[$custom_idx]] = $val;
	}
	$i++;
    }
    return $json;
}

function addOptions($m, $db, $field, $ord, $json, $i2, $i3, $i4, $custom_idx="", $cgroup=NULL) {
    $cat = $m->fillCombo($db, $field, $ord, $cgroup);
    $i = 0;
    foreach ($cat as $row) {
	$data = $row[$field];
	if ($i == 0) {
	    $val = array("html" => $data, "selected" => "selected");
	} else {
	    $val = array("html" => $data);
	}
	//           table      tr         td
	if ($custom_idx == "") {
	    $json['html'][0]['html'][$i2]['html'][$i3]['html'][$i4]['options'][$i] = $val;
	} else {
	    $json['html'][0]['html'][$i2]['html'][$i3]['html'][$i4]['options'][$row[$custom_idx]] = $val;
	}
	$i++;
    }
    return $json;
}

function newItem($type, $subtype="") {
    $dir = "../view/json_form/";
    if ($type == 'LIBRO'	) {
	$filename = $dir."insert_libro.json";
    } else if ($type == "SONETTO") {
	$filename = $dir."insert_sonetto.json";
    } else if ($type == "BOZZETTO") {
	$filename = $dir."insert_bozzetto.json";
    } else if ($type == "PERGAMENA") {
	$filename = $dir."insert_pergamena.json";
    } else if ($type == "FOTOGRAFIA") {
        $filename = $dir."empty.json";
	//$filename = $dir."insert_photo.json";
    } else if ($type == "STAMPA" or $type == "LASTRA") {
	$filename = $dir."insert_stampa.json";
    } else if ($type == "VIDEO") {
	$filename = $dir."insert_video.json";
    } else if ($type == "DELIBERA") {
	$filename = $dir."insert_delibera.json";
    } else if ($type == "VERBALE") {
        $filename = $dir."insert_verbale.json";
    } else if ($type == "VESTIZIONE") {
	$filename = $dir."insert_vestizione.json";
    } else if ($type == "DOCUMENTO") {
	$filename = $dir."insert_doc.json";
    } else if ($type == "FALDONI") {
        $filename = $dir."insert_faldoni.json";
    } else if ($type == "----") {
	$filename = $dir."empty.json";
    }

    $str = file_get_contents($filename);
    $json = json_decode($str, true);
    
    $m = new Member();
    if ($type == "LIBRO") {
	$json = addOptions($m, "codice_archivio", 'prefix', 'prefix', $json, 0, 1, 0, 'prefix');
	$json = addOptions($m, "categories", 'category', 'id', $json, 1, 1, 0, 'category', 1);
    } else if ($type == "FOTOGRAFIA") {
	$json = addOptions($m, "tags", '', '', $json, 1, 1, 0);
    }  else if ($type == "STAMPA" or $type == "LASTRA") {
	if ($type == "STAMPA") {
	    $json['html'][0]['html'][0]['html'][0]['html'] = "Stampa (JPG o TIFF): ";
	} else {
	    $json['html'][0]['html'][0]['html'][0]['html'] = "Lastra (JPG o TIFF): ";
	}
	$json = addOptions($m, "tags", 'name', 'id', $json, 3, 1, 0);
    } else if ($type == "BOZZETTO") {
	$json = addOptions($m, "bozzetto_categories", 'name', 'id', $json, 1, 1, 0);
	$json = addOptions($m, "bozzetto_techniques", 'name', 'id', $json, 3, 1, 0);
    } else if ($type == "PERGAMENA") {
	$json = addOptions($m, "pergamena_techniques", 'name', 'id', $json, 2, 1, 0);
    } else if ($type == "DELIBERA") {
	$json = addOptions($m, "delibera_categories", 'name', 'id', $json, 2, 1, 0);
    } else if ($type == "VERBALE") {
        $json = addOptions($m, "verbale_categories", 'name', 'id', $json, 1, 1, 0);
    } else if ($type == "SONETTO") {
	$json = addOptions($m, "sonetto_events", 'name', 'id', $json, 3, 1, 0);
    } else if ($type == "VESTIZIONE") {
	$json = addOptions($m, "ricorrenze", 'ricorrenza', 'ricorrenza', $json, 0, 1, 0, 'ricorrenza');
	$json = addOptions($m, "ruoli_monturati", 'ruolo', 'ruolo', $json, 1, 1, 0, 'ruolo');
    } else if ($type == "FALDONI") {
        $json = addOptions($m, "faldoni_categories", 'name', 'name', $json, 0, 1, 0, "", $subtype);
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

function restore($file_csv, $file_zip) {
    $params = "-b --action restore ";
    if (!empty($file_csv)) {
	$params .= " --fcsv ".$file_csv;
    }

    if (!empty($file_zip)) {
	$params .= " --fzip ".$file_zip;
    }

    exec("../class/core_manager.py ".$params." ", $output, $status);
    if ($status == 0) {
	print_r(json_encode(array('result'=>implode('<br>', $output))));
    } else {
	print_r(json_encode(array('error'=>implode('<br>', $output))));
    }
    //        if ($isCsv) {
    //            if ($file['size'] > 0) {
    //                $newfilename = $GLOBALS['BACKUP_DIR'].$file['name'];
    //                move_uploaded_file($file["tmp_name"], $newfilename);
    //                $command = $GLOBALS['SOLR_BIN'].' -params "separator=%7C" '.$newfilename;
    //                $output = array();
    //                exec($command, $output, $r);
    //
    //            }
    //        } else {
    //            $zip = new ZipArchive;
    //            if ($zip->open($file["tmp_name"]) === TRUE) {
    //                $zip->extractTo($GLOBALS['COVER_DIR']);
    //                $zip->close();
    //                return 0;
    //            } else {
    //                print_r (json_encode(array('error'=>"Problemi con il file zip")));
    //                return 1;
    //            }
    //        }
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
    $params = "-b --action backup --date ".$upload_time;
    exec("../class/core_manager.py ".$params." ", $output, $status);

    if ($status == 0) {
	print_r(json_encode(array('result'=>implode('<br>', $output))));
    } else {
	print_r(json_encode(array('error'=>implode('<br>', $output))));
    }
}

if (isset($_POST['callback'])) {
    if ($_POST['callback'] == 'finditem') {
	findItem($_POST['codice_archivio']);
    } else if ($_POST['callback'] == 'newitem') {
      	if (isset($_POST['subtype'])) {
	   newItem($_POST['type'], $_POST['subtype']);
	} else {
	  newItem($_POST['type']);
	}
    } else if ($_POST['callback'] == 'backup') {
	backup($_POST['last_upload']);
    } else if ($_POST['callback'] == 'restore') {
	restore($_POST['filecsv'], $_POST['filezip']);
    }
}
?>
