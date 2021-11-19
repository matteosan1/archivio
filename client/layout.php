<html>
  <head>
    
  <title>Solr browse: #core_name</title>

  <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
  <link rel="icon" type="image/x-icon" href="#{url_root}/img/favicon.ico"/>
  <link rel="shortcut icon" type="image/x-icon" href="#{url_root}/img/favicon.ico"/>

  <script type="text/javascript" src="#{url_root}/libs/jquery-1.7.2.min.js"></script>
  <link rel="stylesheet" type="text/css" href="#{url_for_solr}/admin/file?file=/velocity/main.css&contentType=text/css"/>
  <link rel="stylesheet" href="#{url_for_solr}/admin/file?file=/velocity/jquery.autocomplete.css&contentType=text/css" type="text/css" />
  <script type="text/javascript" src="#{url_for_solr}/admin/file?file=/velocity/jquery.autocomplete.js&contentType=text/javascript"></script>
  <style>
    #admin{
      text-align: right;
      vertical-align: top;
    }

    #head{
      width: 100%;
    }

    .parsed_query_header {
      font-family: Helvetica, Arial, sans-serif;
      font-size: 10pt;
      font-weight: bold;
    }

    .parsed_query {
      font-family: Courier, Courier New, monospaced;
      font-size: 10pt;
      font-weight: normal;
    }

    body {
      font-family: Helvetica, Arial, sans-serif;
      font-size: 10pt;
    }

    a {
      color: #305CB3;
    }

    em {
      color: #FF833D;
    }

    .facets {
      float: left;
      margin: 5px;
      margin-top: 0px;
      width: 185px;
      padding: 5px;
      top: -20px;
      position: relative;
    }

    .facets h2 {
      background: #EEEEEE;
      padding: 2px 5px;
    }

    .facets ul {
      list-style: none;
      margin: 0;
      margin-bottom: 5px;
      margin-top: 5px;
      padding-left: 10px;
    }

    .facets ul li {
      color: #999;
      padding: 2px;
    }

    .facet-field {
      font-weight: bold;
    }

    .field-name {
      font-weight: bold;
      // align="right" valign="top"
    }

    .highlighted-facet-field {
      background: white;
    }

    .constraints {
      margin-top: 10px;
    }

    #query-form{
      width: 80%;
    }

    .query-box, .constraints {
      padding: 5px;
      margin: 5px;
      font-weight: normal;
      font-size: 24px;
      letter-spacing: 0.08em;
    }

    .query-box #q {
      margin-left: 8px;
      width: 60%;
      height: 50px;
      border: 1px solid #999;
      font-size: 1em;
      padding: 0.4em;
    }

    .query-box {

    }

    .query-box .inputs{
      left: 380px;
      position: relative;

    }

    #logo {
      width: 115px;
      margin: 0px 0px 20px 12px;
      border-style: none;
    }

    .pagination {
      padding-left: 33%;
      background: #eee;
      margin: 5px;
      margin-left: 210px;
      padding-top: 5px;
      padding-bottom: 5px;
    }

    .result-document {
      border: 1px solid #999;
      padding: 5px;
      margin: 5px;
      margin-left: 210px;
      margin-bottom: 15px;
    }

    .result-document div{
      padding: 5px;
    }

    .result-title{
      width:60%;
    }

    .result-body{
      background: #ddd;
    }

    .mlt{

    }

    .result-document:nth-child(2n+1) {
      background-color: #eee;
    }


    .selected-facet-field {
      font-weight: bold;
    }

    li.show {
      list-style: disc;
    }

    .error {
      color: white;
      background-color: red;
      left: 210px;
      width:80%;
      position: relative;

    }
  </style>
</head>
  <body>
    <div id="head" align="center">
      <a href="http://www.istrice.org">
	<img src="#url_root/img/titolo.png"/>
      </a>
    </div>
    <div id="content">
      <div class="error">
	
	#if( $response.response.error.code )
	   <h1>ERROR $response.response.error.code</h1>
  $response.response.error.msg
#end
</div>

