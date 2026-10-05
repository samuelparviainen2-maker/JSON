<?php
 
$json = file_get_contents("opiskelijat.json");
 
$opiskelijat = json_decode($json, true);
 
$uusiOpiskelija = [
"id" => 4,
"nimi" => "Liisa",
"ala" => "Peliohjelmointi"
];
 
$opiskelijat[] = $uusiOpiskelija;
 
$json = json_encode($opiskelijat, JSON_PRETTY_PRINT);
 
file_put_contents("opiskelijat.json", $json);
 
echo "Opiskelija lisätty.";
 
?>