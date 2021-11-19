<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

echo "PROVA";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://localhost:8984/solr/prova5/select?q=*:*&wt=json");
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                                           'Accept: application/json'));
curl_setopt($ch, CURLOPT_USERPWD, "solr:SolrRocks");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_CAINFO, "/Users/sani/site/Usered/client/solr-ssl.pem");

curl_setopt($ch, CURLOPT_VERBOSE, true);

$data = curl_exec($ch);                                                     
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
}
print_r ($data);
curl_close($ch);
?>