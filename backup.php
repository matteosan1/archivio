<?php
$ch = curl_init();

//curl_setopt($ch, CURLOPT_URL, 'http://lx000000010550.sum.local:8983/solr/admin/cores?action=status&core=mpscs_docs&indexInfo=true');

curl_setopt($ch, CURLOPT_URL, 'http://lx000000010550.sum.local:8983/solr/commodity/select?fl=last_modified&q=*:*&rows=1&sort=last_modified%20desc');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

$data = curl_exec($ch);
$dict = json_decode($data, true);

$day_before = date( 'Y-m-d\T\0\0\:\0\0\:\0\0\Z', strtotime( $dict['response']['docs'][0]['last_modified'].' -1 day'));
curl_close($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://lx000000010550.sum.local:8983/solr/commodity/select?fl=last_modified&fq=last_modified:['.$day_before.'%20TO%20NOW]&q=*:*');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
$data = curl_exec($ch);
echo $data;

curl_close($ch);
?>
