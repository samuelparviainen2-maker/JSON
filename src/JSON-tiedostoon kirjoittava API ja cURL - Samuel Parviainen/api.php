<?php
 
$tiedosto = "data.json";
 
if ($_SERVER["REQUEST_METHOD"] == "GET") {
 
header("Content-Type: application/json");
echo file_get_contents($tiedosto);
}
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
$nimi = $_POST["nimi"];
$hinta = $_POST["hinta"];
 
$tuotteet = json_decode(
file_get_contents($tiedosto),
true
);
 
$tuotteet[] = [
"nimi" => $nimi,
"hinta" => $hinta
];
 
file_put_contents(
$tiedosto,
json_encode($tuotteet, JSON_PRETTY_PRINT)
);
 
echo "Tuote tallennettu";
}