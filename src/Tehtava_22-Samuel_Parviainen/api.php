<?php
header("Content-Type: application/json; charset=utf-8");

require 'tietokanta.php';

$metodi = $_SERVER["REQUEST_METHOD"];

if($metodi === "POST"):
$tiedot = json_decode(file_get_contents("php://input"), true)


endif;

if($metodi === "PUT"):

endif;

if($metodi === "DELETE"):

endif;

if($metodi === "GET"):

endif;

?>
