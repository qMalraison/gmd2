<?php

// ORPHA


$client = \Doctrine\CouchDB\CouchDBClient::create(array('dbname' => 'orphadatabase',
                                                        'port' => '80',
                                                        'host' => 'couchdb.telecomnancy.univ-lorraine.fr') );



$query = $client->createViewQuery('clinicalsigns', 'GetDiseaseByClinicalSign');
$result = $query->setKeys($listSynonym)->execute();

foreach ($result as &$value) {
    print($value['value']['disease']['Name']['text']);
    print("<br>");
}

 ?>
