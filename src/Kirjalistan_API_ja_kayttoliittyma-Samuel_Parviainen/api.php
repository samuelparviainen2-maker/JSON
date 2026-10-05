<?php
 
header("Content-Type: application/json; charset=utf-8");
 
$tiedosto = "data.json";
$metodi = $_SERVER["REQUEST_METHOD"];
 
// Luodaan data.json, jos tiedostoa ei vielä ole
if (!file_exists($tiedosto)) {
    file_put_contents($tiedosto, "[]");
}
 
// Luetaan nykyiset tuotteet tiedostosta
$tuotteet = json_decode(
    file_get_contents($tiedosto),
    true
);
 
// Jos JSON-tiedoston sisältö ei ole taulukko,
// käytetään tyhjää taulukkoa
if (!is_array($tuotteet)) {
    $tuotteet = [];
}
 
 
// GET: palautetaan kaikki tuotteet
if ($metodi === "GET") {
 
    echo json_encode(
        $tuotteet,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );
 
    exit;
}
 
 
// POST: lisätään uusi tuote
if ($metodi === "POST") {
 
    $nimi = trim($_POST["Nimi"] ?? "");
    $kirjoittaja = trim($_POST["Kirjoittaja"] ?? "");
    $Julkaisu = trim($_POST["JulkaisuVuosi"] ?? "");
 
    // Tarkistetaan, että nimi ja hinta on annettu
    if ($nimi === "" || $kirjoittaja === ""|| $Julkaisu === "") {
 
        http_response_code(400);
 
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Anna kirjan nimi, kirjoittaja ja julkaisuvuosi."
        ], JSON_UNESCAPED_UNICODE);
 
        exit;
    }
 

 
    // Etsitään seuraava vapaa id
    $uusiId = 1;
 
    foreach ($tuotteet as $tuote) {
        if (isset($tuote["id"]) && $tuote["id"] >= $uusiId) {
            $uusiId = $tuote["id"] + 1;
        }
    }
 
    // Luodaan uusi tuote
    $uusiTuote = [
        "id" => $uusiId,
        "nimi" => $nimi,
        "kirjoittaja" => $kirjoittaja,
        "julkaisuvuosi" => $Julkaisu,
    ];
 
    // Lisätään tuote taulukkoon
    $tuotteet[] = $uusiTuote;
 
    // Tallennetaan koko taulukko data.json-tiedostoon
    $tallennusOnnistui = file_put_contents(
        $tiedosto,
        json_encode(
            $tuotteet,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        )
    );
 
    if ($tallennusOnnistui === false) {
 
        http_response_code(500);
 
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Kirjaa ei voitu tallentaa."
        ], JSON_UNESCAPED_UNICODE);
 
        exit;
    }
 
    http_response_code(201);
 
    echo json_encode([
        "onnistui" => true,
        "viesti" => "Kirja tallennettu.",
        "tuote" => $uusiTuote
    ], JSON_UNESCAPED_UNICODE);
 
    exit;
}
 
 
// Muita pyyntötyyppejä ei sallita
http_response_code(405);
 
echo json_encode([
    "onnistui" => false,
    "viesti" => "Pyyntötyyppiä ei tueta."
], JSON_UNESCAPED_UNICODE);
 