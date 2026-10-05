<?php
 
header("Content-Type: application/json");
 
$json = file_get_contents("opiskelijat.json");
 
$opiskelijat = json_decode($json, true);
 
$id = $_GET["id"];
 
$loytyi = false;
 
foreach ($opiskelijat as $opiskelija) {
 
if ($opiskelija["id"] == $id) {
echo json_encode($opiskelija);
$loytyi = true;
}
}
 
if (!$loytyi) {
echo json_encode([
"virhe" => "Opiskelijaa ei loytynyt"
]);
}
 
?>

