<?php
header("Content-Type: application/json; charset=utf-8");

require 'yhteys.php';


$metodi = $_SERVER["REQUEST_METHOD"];

if ($metodi === "GET") {
    $lause = $conn->query("SELECT kirja_id, nimi, kirjailija, julkaisuvuosi, hyllypaikka FROM kirja");
    $vastaus = $lause->fetchAll();
    echo json_encode($vastaus, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($metodi === "POST") {
    $tiedot = json_decode(file_get_contents("php://input"), true);
    $nimi = $tiedot["nimi"] ?? "";
    $kirjailija = $tiedot["kirjailija"] ?? "";
    $julkaisuvuosi = $tiedot["julkaisuvuosi"] ?? "";
    $hyllynumero = $tiedot["hyllypaikka"] ?? "";

    $lause = $conn->prepare("INSERT INTO kirja (nimi, kirjailija, julkaisuvuosi, hyllypaikka) VALUES (:nimi, :kirjailija, :julkaisuvuosi, :hyllypaikka)");

    $lause->bindValue(":nimi", $nimi, PDO::PARAM_STR);
    $lause->bindValue(":kirjailija", $kirjailija, PDO::PARAM_STR);
    $lause->bindValue(":julkaisuvuosi", $julkaisuvuosi, PDO::PARAM_STR);
    $lause->bindValue(":hyllypaikka", $hyllynumero, PDO::PARAM_STR);
    $lause->execute();

    echo json_encode([
        "onnistui" => true,
        "viesti" => "Kirja lisätty onnistuneesti."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($metodi === "PUT") {
    $tiedot = json_decode(file_get_contents("php://input"), true);
    $kirjaId = filter_var($tiedot["kirja_id"] ?? null, FILTER_VALIDATE_INT);
    $nimi = trim((string) ($tiedot["nimi"] ?? ""));
    $kirjailija = trim((string) ($tiedot["kirjailija"] ?? ""));
    $julkaisuvuosi = filter_var($tiedot["julkaisuvuosi"] ?? null, FILTER_VALIDATE_INT);
    $hyllynumero = filter_var($tiedot["hyllypaikka"] ?? null, FILTER_VALIDATE_INT);

    if (
        $kirjaId === false || $kirjaId < 1 ||
        $nimi === "" || $kirjailija === "" ||
        $julkaisuvuosi === false || $julkaisuvuosi < 1901 || $julkaisuvuosi > 2155 ||
        $hyllynumero === false || $hyllynumero < 0
    ) {
        http_response_code(400);
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Virheelliset kirjan tiedot."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $lause = $conn->prepare("UPDATE kirja SET nimi = :nimi, kirjailija = :kirjailija, julkaisuvuosi = :julkaisuvuosi, hyllypaikka = :hyllypaikka WHERE kirja_id = :kirja_id");
    $lause->bindValue(":nimi", $nimi, PDO::PARAM_STR);
    $lause->bindValue(":kirjailija", $kirjailija, PDO::PARAM_STR);
    $lause->bindValue(":julkaisuvuosi", $julkaisuvuosi, PDO::PARAM_INT);
    $lause->bindValue(":hyllypaikka", $hyllynumero, PDO::PARAM_INT);
    $lause->bindValue(":kirja_id", $kirjaId, PDO::PARAM_INT);
    $lause->execute();

    if ($lause->rowCount() === 0) {
        $tarkistus = $conn->prepare("SELECT 1 FROM kirja WHERE kirja_id = :kirja_id");
        $tarkistus->bindValue(":kirja_id", $kirjaId, PDO::PARAM_INT);
        $tarkistus->execute();

        if (!$tarkistus->fetchColumn()) {
            http_response_code(404);
            echo json_encode([
                "onnistui" => false,
                "viesti" => "Kirjaa ei löytynyt."
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    echo json_encode([
        "onnistui" => true,
        "viesti" => "Kirjan tiedot päivitetty."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

