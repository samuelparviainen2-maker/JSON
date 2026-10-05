<?php
session_start();
 
$apiOsoite = "http://localhost/Tehtava_20-Samuel_Parviainen/autot_api.php";
$viesti = $_SESSION["muokkaus_viesti"] ?? "";
unset($_SESSION["muokkaus_viesti"]);
 
 
// Kun lomake lähetetään
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    $auto_id = trim($_POST["auto_id"] ?? "");
    $merkki = $_POST["merkki"] ?? "";
    $tyyppi = $_POST["tyyppi"] ?? "";
    $vuodenmalli = $_POST["vuodenmalli"] ?? "";
 
    if ($merkki === "" || $tyyppi === "" || $vuodenmalli === "") {
 
        $viesti = "Täytä autojen tiedot.";
 
    } else {
 
        // Aloitetaan cURL-pyyntö
        $curl = curl_init();
 
        // Asetetaan API:n osoite
        curl_setopt(
            $curl,
            CURLOPT_URL,
            $apiOsoite
        );
 
        // Pyyntö on DELETE-pyyntö
        curl_setopt(
            $curl,
            CURLOPT_CUSTOMREQUEST,
            "DELETE"
        );
 
        // Lähetettävät tiedot
        curl_setopt(
            $curl,
            CURLOPT_POSTFIELDS,
            json_encode([
                "auto_id" => $auto_id,
                "merkki" => $merkki,
                "tyyppi" => $tyyppi,
                "vuosimalli" => $vuodenmalli
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
 
    <title>Autojen hallinta</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
 <?php include 'header.php'; ?>
<div class="sisalto">
 
    <h1>Autojen hallinta</h1>
 
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
 
        <h2>Muokkaa auton tietoja</h2>
 
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
 
                <?php foreach ($tuotteet as $tuote): //pidetään talua form-silmukoissa, jotta voidaan päivittää suoraan autoista?>
                    <?php $paivityslomake = "paivita-auto-" . (int) ($tuote["auto_id"] ?? 0); ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars((string) ($tuote["auto_id"] ?? "")) ?>
                        </td>
 
                        <td>
                            <input
                                class="rivin-kentta"
                                type="text"
                                name="merkki"
                                value="<?= htmlspecialchars((string) ($tuote["merkki"] ?? ""), ENT_QUOTES, "UTF-8") ?>"
                                form="<?= $paivityslomake ?>"
                                required
                                readonly
                            >
                        </td>
 
                        <td>
                            <input
                                class="rivin-kentta"
                                type="text"
                                name="tyyppi"
                                value="<?= htmlspecialchars((string) ($tuote["tyyppi"] ?? ""), ENT_QUOTES, "UTF-8") ?>"
                                form="<?= $paivityslomake ?>"
                                required
                                readonly
                            >
                        </td>
                        <td>
                            <input
                                class="rivin-kentta"
                                type="number"
                                name="vuodenmalli"
                                min="1901"
                                max="2155"
                                value="<?= htmlspecialchars((string) ($tuote["vuosimalli"] ?? ""), ENT_QUOTES, "UTF-8") ?>"
                                form="<?= $paivityslomake ?>"
                                required
                                readonly
                            >
                        </td>
                        <td class="rivin-toiminto">
                            <form id="<?= $paivityslomake ?>" method="post" action="poista.php">
                                <input type="hidden" name="toiminto" value="poista">
                                <input type="hidden" name="auto_id" value="<?= (int) ($tuote["auto_id"] ?? 0) ?>">
                                <button type="submit">Poista</button>
                            </form>
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