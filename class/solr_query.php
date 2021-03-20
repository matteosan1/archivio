<?php

require_once '../view/session.php';
require_once '../view/config.php';
require_once '../view/solr_client.php';

error_reporting(E_ALL);
ini_set('display_errors', true);

$role = $_SESSION['role'];

if (isset($_GET)) {

   $searchValue = $_GET['q'];
   $query = $client->createSelect();

   if (strpos($searchValue, ':') === false) {
      $dismax = $query->getDisMax();
      //$dismax->setBoostQuery('cat:"graphics card"^2');
   }

   $query->setOmitHeader(false);
   $query->setQuery($searchValue);

   $query->addSort('codice_archivio', $query::SORT_ASC);
   $query->addSort('tipologia', $query::SORT_ASC);

   /////////////////////////////////////////////////

   $facetSet = $query->getFacetSet();
   $facetSet->createFacetField('tipo')->setField('tipologia');
   $facetSet->createFacetField('anno')->setField('anno');

   /////////////////////////////////////////////////

   $resultsPerPage = 20;
   if (isset($_GET['currentPage'])) {
      $currentPage = intval($_GET['currentPage']);
   } else {
      $currentPage = 1;
   }
   $query->setRows($resultsPerPage);
   $query->setStart(($currentPage - 1) * $resultsPerPage);

   $resultset = $client->select($query);

   $result = array();
   $result['numFound'] = $resultset->getNumFound();
   $result['queryTime'] = $resultset->getQueryTime();
   $result['currentPage'] = $currentPage;
   $found = intval($resultset->getNumFound());
   $result['totalPages'] = $found == 0 ? 1:((int)($found / $resultsPerPage));
   if (($found % $resultsPerPage) != 0) {
      $result['totalPages'] += 1;
   }
   $result['docs'] = array();

   $fieldConversion = array("codice_archivio" => "Codice", "titolo" => "Titolo",
   		      	    "prima_responsabilita" => "Autore",
			    "altre_responsabilita" => "Altri autori",
			    "anno" => "Anno", "luogo" => "Luogo",
			    "tipologia" => "Tipologia", "descrizione" => "Descrizione",
			    "edizione" => "Edizione", "ente" => "Ente",
			    "soggetto" => "Soggetto", "note" => "Note",
			    "sottotitolo" => "Sottotitolo", "serie" => "Serie",
			    "cdd" => "CDD");


   foreach ($resultset as $document) {
      //if ($role == "archive") {
      //	 if (array_key_exists("private", $document)) {
      //	    if ($document['private'] == 1) {
      //	       continue;
      //	    }
      //   }
      //}
      //
      //if ($role == "photo") {
      //  if ($document['tipologia'] != "FOTO" && $document['tipologia'] != "VIDEO") {
      //     continue;
      //	}
      //
      //	 if (array_key_exists("private", $document)) {
      //	    if ($document['private'] == 1) {
      //	       continue;
      //	    }
      //   }
      //}
      
      $writeNote = false;
      $s = '<hr/><table>';
      
      $i = 0;
      foreach ($document as $field => $value) {
        if (($field == "_version_") || ($field == "score") || ($field == "timestamp")) {
	   continue;
	}
	
	if ($field == "note" && $value !== "") {
	   $writeNote = true;
	   continue;
	}   
	
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
	
	if (($value !== "") && (array_key_exists($field, $fieldConversion))) {
	   if ($i == 0) {
	       $i += 1;
	       $filename = $document['codice_archivio']. '.JPG.';
	       $s = $s.'<tr><th align="right">'.$fieldConversion[$field].'</th><td width=80%>'.$value.'</td><td width=20% rowspan='. strval(count($document) - 4) .'> <img height=199 src="/copertine/' . $filename . '" onerror="this.onerror=null;this.src=/copertine/no_image.png;"/></td></tr>';
	   } else {
	       $s = $s.'<tr><th valign="top" align="right">'.$fieldConversion[$field].'</th><td align="justify">'.$value.'</td></tr>';
	   }
	}   
      }      
      $s = $s.'</table>';
      if ($writeNote) {
      	 $s = $s."<br><div class=\"result-body\"><b>NOTE: </b>".$document['note']."</div>";
      }

      array_push($result['docs'], $s);
   }

   /////////////////////////////////////////////////
   $result['facets_tipo'] = array();
   $facet = $resultset->getFacetSet()->getFacet('tipo');
   foreach ($facet as $value => $count) {
       if (strpos($value, "_") !== false) {
          $items = explode("_", $value);
	  $value = substr($items[0], 0, 3) . ". " . end($items);
       }
       $s = $value . ' [' . $count . ']<br/>';
       array_push($result['facets_tipo'], $s);
   }

   $result['facets_anno'] = array();
   $facet = $resultset->getFacetSet()->getFacet('anno');
   foreach ($facet as $value => $count) {
       if (strpos($value, "_") !== false) {
          $items = explode("_", $value);
	  $value = substr($items[0], 0, 3) . ". " . end($items);
       }
       
       $s = $value . ' [' . $count . ']<br/>';
       array_push($result['facets_anno'], $s);
   }

   //////////////////////////////////////////////////
   if ($currentPage > 1) {
       $result['prevPage'] = $currentPage - 1;    
   }

   if ($currentPage < $result['totalPages']) {
      $result['nextPage'] = $currentPage + 1;
   }

   $result['query'] = $searchValue;
}

//print_r ($result['prec']);

echo json_encode($result);

?>
