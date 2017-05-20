<<?php

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

 ?>
