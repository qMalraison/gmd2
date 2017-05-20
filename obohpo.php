<?php

use PhpObo\LineReader, PhpObo\Parser;

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

 ?>
