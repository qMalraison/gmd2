<?php
error_reporting(E_ALL);
ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('memory_limit', '2048M');
set_time_limit(0);
// bootstrap.php
require_once "vendor/autoload.php";
require_once "PhpObo/LineReader.php";
require_once "PhpObo/Parser.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\CouchDB\HTTP\SocketClient;
use PhpObo\LineReader, PhpObo\Parser;

$paths = array("/path/to/entity-files");
$isDevMode = false;

$NomSymptome = $_GET["Symptome"];

// the connection configuration

$handle = fopen('hp.obo', 'r');
$lineReader = new LineReader($handle);

//parse file
$parser = new Parser($lineReader);
$parser->retainTrailingComments(true);
$parser->getDocument()->mergeStanzas(false); //speed tip
$parser->parse();
//loop through Term stanzas to find obsolete terms


$listSynonym = array($NomSymptome);

$terms = array_filter($parser->getDocument()->getStanzas('Term'), function($stanza) {
    return (!isset($stanza['is_obsolete']) & isset($stanza['synonym']) &  (stripos($stanza['name'], $_GET["Symptome"]) !== false)  );
});
foreach ($terms as $term) {
  foreach ($term['synonym'] as $synonym) {

    preg_match('#\"(.*?)\"#', $synonym, $parse);

            array_push($listSynonym , $parse[1]);
  }

}

$listSynonym = array_unique($listSynonym);

var_dump($listSynonym);

echo "<br>HPO<br>";

// fichier hpo_annotations.sqlite
$dir = 'sqlite:hpo_annotations.sqlite';
$dbh  = new PDO($dir) or die("cannot open the database");
$query =  "SELECT * FROM phenotype_annotation";
foreach ($dbh->query($query) as $row)
{
    //var_dump($row);
    break;
}




// ORPHA

/*
$client = \Doctrine\CouchDB\CouchDBClient::create(array('dbname' => 'orphadatabase',
                                                        'port' => '80',
                                                        'host' => 'couchdb.telecomnancy.univ-lorraine.fr') );



$query = $client->createViewQuery('clinicalsigns', 'GetDiseaseByClinicalSign');
$result = $query->setKeys($listSynonym)->execute();

foreach ($result as &$value) {
    print($value['value']['disease']['Name']['text']);
    print("<br>");
}



// Stitch

/*
//Open the file.
$fileHandle = fopen("chemical.sources.v5.0.tsv", "r");

//Loop through the CSV rows.
while (($row = fgetcsv($fileHandle, 0, "	")) !== FALSE) {
    //Dump out the row for the sake of clarity.
    var_dump($row);
}

/*
// MySQL SIde
$servername = "neptune.telecomnancy.univ-lorraine.fr";
$username = "gmd-read";
$password = "esial";
$dbname = "gmd";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM meddra_all_se where side_effect_name LIKE";

foreach ($listSynonym as &$value) {
    $sql = $sql."side_effect_name LIKE '%".$value."%' OR ";
}

$sql = substr($sql, 0, -3);
$sql = $sql." GROUP BY cui";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table><tr><th>ID</th><th>Name</th></tr>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row["cui"]."</td><td>".$row["side_effect_name"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}
$conn->close();


*/
// ONIM

echo "debut<br>";

$onimResult = array();

$handle = @fopen("omim.txt", "r");
if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {

      if ($buffer  == "*FIELD* TI\n"){

        if(($buffer = fgets($handle, 4096)) !== false){
          $lastMaladie = $buffer;

        }
      }

      if ($buffer  == "*FIELD* CS\n"){

          while (($buffer = fgets($handle, 4096)) !== false) {
              if ($buffer  == "*FIELD* TI\n"){
                break;
              }

              $found = false;
              foreach ($listSynonym as &$NomSymptome) {
                if(stripos($buffer, $NomSymptome) !== false){

                  $onimRow = array($lastMaladie,$buffer);
                  array_push($onimResult , $onimRow);
                  $found = true;
                  break;
                }
              }
              if ($found) {
                break;
              }

          }
      }
    }
    fclose($handle);
}


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

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/jquery.dataTables.min.css">
  </head>
  <body>
    <h1 style="text-align:center">Projet GMD</h1>

    <div class="container">
      <div class="row" style="text-align:center">



        <div class="col-12">
          <form class="" action="index.php" method="get">
          Symptome <input type="text" name="Symptome" value="" size="50">
            </form>
        </div>
      </div>

    </div>

    <table id="example" class="display" width="100%" cellspacing="0">
      <thead>
      <tr>
          <th>Database</th>
          <th>Type</th>
          <th>Cause</th>
          <th>CUID</th>
          <th>ClinicalSign</th>
      </tr>
  </thead>
  <tfoot>
      <tr>
        <th>Database</th>
        <th>Type</th>
        <th>Cause</th>
        <th>CUID</th>
        <th>ClinicalSign  </th>
      </tr>
  </tfoot>
  <tbody>

    <?php



    // Tab ONIM

    foreach ($onimResult as &$value) {

        print("<tr>
                <td>ONIM</td>
                <td>Disease</td>
                <td>".$value[0]."</td>
                <td>-</td>
                <td>".$value[1]."</td>
              </tr>");
    }

     ?>

   </tbody>
  </table>


    </body>

    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js\jquery.dataTables.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
      $('#example').DataTable();
    } );
    </script>
</html>
