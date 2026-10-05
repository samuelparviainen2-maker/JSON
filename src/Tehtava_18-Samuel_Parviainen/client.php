<?php
 
$apiOsoite = "http://localhost/Tehtava_18-Samuel_Parviainen/api.php";
$viesti = "";
 
 
// Kun lomake lähetetään
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    $nimi = trim($_POST["nimi"] ?? "");
    $paikka = $_POST["paikka"] ?? "";
    $pvm = $_POST["pvm"] ?? "";
    $osallistujamaara = $_POST["osallistujamaara"] ?? "";
 
    if ($nimi === "" || $paikka === "" || $pvm === "" || $osallistujamaara === "") {
 
        $viesti = "Täytä tapahtuman nimi, paikka, päivämäärä ja osallistujamäärä.";
 
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
                "nimi" => $nimi,
                "paikka" => $paikka,
                "pvm" => $pvm,
                "osallistujamaara" => $osallistujamaara
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
 
    <title>Tapahtumien hallinta</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
 
<div class="sisalto">
 
    <h1>Tapahtumien hallinta</h1>
 
    <?php if ($viesti !== ""): ?>
 
        <div class="viesti">
            <?= htmlspecialchars($viesti) ?>
        </div>
 
    <?php endif; ?>
 
    <?php if ($hakuvirhe !== ""): ?>
 
        <div class="virhe">
            Tapahtumien hakeminen epäonnistui:
            <?= htmlspecialchars($hakuvirhe) ?>
        </div>
 
    <?php endif; ?>
 
    <div class="laatikko">
 
        <h2>Lisää uusi tapahtuma</h2>
 
        <form method="post" action="client.php">
 
            <label for="nimi">
                Tapahtuman nimi
            </label>
 
            <input
                type="text"
                id="nimi"
                name="nimi"
                required
            >
 
            <label for="paikka">
                Tapahtuman paikka
            </label>
 
            <input
                type="text"
                id="paikka"
                name="paikka"
                required
            >

            <label for="pvm">
                Tapahtuman päivämäärä
            </label>
 
            <input
                type="date"
                id="pvm"
                name="pvm"
                required
            >

            <label for="osallistujamaara">
                Tapahtuman osallistujamäärä
            </label>
 
            <input
                type="number"
                id="osallistujamaara"
                name="osallistujamaara"
                min="0"
                step="1"
                required
            >

            <button type="submit">
                Lisää tapahtuma
            </button>
 
        </form>
 
    </div>
 
    <div class="laatikko">
 
        <h2>Tallennetut tapahtumat</h2>
 
        <?php if (count($tuotteet) === 0): ?>
 
            <p class="ei-tuotteita">
                Tapahtumia ei ole vielä tallennettu.
            </p>
 
        <?php else: ?>
 
            <table>
 
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nimi</th>
                        <th>Paikka</th>
                        <th>Päivämäärä</th>
                        <th>Osallistujamäärä</th>
                    </tr>
                </thead>
 
                <tbody>
 
                <?php foreach ($tuotteet as $tuote): ?>
 
                    <tr>
                        <td>
                            <?= htmlspecialchars((string) ($tuote["tapahtuma_id"] ?? "")) ?>
                        </td>
 
                        <td>
                            <?= htmlspecialchars($tuote["nimi"]) ?>
                        </td>
 
                        <td>
                            <?= htmlspecialchars($tuote["paikka"]) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars((string) ($tuote["paivamaara"] ?? "")) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($tuote["osallistujamaara"]) ?>
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