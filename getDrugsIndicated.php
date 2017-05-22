<?php


// MySQL SIDER
$servername = "neptune.telecomnancy.univ-lorraine.fr";
$username = "gmd-read";
$password = "esial";
$dbname = "gmd";

// Create connection de MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM meddra_all_indications where meddra_concept_name IN (";

foreach ($listSynonymForDrugs as &$value) {
    $sql = $sql."'".$value."',";
}
$sql = substr($sql, 0, -1);
$sql = $sql.")";
// getting Sider elements
$resultSider = $conn->query($sql);


// For all Sider results get Atc ids
$finalResults = [];
if ($resultSider->num_rows > 0) {
    //echo "<table><tr><th>ID</th><th>Name</th></tr>";
    // output data of each row
    while($row = $resultSider->fetch_assoc()) {
      $idSt = $row["stitch_compound_id1"];
      $atcId = getAtcId($idSt);

      array_push($finalResults, $atcId);
    }
}
$conn->close();

// get medicament name list
$medicamentIndicatedList = getMedicamentListName($finalResults);




// Function to get ATC id by Stitch ID
function getAtcId($idStitch):string
{
  // *:* is equivalent to telling solr to return all docs
  //echo str_replace("CID1","CIDm",$idStitch);
  $config = array(
      'endpoint' => array(
          'localhost' => array(
              'host' => '127.0.0.1', 'port' => '8983', 'path' => '/solr/', 'core' => 'stitch'
          )
      )
  );
  // new Solarium Client object
  $client = new Solarium\Client($config);

  $query = $client->createSelect();

  $query->setQuery("id:".str_replace("CID1","CIDm",$idStitch));
  $query->setFields(array('id','atc_id'));

  $resultSet = $client->select($query);

  $returnVal = "";
  if (count($resultSet)==1) {
    foreach ($resultSet as $result) {
        $returnVal = implode($result->atc_id);
    }
  }
  return $returnVal;

}



// Function to get Medicament list name  by list of Stitch Ids
function getMedicamentListName($listIdAtc) {

  $medocList = [];

  $file_handle = fopen("br08303.keg", "r");

  while (!feof($file_handle)) {
     $line = fgets($file_handle);
      if ($line[0] == "B") {
        $separator = "  ";
      }

      elseif ($line[0] == "C") {
        $separator = "    ";
      }
          elseif ($line[0] == "D") {
        $separator = "      ";
      }
          elseif ($line[0] == "E") {
        $separator = "        ";
      }
          elseif ($line[0] == "F") {
        $separator = "          ";
      }
      if ($line[0] == "A") {
        $newnewline = explode(" ", $line, 2);
      }
      else {
        $newline = explode($separator, $line);
        $newnewline = explode(" ", $newline[1], 2);
      }

      if(in_array($newnewline[0], $listIdAtc) && $newnewline[1]!= null) {
        array_push($medocList, $newnewline[1]);
      }
  }

  return $medocList;
}





 ?>
