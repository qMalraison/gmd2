<?php

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
    echo $newnewline[0];
    echo "\t";
    echo $newnewline [1];
    echo "<br>";

}

  ?>