<div class="query-box">
  <form id="query-form" action="#{url_for_home}" method="GET">
    Ricerca:
    <input type="text" id="q" name="q" value="$!esc.html($request.params.get('q'))"/>
    <input type="hidden" name="sort" value="codice_archivio asc,tipologia asc"/>
    <input type="submit" value="Invia"/>
    <input type="reset" value="Reset"/>
    
    ###if($debug) ## TODO: this would automatically happen when arbitrary parameters are kept on URLs
    ##  <input type="hidden" name="debug" value="true"/>
    ###end
    ###foreach($fq in $request.params.getParams('fq'))
    ##  <input type="hidden" name="fq" id="allFQs" value="$esc.html($fq)"/>
    ###end

    ##<div class="constraints">
    ##  #foreach($fq in $request.params.getParams('fq'))
    ##    #set($previous_fq_count=$velocityCount - 1)
    ##    #if($fq != '')
    ##      &gt;
    ##      <a href="#url_for_filters($request.params.getParams('fq').subList(0,$previous_fq_count))">$fq</a>
    ##    #end
    ##  #end
    ##</div>

    ##<div class="parsed_query_header">
    ##  #if($debug)
    ##    <a href="#" onclick='jQuery(this).siblings("div").toggle(); return false;'>toggle parsed query</a>
    ##    <div class="parsed_query" style="display:none">$response.response.debug.parsedquery</div>
    ##  #end
    ##</div>     #{url_for_solr}/admin/file?file=/velocity/istruzioni.js

  </form>
</div>
##<div>
##<script type="application/javascript" src="#{url_for_solr}/admin/file?file=/velocity/istruzioni.js"></script>
##<button id="amazing">Am I amazing ?</button>
##
##<br>
##Per ricercare una parola che si pu&ograve; trovare all'interno di un qualsiasi 
##campo &egrave; sufficiente digitarla e premere il tasto "Invia" (es. aceto). <br>
##Per la ricerca di una parte di parola &egrave; aggiungere un * (asterisco)
##alla fine (o all'inizio) (es. acet*).
##Se si vuole ricercare una parola in un campo specifico usare la seguente notazione
##campo:parola (es. soggetto:costumi). <br>
##I campi disponibili per la ricerca sono: 
##<ul>
##<li>codice_archivio</li>
##<li>tipologia</li>
##<li>titolo</li>
##<li>sottotitolo</li>
##<li>prima_responsabilita</li>
##<li>altre_responsabilita</li>
##<li>luogo</li>
##<li>edizione</li>
##<li>ente</li>
##<li>serie</li>
##w<li>anno</li>
##<li>descrizione</li>
##<li>soggetto</li>
##<li>note</li>
##</ul><br>
##La ricerca nei documenti elettronici viene automaticamente effettuata sul testo.<br>
##Per le immagini la ricerca avviene invece nei tag EXIF.<br>
##</div>

</div>
</p>
<br><br>
<div class="facets">
  #**
 *  Display facets based on field values
 *  e.g.: fields specified by &facet.field=
 *#

#if($response.facetFields.size() > 0)
  <h2>Filtri</h2>

  #foreach($field in $response.facetFields)
    ## Hide facets without value
    #if($field.values.size() > 0)
      <span class="facet-field">$field.name</span>
      <ul>
        #foreach($facet in $field.values)
          <li>
            <a href="#url_for_facet_filter($field.name, $facet.name)" title="$esc.html($facet.name)">
              #if($facet.name!=$null)$esc.html($display.truncate($facet.name,20))#else<em>missing</em>#end</a> ($facet.count)
          </li>
        #end
      </ul>
    #end  ## end if > 0
  #end    ## end for each facet field
#end      ## end if response has facet fields

</div>

<div class="pagination">
  #link_to_previous_page("Prec.")
  <span>
    <span class="results-found">$page.results_found</span>
    results found in
    ${response.responseHeader.QTime}ms -
  </span>

  $resource.page_of.insert($page.current_page_number,$page.page_count)
  #link_to_next_page("Succ.")	
</div>

## Render Results, actual matching docs
<div class="results">
#foreach($doc in $response.results)
  #set($video = $doc.getFieldValue('video'))
  #set($ct = $doc.getFieldValue('content_type'))
  #if ($ct == "[image/jpeg]")
      #parse("hit_image.vm")
  #elseif ($ct == "[image/tiff]")
      #parse("hit_tiff.vm")
  #elseif ($ct == "[application/pdf]")
      #parse("hit_pdf.vm")
  #elseif ($video)
      #parse("hit_video.vm")
  #else
      #parse("hit.vm")
  #end  
#end

</div>

<div class="pagination">
  #link_to_previous_page("Prec.")

  <span class="results-found">$page.results_found</span>
  results found.

  $resource.page_of.insert($page.current_page_number,$page.page_count)

  #link_to_next_page("Succ.")
</div>

    </div>
    
    <div id="footer">
      <hr>
    </div>
  </body>
</html>
