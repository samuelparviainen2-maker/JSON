<?php
header("Content-Type: application/json; charset=utf-8");

require 'db_config.php';

$conn->exec("CREATE TABLE IF NOT EXISTS autot (
    auto_id INT AUTO_INCREMENT PRIMARY KEY,
    merkki VARCHAR(255) NOT NULL,
    tyyppi VARCHAR(255) NOT NULL,
    vuosimalli INT NOT NULL
)");

$metodi = $_SERVER["REQUEST_METHOD"];

if ($metodi === "GET") {
    $lause = $conn->query("SELECT auto_id, merkki, tyyppi, vuosimalli FROM autot");
    $vastaus = $lause->fetchAll();
    echo json_encode($vastaus, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($metodi === "POST") {
    $raw = file_get_contents("php://input");
    $tiedot = $raw === false || trim($raw) === "" ? [] : json_decode($raw, true);

    if (!is_array($tiedot)) {
        http_response_code(400);
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Virheelliset tiedot."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $merkki = trim((string) ($tiedot["merkki"] ?? ""));
    $tyyppi = trim((string) ($tiedot["tyyppi"] ?? ""));
    $vuodenmalli = trim((string) ($tiedot["vuodenmalli"] ?? ""));

    if ($merkki === "" || $tyyppi === "" || $vuodenmalli === "") {
        http_response_code(400);
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Täytä auton merkki, tyyppi ja vuosimalli."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

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
    $raw = file_get_contents("php://input");
    $tiedot = $raw === false || trim($raw) === "" ? [] : json_decode($raw, true);

    if (!is_array($tiedot)) {
        http_response_code(400);
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Virheelliset tiedot."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $autoId = filter_var($tiedot["auto_id"] ?? null, FILTER_VALIDATE_INT);
    $merkki = trim((string) ($tiedot["merkki"] ?? ""));
    $tyyppi = trim((string) ($tiedot["tyyppi"] ?? ""));
    $vuodenmalli = filter_var($tiedot["vuodenmalli"] ?? null, FILTER_VALIDATE_INT);

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

    $lause = $conn->prepare("UPDATE autot SET merkki = :merkki, tyyppi = :tyyppi, vuosimalli = :vuodenmalli WHERE auto_id = :auto_id");
    $lause->bindValue(":merkki", $merkki, PDO::PARAM_STR);
    $lause->bindValue(":tyyppi", $tyyppi, PDO::PARAM_STR);
    $lause->bindValue(":vuodenmalli", $vuodenmalli, PDO::PARAM_INT);
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
        "viesti" => "Auton tiedot päivitetty."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(405);
echo json_encode([
    "onnistui" => false,
    "viesti" => "Metodi ei ole tuettu."
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
