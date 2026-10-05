<?php
header("Content-Type: application/json; charset=utf-8");

require 'db_config.php';


$metodi = $_SERVER["REQUEST_METHOD"];

if ($metodi === "GET") {
    $autoId = filter_var($_GET["auto_id"] ?? null, FILTER_VALIDATE_INT);

    if ($autoId !== false && $autoId > 0) {
        $lause = $conn->prepare("SELECT ID AS auto_id, merkki, tyyppi, vuosimalli FROM autot WHERE ID = :auto_id");
        $lause->bindValue(":auto_id", $autoId, PDO::PARAM_INT);
        $lause->execute();
        $vastaus = $lause->fetchAll();
        echo json_encode($vastaus, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $lause = $conn->query("SELECT ID AS auto_id, merkki, tyyppi, vuosimalli FROM autot");
    $vastaus = $lause->fetchAll();
    echo json_encode($vastaus, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($metodi === "POST") {
    $tiedot = json_decode(file_get_contents("php://input"), true);
    $merkki = $tiedot["merkki"] ?? "";
    $tyyppi = $tiedot["tyyppi"] ?? "";
    $vuodenmalli = $tiedot["vuodenmalli"] ?? "";

    $lause = $conn->prepare("INSERT INTO autot (merkki, tyyppi, vuosimalli) VALUES (:merkki, :tyyppi, :vuodenmalli)");

    $lause->bindValue(":merkki", $merkki, PDO::PARAM_STR);
    $lause->bindValue(":tyyppi", $tyyppi, PDO::PARAM_STR);
    $lause->bindValue(":vuodenmalli", $vuodenmalli, PDO::PARAM_STR);
    $lause->execute();

    echo json_encode([
        "onnistui" => true,
        "viesti" => "Auto lisätty onnistuneesti."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($metodi === "PUT") {
    $tiedot = json_decode(file_get_contents("php://input"), true);
    $autoId = filter_var($tiedot["auto_id"] ?? null, FILTER_VALIDATE_INT);
    $merkki = trim((string) ($tiedot["merkki"] ?? ""));
    $tyyppi = trim((string) ($tiedot["tyyppi"] ?? ""));
    $vuodenmalli = filter_var($tiedot["vuosimalli"] ?? null, FILTER_VALIDATE_INT);

    if (
        $autoId === false || $autoId < 1 ||
        $merkki === "" || $tyyppi === "" ||
        $vuodenmalli === false || $vuodenmalli < 1900 || $vuodenmalli > 2100
    ) {
        http_response_code(400);
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Virheelliset auton tiedot."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $lause = $conn->prepare("UPDATE autot SET merkki = :merkki, tyyppi = :tyyppi, vuosimalli = :vuodenmalli WHERE ID = :auto_id");
    $lause->bindValue(":merkki", $merkki, PDO::PARAM_STR);
    $lause->bindValue(":tyyppi", $tyyppi, PDO::PARAM_STR);
    $lause->bindValue(":vuodenmalli", $vuodenmalli, PDO::PARAM_INT);
    $lause->bindValue(":auto_id", $autoId, PDO::PARAM_INT);
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
        "viesti" => "Auton tiedot päivitetty."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

if($metodi === "DELETE") {
    $tiedot = json_decode(file_get_contents("php://input"), true);
    $autoId = filter_var($tiedot["auto_id"] ?? null, FILTER_VALIDATE_INT);

    if ($autoId === false || $autoId < 1) {
        http_response_code(400);
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Virheellinen auton tunniste."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $lause = $conn->prepare("DELETE FROM autot WHERE ID = :auto_id");
    $lause->bindValue(":auto_id", $autoId, PDO::PARAM_INT);
    $lause->execute();

    if ($lause->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Autoa ei löytynyt."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode([
        "onnistui" => true,
        "viesti" => "Auto poistettu onnistuneesti."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

