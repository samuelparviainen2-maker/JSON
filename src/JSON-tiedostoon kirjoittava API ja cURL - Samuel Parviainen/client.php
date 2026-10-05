<?php
 
$curl = curl_init();
 
curl_setopt($curl, CURLOPT_URL,
"http://localhost/JSON-tiedostoon%20kirjoittava%20API%20ja%20cURL%20-%20Samuel%20Parviainen/api.php");
 
curl_setopt($curl, CURLOPT_POST, true);
 
curl_setopt($curl, CURLOPT_POSTFIELDS, [
"nimi" => "Tietokone",
"hinta" => "10.30"
]);
 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
 
$vastaus = curl_exec($curl);
 
curl_close($curl);
 
echo $vastaus;