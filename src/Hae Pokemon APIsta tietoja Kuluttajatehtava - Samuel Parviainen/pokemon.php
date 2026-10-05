<?php
 
$url = "https://pokeapi.co/api/v2/pokemon/pikachu";
 
$json = file_get_contents($url);
 
$pokemon = json_decode($json, true);
 
echo $pokemon["name"]. '<br>';
echo 'Paino '.$pokemon["weight"]. '<br>';
echo 'Pituus '.$pokemon["height"];
?>