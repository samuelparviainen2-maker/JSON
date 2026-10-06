<?php
header("Content-Type: application/json; charset=utf-8");

require 'tietokanta.php';

$metodi = $_SERVER["REQUEST_METHOD"];

if ($metodi === "GET") {
    $id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);
    $elain = trim((string) ($_GET["elain"] ?? ""));

    if ($id !== false && $id > 0) {
        $lause = $conn->prepare("SELECT * FROM ajanvaraukset WHERE ID = :id");
        $lause->bindValue(":id", $id, PDO::PARAM_INT);
    } elseif ($elain !== "") {
        $lause = $conn->prepare("SELECT * FROM ajanvaraukset WHERE elain LIKE :elain");
        $lause->bindValue(":elain", "%$elain%", PDO::PARAM_STR);
    } else {
        $lause = $conn->prepare("SELECT * FROM ajanvaraukset");
    }
    $lause->execute();
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


    
    if ($tila !== "Varattu" && $tila !== "Peruttu" && $tila !== "Saapunut" && strtotime($kayntipaiva) < time()) {
        http_response_code(400);
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Virheelliset tiedot."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

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
    $omistaja = trim((string) ($tiedot["omistaja"] ?? ""));
    $elain = trim((string) ($tiedot["elain"] ?? ""));
    $laji = trim((string) ($tiedot["laji"] ?? ""));
    $ika = trim((string) ($tiedot["ika"] ?? ""));
    $puhelin = trim((string) ($tiedot["puhelin"] ?? ""));
    $kayntipaiva = trim((string) ($tiedot["kayntipaiva"] ?? ""));
    $kellonaika = trim((string) ($tiedot["kellonaika"] ?? ""));
    $syy = trim((string) ($tiedot["syy"] ?? ""));

    if ($id === false || $id < 1 || $tila === "" && $tila !== "Varattu" && $tila !== "Peruttu" && $tila !== "Saapunut") {
        http_response_code(400);
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Virheelliset tiedot."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $lause = $conn->prepare("UPDATE ajanvaraukset SET tila = :tila, omistaja = :omistaja, elain = :elain, laji = :laji, ika = :ika, puhelin = :puhelin, kayntipaiva = :kayntipaiva, kellonaika = :kellonaika, syy = :syy WHERE ID = :id");
    $lause->bindValue(":tila", $tila, PDO::PARAM_STR);
    $lause->bindValue(":omistaja", $omistaja, PDO::PARAM_STR);
    $lause->bindValue(":elain", $elain, PDO::PARAM_STR);
    $lause->bindValue(":laji", $laji, PDO::PARAM_STR);
    $lause->bindValue(":ika", $ika, PDO::PARAM_STR);
    $lause->bindValue(":puhelin", $puhelin, PDO::PARAM_STR);
    $lause->bindValue(":kayntipaiva", $kayntipaiva, PDO::PARAM_STR);
    $lause->bindValue(":kellonaika", $kellonaika, PDO::PARAM_STR);
    $lause->bindValue(":syy", $syy, PDO::PARAM_STR);
    $lause->bindValue(":id", $id, PDO::PARAM_INT);
    $lause->execute();

    if ($lause->rowCount() === 0) {
        $tarkistus = $conn->prepare("SELECT 1 FROM autot WHERE ID = :auto_id");
        $tarkistus->bindValue(":auto_id", $autoId, PDO::PARAM_INT);
        $tarkistus->execute();

        if (!$tarkistus->fetchColumn()) {
            http_response_code(404);
            echo json_encode([
                "onnistui" => false,
                "viesti" => "Autoa ei löytynyt."
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    echo json_encode([
        "onnistui" => true,
        "viesti" => "Ajanvarauksen tiedot päivitetty onnistuneesti."
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