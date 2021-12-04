<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../view/config.php";
require_once "../view/solr_client.php";
require_once "../class/pagination.class.php";
require_once "../class/Member.php";
    
require_once("../view/search_output/libro_output.php");
require_once("../view/search_output/monturato_output.php");
require_once("../view/search_output/fotografia_output.php");
require_once("../view/search_output/stampa_output.php");
require_once("../view/search_output/bozzetto_output.php");
require_once("../view/search_output/pergamena_output.php");
require_once("../view/search_output/sonetto_output.php");
require_once("../view/search_output/delibera_output.php");    
     
if (isset($_GET)) {
    $member = new Member();
  
    $no_of_records_per_page = 10;
    $page = 1;
    if (isset($_GET['page'])) {           
        $page = $_GET['page'];
    }

    $searchValue = "";
    if (isset($_GET['q'])) {           
        $searchValue = $_GET['q'];
    }
    if ($searchValue == "") {
        $searchValue = "*";
    }

    $subsearch = 0;
    if(!empty($_GET["sub"])) {
        $subsearch = $_GET["sub"];
    }
    
    $query = $client->createSelect();
    $query->setOmitHeader(false);
    $dismax = $query->getEDisMax();
    $dismax->setQueryFields('codice_archivio privato note titolo nome_cognome sottotitolo prima_responsabilita altre_responsabilita anno soggetto autore data Keywords Creation-Date modified Last-Modified tipo_delibera argomento_breve testo straordinaria unanimita');
    
    $localParams = "";
    if ($_SESSION['role'] == 'admin') {
        $localParams .= "privato:[* TO 1]";
    } else {
       $localParams .= "privato:0";
    }
    
    if ($subsearch != 0) {
        $firstInsert = 0;
        for ($i=0; $i<10; $i++) {
            if (($subsearch & pow(2, $i)) == pow(2, $i)) {
                if ($firstInsert == 0) {
                    $localParams .= " AND ";
                }

                if ($i == 0) {                                             
                    $localParams .= "(".$member->curlFlBiblio('book_categories').")";
                } else if ($i == 1) {
                    $localParams .= "(".$member->curlFlBiblio('photo_categories').")";
                } else if ($i == 2) {
                    $localParams .= "(".$member->curlFlBiblio('video').")";
                } else if ($i == 3) { 
                    $localParams .= "(".$member->curlFlBiblio('ebook_categories').")";
                } else if ($i == 4) { 
                    $localParams .= "(".$member->curlFlBiblio('monturato').")";
                } else if ($i == 5) {
                    $localParams .= "(".$member->curlFlBiblio('delibera_categories').")";
                }
                $firstInsert = 1;
            }
        }
    } else {
        $localParams = "";
    }

    $hl = $query->getHighlighting();
    $hl->setSnippets(10);
    $hl->setMergeContiguous(true);
    $hl->setFields('note Keywords testo argomento_breve');
    $hl->setSimplePrefix($GLOBALS['HIGHLIGHT_BEGIN']);
    $hl->setSimplePostfix($GLOBALS['HIGHLIGHT_END']);

    $facetSet = $query->getFacetSet();
    $facetSet->createFacetField('anno')->setField('anno');
    
    $query->addSort('codice_archivio', $query::SORT_ASC);
    
    $perPage = new PerPage();    
    //$paginationlink = "page=";	
    $offset = ($page - 1) * $perPage->perpage;
    if ($offset < 0) $offset = 0;

    $query->setRows($perPage->perpage);
    $query->setStart($offset);
    
    if (strpos(":", $searchValue) !== false) {
        $dismax->setQueryAlternative($localParams." ".$searchValue);
    } else {
        if ($localParams == "") {
            $query->setQuery($searchValue);
        } else {
            $query->createFilterQuery('fq')->setQuery($localParams);
            $query->setQuery($searchValue);
        }
    }    
    //$request = $client->createRequest($query);
    //echo 'Request URI: ' . $request->getUri() . '<br/>';
    
    $resultset = $client->select($query);
    $highlighting = $resultset->getHighlighting();
    
    $total_rows = $resultset->getNumFound();
    $query_result = "Trovati ".$total_rows." risultati in ".$resultset->getQueryTime()." ms (pag. ".$page."/".ceil($total_rows/$perPage->perpage).")";//$perPage->pages

    // FIXME PASSARE ANCHE LA STRINGA DI RICERCA
    $perpageresult = $perPage->getAllPageLinks($total_rows);
     
    //$total_pages = ceil($total_rows / $no_of_records_per_page);

    $tipo_libri = $member->getAllCategories('book_category', true);
    $output = '<div class="results">';
    foreach($resultset as $document) {
        if ($document->tipologia == "MONTURATO") {
            $output .= monturatoOutput($document);
        } else if (in_array($document->tipologia, $tipo_libri)) {
            $output .= libroOutput($document, $highlighting);
        } else if ($document->tipologia == "FOTOGRAFIA") {
            $output .= fotoOutput($document, $highlighting);
        } else if ($document->tipologia == "STAMPA" or $document->tipologia == "LASTRA") {
            $output .= stampaOutput($document, $highlighting);
        } else if ($document->tipologia == "BOZZETTO") {
            $output .= bozzettoOutput($document, $highlighting);
        } else if ($document->tipologia == "PERGAMENA") {
            $output .= pergamenaOutput($document, $highlighting);
        } else if ($document->tipologia == "SONETTO") {
            $output .= sonettoOutput($document, $highlighting);
        } else if ($document->tipologia == "DELIBERA") {
            $output .= deliberaOutput($document, $highlighting);
        } else {
            continue;
        }
        $output .= "<br>";
    }
    $output .= "</div>";
    
    if(!empty($perpageresult)) {
        $output .= '<div id="pagination">' . $perpageresult . '</div>';
    }

    $facet = $resultset->getFacetSet()->getFacet('anno');
    $facet_text = "";
    foreach($facet as $value => $count) {    
        $facet_text .= $value . ' [' . $count . ']<br/>';
    }
    
    $response = array("header"=>$query_result, "body"=>$output, "faceting"=>$facet_text);
    //print ($output);
    print_r(json_encode($response));
}
?>
