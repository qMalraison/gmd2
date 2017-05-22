<?php

// ORPHA


$resultOrpha = array();

$client = \Doctrine\CouchDB\CouchDBClient::create(array('dbname' => 'orphadatabase',
                                                        'port' => '80',
                                                        'host' => 'couchdb.telecomnancy.univ-lorraine.fr') );




$query = $client->createViewQuery('clinicalsigns', 'GetDiseaseByClinicalSign');
$result = $query->setStartKey($listSynonym[0])->execute();

foreach ($result as &$value) {
    array_push($resultOrpha , $value['value']['disease']);
}



 ?>
