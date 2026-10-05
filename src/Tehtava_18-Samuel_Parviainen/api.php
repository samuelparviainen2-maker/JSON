<?php
header("Content-Type: application/json; charset=utf-8");

require 'yhteys.php';


$metodi = $_SERVER["REQUEST_METHOD"];

if ($metodi === "GET") {
    $lause = $conn->query("SELECT tapahtuma_id, nimi, paikka, paivamaara, osallistujamaara FROM Tapahtuma");
    $vastaus = $lause->fetchAll();
    echo json_encode($vastaus, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($metodi === "POST") {
    $tiedot = json_decode(file_get_contents("php://input"), true);
    $nimi = $tiedot["nimi"] ?? "";
    $paikka = $tiedot["paikka"] ?? "";
    $pvm = $tiedot["pvm"] ?? "";
    $osallistujamaara = $tiedot["osallistujamaara"] ?? "";

    $lause = $conn->prepare("INSERT INTO Tapahtuma (nimi, paikka, paivamaara, osallistujamaara) VALUES (:nimi, :paikka, :pvm, :osallistujamaara)");

    $lause->bindValue(":nimi", $nimi, PDO::PARAM_STR);
    $lause->bindValue(":paikka", $paikka, PDO::PARAM_STR);
    $lause->bindValue(":pvm", $pvm, PDO::PARAM_STR);
    $lause->bindValue(":osallistujamaara", $osallistujamaara, PDO::PARAM_INT);
    $lause->execute();

    echo json_encode([
        "onnistui" => true,
        "viesti" => "Tapahtuma lisätty onnistuneesti."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

