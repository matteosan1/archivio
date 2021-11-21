<?php

function checkServer() {
    if (file_exists('config.php')) {
        require('config.php');
    }

    require __DIR__.'/../vendor/autoload.php';
    $adapter = new Solarium\Core\Client\Adapter\Curl();
    $eventDispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();
    $client = new Solarium\Client($adapter, $eventDispatcher, $config);

    $ping = $client->createPing();

    try {
        $result = $client->ping($ping);
    } catch (Exception $e) {
        echo "<div style='color:red' align='center'>Il server Solr non &egrave; attivo. Contattare l'amministratore del sistema.</div>";
    }
}
?>