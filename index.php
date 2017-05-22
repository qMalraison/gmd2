<?php
error_reporting(0);
// bootstrap.php
require_once "vendor/autoload.php";
require_once "PhpObo/LineReader.php";
require_once "PhpObo/Parser.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\CouchDB\HTTP\SocketClient;
use PhpObo\LineReader, PhpObo\Parser;



$NomSymptome = $_GET["Symptome"];

// List de tout les synonyms alimenter par hp.obo
$listSynonym = array($NomSymptome);



require_once "obohpo.php";

require_once "orpha.php";

require_once "omim.php";

require_once "getDrugsSE.php";




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

    <table id="deseas" class="display" width="100%" cellspacing="0">
      <thead>
      <tr>
          <th>Database</th>
          <th>Type</th>
          <th>Cause</th>
          <th>CUID</th>
          <th>ClinicalSign</th>
          <th>Show list med</th>
      </tr>
  </thead>
  <tfoot>
      <tr>
        <th>Database</th>
        <th>Type</th>
        <th>Cause</th>
        <th>CUID</th>
        <th>ClinicalSign </th>
        <th>Show list med</th>
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
                <td><a href=\"listMed.php?db=ONIM&name=".urlencode($value[0])."\">link</a></td>
              </tr>");
    }


    foreach ($resultOrpha as &$value) {

        print("<tr>
                <td>Orpha</td>
                <td>Disease</td>
                <td>".$value['Name']['text']."</td>
                <td>-</td>
                <td>".$listSynonym[0]."</td>
                <td><a href=\"listMed.php?db=Orpha&name=".urlencode($value['OrphaNumber'])."\">link</a></td>
              </tr>");
    }

     ?>

   </tbody>
  </table>

  <table id="drugsSE" class="display" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>Drugs causing side effects</th>
        <th>Show list med</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th>Drugs causing side effects</th>
        <th>Show list med</th>
      </tr>
    </tfoot>
    <tbody>

      <?php

      // Tab ONIM
      foreach ($medicamentSEList as &$value) {

        print("<tr>
        <td>".$value."</td>
        <td><a href=\"listMed.php?db=ONIM&name=".urlencode($value[0])."\">link</a></td>
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
      $('#deseas').DataTable();
      $('#drugsSE').DataTable();
    } );
    </script>
</html>
