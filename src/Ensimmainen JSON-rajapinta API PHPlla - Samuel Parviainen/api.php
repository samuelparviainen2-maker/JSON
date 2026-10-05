<?php
 
header("Content-Type: application/json");
 
$opiskelija = [
"nimi" => "Matti",
"ika" => 18,
"ala" => "Ohjelmistokehitys",
"kaupunki" => "Kuopio",
"sahkoposti" => "maija@example.com",
"puhelin" => "0401234567"
];
 
echo json_encode($opiskelija);
 
?>