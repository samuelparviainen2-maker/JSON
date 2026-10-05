<?php
 
$json = file_get_contents("opiskelijat.json");
 
$opiskelijat = json_decode($json, true);
 
foreach ($opiskelijat as &$opiskelija) {
 
if ($opiskelija["id"] == 1) {
 
$opiskelija["nimi"] = "Matti Meikäläinen";
$opiskelija["ala"] = "Peliohjelmointi";
 
}
 
}
 
$json = json_encode($opiskelijat, JSON_PRETTY_PRINT);
 
file_put_contents("opiskelijat.json", $json);
 
echo "Opiskelijan tiedot päivitetty.";
 
?>