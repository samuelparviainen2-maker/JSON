<?php
 
$id = $_GET["id"];
 
$json = file_get_contents("opiskelijat.json");
 
$opiskelijat = json_decode($json, true);
 
foreach ($opiskelijat as $avain => $opiskelija) {
 
if ($opiskelija["id"] == $id) {
unset($opiskelijat[$avain]);
}
 
}
 
$opiskelijat = array_values($opiskelijat);
 
$json = json_encode($opiskelijat, JSON_PRETTY_PRINT);
 
file_put_contents("opiskelijat.json", $json);
 
echo "Opiskelija poistettu.";
 
?>