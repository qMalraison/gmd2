<?php

// ONIM

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


 ?>
