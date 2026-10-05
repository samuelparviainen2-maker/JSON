<?php
header("Content-Type: application/json; charset=utf-8");

require 'tietokanta.php';

$metodi = $_SERVER["REQUEST_METHOD"];

if ($metodi === "GET") {
    $lause = $conn->query("SELECT ID AS id, omistaja, elain, laji, ika, puhelin, kayntipaiva, kellonaika, syy, tila FROM ajanvaraukset");
    $vastaus = $lause->fetchAll();
    echo json_encode($vastaus, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if($metodi === "POST") {
    $tiedot = json_decode(file_get_contents("php://input"), true);
    $omistaja = $tiedot["omistaja"] ?? "";
    $elain = $tiedot["elain"] ?? "";
    $laji = $tiedot["laji"] ?? "";
    $ika = $tiedot["ika"] ?? "";
    $puhelin = $tiedot["puhelin"] ?? "";
    $kayntipaiva = $tiedot["kayntipaiva"] ?? "";
    $kellonaika = $tiedot["kellonaika"] ?? "";
    $syy = $tiedot["syy"] ?? "";
    $tila = $tiedot["tila"] ?? "";

    $lause = $conn->prepare("INSERT INTO ajanvaraukset (omistaja, elain, laji, ika, puhelin, kayntipaiva, kellonaika, syy, tila) VALUES (:omistaja, :elain, :laji, :ika, :puhelin, :kayntipaiva, :kellonaika, :syy, :tila)");

    $lause->bindValue(":omistaja", $omistaja, PDO::PARAM_STR);
    $lause->bindValue(":elain", $elain, PDO::PARAM_STR);
    $lause->bindValue(":laji", $laji, PDO::PARAM_STR);
    $lause->bindValue(":ika", $ika, PDO::PARAM_INT);
    $lause->bindValue(":puhelin", $puhelin, PDO::PARAM_STR);
    $lause->bindValue(":kayntipaiva", $kayntipaiva, PDO::PARAM_STR);
    $lause->bindValue(":kellonaika", $kellonaika, PDO::PARAM_STR);
    $lause->bindValue(":syy", $syy, PDO::PARAM_STR);
    $lause->bindValue(":tila", $tila, PDO::PARAM_STR);
    $lause->execute();

    echo json_encode([
        "onnistui" => true,
        "viesti" => "Ajanvaraus lisätty onnistuneesti."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

if($metodi === "PUT") {
    $tiedot = json_decode(file_get_contents("php://input"), true);
    $id = filter_var($tiedot["id"] ?? null, FILTER_VALIDATE_INT);
    $tila = trim((string) ($tiedot["tila"] ?? ""));

    if ($id === false || $id < 1 || $tila === "") {
        http_response_code(400);
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Virheelliset tiedot."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $lause = $conn->prepare("UPDATE ajanvaraukset SET tila = :tila WHERE ID = :id");
    $lause->bindValue(":tila", $tila, PDO::PARAM_STR);
    $lause->bindValue(":id", $id, PDO::PARAM_INT);
    $lause->execute();

    echo json_encode([
        "onnistui" => true,
        "viesti" => "Ajanvarauksen tila päivitetty onnistuneesti."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

if($metodi === "DELETE") {
    $tiedot = json_decode(file_get_contents("php://input"), true);
    $id = filter_var($tiedot["id"] ?? null, FILTER_VALIDATE_INT);

    if ($id === false || $id < 1) {
        http_response_code(400);
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Virheellinen ID."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $lause = $conn->prepare("DELETE FROM ajanvaraukset WHERE ID = :id");
    $lause->bindValue(":id", $id, PDO::PARAM_INT);
    $lause->execute();

    echo json_encode([
        "onnistui" => true,
        "viesti" => "Ajanvaraus poistettu onnistuneesti."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}