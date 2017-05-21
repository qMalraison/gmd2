<?php

$db = $_GET["db"];
$name = $_GET["name"];


/* AFFICHE TOUTE LA PAGE DE LA MALADIE
$onimResult = array();

$handle = @fopen("omim.txt", "r");
if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {

      if ($buffer  == "*FIELD* TI\n"){

        if(($buffer = fgets($handle, 4096)) !== false){

          if(stripos($buffer, $name) !== false){
            echo "trouvé";
            echo "<br>";
            while (($buffer = fgets($handle, 4096)) !== false) {
              echo $buffer;
              echo "<br>";
                if ($buffer  == "*RECORD*\n"){
                  break;
                }
            }
              break;
        }
      }
    }
  }
  fclose($handle);
}

*/
$onimResult = array();

$listOmimResult = explode(" ", $name);

$OmimNumber = substr($listOmimResult[0], 1);

var_dump($OmimNumber);

$handle = @fopen("omim_onto.csv", "r");
if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {

    if (stripos($buffer, $OmimNumber) !== false){

        $onimResult = explode(",", $buffer);
        break;
    }
  }
  fclose($handle);
}


// CUID EN CASE 6
var_dump($onimResult);


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
