<?php
 
header("Content-Type: application/json; charset=utf-8");
 
$tiedosto = "data.json";
$metodi = $_SERVER["REQUEST_METHOD"];

if(!file_exists($tiedosto))
    {
        file_put_contents($tiedosto, "[]");
    }

$tapahtumat = json_decode(
    file_get_contents($tiedosto),
    true
);

if(!is_array($tapahtumat))
    {
        $tapahtumat = [];
    }

if($metodi === "GET")
    {
        echo json_encode(
            $tapahtumat,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
        exit;
    }

if ($metodi === "POST") {
    $nimi = trim($_POST["nimi"] ?? "");
    $paikka = trim($_POST["paikka"] ?? "");
    $pvm = trim($_POST["pvm"] ?? "");
    $osallistujamaara = $_POST["osallistujamaara"] ?? "";

    if ($nimi === "" || $paikka === "" || $pvm === "" || $osallistujamaara === "") {
        http_response_code(400);
 
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Anna tapahtuman nimi, paikka, päivämäärä ja osallistujamäärä."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        exit;
        }

    if (!is_numeric($osallistujamaara)) {
        http_response_code(400);
 
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Osallistujamäärän täytyy olla numero."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        exit;
        }


        $uusiId = 1;
 
        foreach ($tapahtumat as $tapahtuma) {
            if (isset($tapahtuma["id"]) && $tapahtuma["id"] >= $uusiId) {
                $uusiId = $tapahtuma["id"] + 1;
            }
        }

        $uusitapahtuma = [
        "id" => $uusiId,
        "nimi" => $nimi,
        "paikka" => $paikka,
        "pvm" => $pvm,
        "osallistujamaara" => (int) $osallistujamaara
    ];

    $tapahtumat[] = $uusitapahtuma;

    $tallennusOnnistui = file_put_contents(
        $tiedosto,
        json_encode(
            $tapahtumat,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        )
    );

        if ($tallennusOnnistui === false) {
 
        http_response_code(500);
 
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Tapahtumaa ei voitu tallentaa."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        exit;
    }
 
    http_response_code(201);
 
    echo json_encode([
        "onnistui" => true,
        "viesti" => "Tapahtuma tallennettu.",
        "tapahtuma" => $uusitapahtuma
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    exit;
}
 
 
// Muita pyyntötyyppejä ei sallita
http_response_code(405);
 
echo json_encode([
    "onnistui" => false,
    "viesti" => "Pyyntötyyppiä ei tueta."
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    