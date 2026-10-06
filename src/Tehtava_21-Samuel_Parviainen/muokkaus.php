<?php
session_start();

$apiOsoite = "http://localhost/Tehtava_21-Samuel_Parviainen/api.php";
    $viesti = $_SESSION["auto_viesti"] ?? "";
    unset($_SESSION["auto_viesti"]);

$id = trim((string) ($_GET["id"] ?? ""));
$omistaja = "";
$elain = "";
$laji = "";
$ika = "";
$puhelin = "";
$kayntipaiva = "";
$kellonaika = "";
$syy = "";
$tila = "";
$tuotteet = [];
$hakuvirhe = "";

// Kun lomake lähetetään
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    $id = trim($_POST["id"] ?? "");
    $omistaja = $_POST["omistaja"] ?? "";
    $elain = $_POST["elain"] ?? "";
    $laji = $_POST["laji"] ?? "";
    $ika = $_POST["ika"] ?? "";
    $puhelin = $_POST["puhelin"] ?? "";
    $kayntipaiva = $_POST["kayntipaiva"] ?? "";
    $kellonaika = $_POST["kellonaika"] ?? "";
    $syy = $_POST["syy"] ?? "";
    $tila = $_POST["tila"] ?? "";

    if ($omistaja === "" || $elain === "" || $laji === "") {
        $viesti = "Täytä kaikki pakolliset tiedot.";
 
    } else {
 
        // Aloitetaan cURL-pyyntö
        $curl = curl_init();
 
        // Asetetaan API:n osoite
        curl_setopt(
            $curl,
            CURLOPT_URL,
            $apiOsoite
        );
 
        // Pyyntö on PUT-pyyntö
        curl_setopt(
            $curl,
            CURLOPT_CUSTOMREQUEST,
            "PUT"
        );
 
        // Lähetettävät tiedot
        curl_setopt(
            $curl,
            CURLOPT_POSTFIELDS,
            json_encode([
                "id" => $id,
                "omistaja" => $omistaja,
                "elain" => $elain,
                "laji" => $laji,
                "ika" => $ika,
                "puhelin" => $puhelin,
                "kayntipaiva" => $kayntipaiva,
                "kellonaika" => $kellonaika,
                "syy" => $syy,
                "tila" => $tila
            ])
        );

        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            ["Content-Type: application/json"]
        );
 
        // API:n vastaus tallennetaan muuttujaan
        curl_setopt(
            $curl,
            CURLOPT_RETURNTRANSFER,
            true
        );
 
        // Suoritetaan pyyntö
        $vastaus = curl_exec($curl);
 
        // Tarkistetaan, tapahtuiko cURL-virhe
        if ($vastaus === false) {
 
            $viesti = "cURL-virhe: " . curl_error($curl);
 
        } else {
 
            $vastausTaulukkona = json_decode(
                $vastaus,
                true
            );
 
            $viesti = $vastausTaulukkona["viesti"]
                ?? "API ei palauttanut viestiä.";

            if (isset($vastausTaulukkona["onnistui"]) && $vastausTaulukkona["onnistui"] === true) {
                $_SESSION["muokkaus_viesti"] = $viesti;
                header("Location: " . $_SERVER["PHP_SELF"]);
                exit;
            }
        }
 
        // Suljetaan cURL-yhteys
        curl_close($curl);
    }
}

// Haetaan ajanvaraukset API:sta
$parametrit = array_filter(
    ["id" => $id, "elain" => $elain],
    static fn ($arvo) => $arvo !== ""
);
$kysely = http_build_query($parametrit);
$apiurl = $apiOsoite . ($kysely !== "" ? "?" . $kysely : "");

$curl = curl_init();

curl_setopt(
    $curl,
    CURLOPT_URL,
    $apiurl
);
curl_setopt(
    $curl,
    CURLOPT_RETURNTRANSFER,
    true
);

$vastaus = curl_exec($curl);

if ($vastaus === false) {
    $tuotteet = [];
    $hakuvirhe = curl_error($curl);
} else {
    $tuotteet = json_decode(
        $vastaus,
        true
    );

    $hakuvirhe = "";

    if (!is_array($tuotteet)) {
        $tuotteet = [];
    }

    if ($id !== "") {
        $idInt = (int) $id;
        $tuotteet = array_values(array_filter(
            $tuotteet,
            static fn ($tuote) => ((int) ($tuote["id"] ?? 0)) === $idInt
        ));

        if (count($tuotteet) === 0) {
            $viesti = "Ajanvarausta ei löytynyt ID:llä " . htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') . ".";
        } else {
            $viesti = "Ajanvarausta löytyi ID:llä " . htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') . ".";
        }
    }
}

curl_close($curl);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajanvarauksen muokkaus</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <?php if ($viesti !== ""): ?>
    <div class="viesti">
        <p><?php echo htmlspecialchars($viesti, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <?php endif; ?>

    <?php if (!empty($hakuvirhe)): ?>
    <div class="hakuvirhe">
        <p><?php echo htmlspecialchars($hakuvirhe, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <?php endif; ?>
    <div class="sailio">
    <h1>Ajanvarauksen muokkaus</h1>
    <br>
    <br>
    <?php if (!empty($tuotteet)): ?>
        
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Omistaja</th>
                        <th>Eläin</th>  
                        <th>Laji</th>
                        <th>Ikä</th>
                        <th>Puhelin</th>
                        <th>Käyntipäivä</th>
                        <th>Kellonaika</th>
                        <th>Syy</th>
                        <th>Tila</th>
                        <th>Tallennus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tuotteet as $tuote): ?>
                    <form method="POST" action="muokkaus.php">
                        <tr>
                            <td>
                                <input type="number" name="id" value="<?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                            </td>
                            <td>
                                <input type="text" name="omistaja" value="<?php echo htmlspecialchars($tuote['omistaja'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </td>
                            <td>
                                <input type="text" name="elain" value="<?php echo htmlspecialchars($tuote['elain'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </td>
                            <td>
                                <input type="text" name="laji" value="<?php echo htmlspecialchars($tuote['laji'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </td>
                            <td>
                                <input type="number" min="0" max="30" name="ika" value="<?php echo htmlspecialchars($tuote['ika'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </td>
                            <td>
                                <input type="text" name="puhelin" value="<?php echo htmlspecialchars($tuote['puhelin'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </td>
                            <td>
                                <input type="date" name="kayntipaiva" value="<?php echo htmlspecialchars($tuote['kayntipaiva'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </td>
                            <td>
                                <input type="time" name="kellonaika" value="<?php echo htmlspecialchars($tuote['kellonaika'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </td>
                            <td>
                                <input type="text" name="syy" value="<?php echo htmlspecialchars($tuote['syy'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </td>
                            <td>
                                <input type="text" name="tila" value="<?php echo htmlspecialchars($tuote['tila'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </td>
                            <td>
                                <button type="submit">Tallenna</button>
                            </td>
                        </tr>
                    </form>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Ei ajanvarauksia saatavilla.</p>
        <?php endif; ?>