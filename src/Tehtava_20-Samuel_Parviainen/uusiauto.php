<?php
session_start();

$apiOsoite = "http://localhost/Tehtava_20-Samuel_Parviainen/autot_api.php";
$viesti = $_SESSION["auto_viesti"] ?? "";
unset($_SESSION["auto_viesti"]);
 

// Kun lomake lähetetään
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    $merkki = trim($_POST["merkki"] ?? "");
    $tyyppi = $_POST["tyyppi"] ?? "";
    $vuodenmalli = $_POST["vuodenmalli"] ?? "";
 
    if ($merkki === "" || $tyyppi === "" || $vuodenmalli === "") {
 
        $viesti = "Täytä auton merkki, tyyppi ja vuosimalli.";
 
    } else {
 
        // Aloitetaan cURL-pyyntö
        $curl = curl_init();
 
        // Asetetaan API:n osoite
        curl_setopt(
            $curl,
            CURLOPT_URL,
            $apiOsoite
        );
 
        // Pyyntö on POST-pyyntö
        curl_setopt(
            $curl,
            CURLOPT_POST,
            true
        );
 
        // Lähetettävät tiedot
        curl_setopt(
            $curl,
            CURLOPT_POSTFIELDS,
            json_encode([
                "merkki" => $merkki,
                "tyyppi" => $tyyppi,
                "vuodenmalli" => $vuodenmalli
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
                $_SESSION["auto_viesti"] = $viesti;
                header("Location: " . $_SERVER["PHP_SELF"]);
                exit;
            }
        }
 
        // Suljetaan cURL-yhteys
        curl_close($curl);
    }
}
 
 
// Haetaan kaikki tuotteet API:sta
$curl = curl_init();
 
curl_setopt(
    $curl,
    CURLOPT_URL,
    $apiOsoite
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
    
}
 
curl_close($curl);
 
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
 
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
 
    <title>Autojen lisäys</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
 <?php include 'header.php'; ?>
<div class="sisalto">
 
    <h1>Autojen lisäys</h1>
 
    <?php if ($viesti !== ""): ?>
 
        <div class="viesti">
            <?= htmlspecialchars($viesti) ?>
        </div>
 
    <?php endif; ?>
 
    <?php if ($hakuvirhe !== ""): ?>
 
        <div class="virhe">
            Autojen hakeminen epäonnistui:
            <?= htmlspecialchars($hakuvirhe) ?>
        </div>
 
    <?php endif; ?>
 
    <div class="laatikko">
 
        <h2>Lisää uusi auto</h2>
 
        <form method="post" action="uusiauto.php">
 
            <label for="merkki">
                Auton merkki
            </label>
 
            <input
                type="text"
                id="merkki"
                name="merkki"
                required
            >
 
            <label for="tyyppi">
                Auton tyyppi
            </label>
 
            <input
                type="text"
                id="tyyppi"
                name="tyyppi"
                required
            >

            <label for="vuodenmalli">
                Auton vuosimalli
            </label>
 
            <input
                type="number"
                id="vuodenmalli"
                name="vuosimalli"
                min="1900"
                max="2027"
                required
            >

            <button type="submit">
                Lisää auto
            </button>
 
        </form>
 
    </div>
 
    <div class="laatikko">
 
        <h2>Tallennetut autot</h2>
 
        <?php if (count($tuotteet) === 0): ?>
 
            <p class="ei-tuotteita">
                Autoja ei ole vielä tallennettu.
            </p>
 
        <?php else: ?>
 
            <table>
 
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Merkki</th>
                        <th>Tyyppi</th>
                        <th>Vuosimalli</th>
                    </tr>
                </thead>
 
                <tbody>
 
                <?php foreach ($tuotteet as $tuote): ?>
 
                    <tr>
                        <td>
                            <?= htmlspecialchars((string) ($tuote["auto_id"] ?? "")) ?>
                        </td>
 
                        <td>
                            <?= htmlspecialchars($tuote["merkki"]) ?>
                        </td>
 
                        <td>
                            <?= htmlspecialchars($tuote["tyyppi"]) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars((string) ($tuote["vuodenmalli"] ?? "")) ?>
                        </td>
                    </tr>
 
                <?php endforeach; ?>
 
                </tbody>
 
            </table>
 
        <?php endif; ?>
 
    </div>
 
</div>
 
</body>
</html>