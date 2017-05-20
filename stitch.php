<?php

// STITCH

//1. Connecting to Solr
$config = array(
    'endpoint' => array(
        'localhost' => array(
            'host' => '127.0.0.1', 'port' => '8983', 'path' => '/solr/', 'core' => 'stitch'
        )
    )
);
// new Solarium Client object
$client = new Solarium\Client($config);
/* CGARGEMENT index
// 2. Stitch Parsing file

//Open the file.
$fileHandle = fopen("chemical.sources.v5.0.tsv", "r");
//Loop through the CSV rows.

$i=0;
while (($row = fgetcsv($fileHandle, 0, "	")) !== FALSE) {
    //Creating docuemnt to index with Solr
    //var_dump($row);


    if(strcmp($row[2], "ATC")==0) {
      $update = $client->createUpdate();
      $doc1 = $update->createDocument();

      $doc1->id = $row[0];
      $doc1->atc_id = $row[3];

      $update->addDocument($doc1);
      $update->addCommit();
      $client->update($update);
    }


}
*/
$query = $client->createSelect();

// *:* is equivalent to telling solr to return all docs
$query->setQuery('id:CIDm00002194');
$query->setFields(array('id','atc_id'));


$resultSet = $client->select($query);

echo '<div class="search-results">';
foreach ($resultSet as $result) {
    echo '<div class="search-result">';
    echo '<p>' . $result->id . '</p>';
    print_r($result->atc_id);
    echo '</div>';
}
echo '</div>';


 ?>
