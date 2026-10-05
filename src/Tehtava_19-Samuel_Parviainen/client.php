<?php
session_start();

$apiOsoite = "http://localhost/Tehtava_19-Samuel_Parviainen/api.php";
$viesti = $_SESSION["viesti"] ?? "";
unset($_SESSION["viesti"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $paivittaa = ($_POST["toiminto"] ?? "") === "paivita";
    $kirjaId = filter_var($_POST["kirja_id"] ?? null, FILTER_VALIDATE_INT);
    $nimi = trim($_POST["nimi"] ?? "");
    $kirjailija = trim($_POST["kirjailija"] ?? "");
    $julkaisuvuosi = $_POST["julkaisuvuosi"] ?? "";
    $hyllynumero = $_POST["hyllynumero"] ?? "";

    if (($paivittaa && ($kirjaId === false || $kirjaId < 1)) || $nimi === "" || $kirjailija === "" || $julkaisuvuosi === "" || $hyllynumero === "") {
        $viesti = $paivittaa //tarkistetaan, onko kyseessä päivitys vai lisäys, jotta voidaan antaa tarkempi virheilmoitus
            ? "Tarkista kirjan tunniste ja täytä kaikki kirjan tiedot."
            : "Täytä kirjan nimi, kirjailija, julkaisuvuosi ja hyllynumero.";
    } else {
        $tiedot = [
            "nimi" => $nimi,
            "kirjailija" => $kirjailija,
            "julkaisuvuosi" => $julkaisuvuosi,
            "hyllypaikka" => $hyllynumero
        ];

        if ($paivittaa) { //Jos kyseessä on päivitys, lisätään kirja_id-tieto taulukkoon
            $tiedot["kirja_id"] = $kirjaId;
        }

        $curl = curl_init();
        curl_setopt_array($curl, [//Asetetaan cURL-asetukset
            CURLOPT_URL => $apiOsoite, 
            CURLOPT_CUSTOMREQUEST => $paivittaa ? "PUT" : "POST", //Riipumatta siitä, onko kyseessä päivitys vai lisäys, käytetään samaa API-osoitetta
            CURLOPT_POSTFIELDS => json_encode($tiedot),
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
            CURLOPT_RETURNTRANSFER => true
        ]);

        $vastaus = curl_exec($curl);
        if ($vastaus === false) {
            $viesti = "cURL-virhe: " . curl_error($curl);
        } else {
            $vastausTaulukkona = json_decode($vastaus, true);

            if (is_array($vastausTaulukkona) && !empty($vastausTaulukkona["onnistui"])) {
                $_SESSION["viesti"] = $vastausTaulukkona["viesti"]
                    ?? ($paivittaa ? "Kirjan tiedot päivitetty." : "Kirja lisätty onnistuneesti.");
                header("Location: client.php", true, 303);
                exit;
            }

            $viesti = is_array($vastausTaulukkona)
                ? ($vastausTaulukkona["viesti"] ?? "API ei palauttanut viestiä.")
                : "API palautti virheellisen vastauksen.";
        }
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
 
    <title>Kirjojen hallinta</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
 
<div class="sisalto">
 
    <h1>Kirjat</h1>
 
    <?php if ($viesti !== ""): ?>
 
        <div class="viesti">
            <?= htmlspecialchars($viesti) ?>
        </div>
 
    <?php endif; ?>
 
    <?php if ($hakuvirhe !== ""): ?>
 
        <div class="virhe">
            Kirjojen hakeminen epäonnistui:
            <?= htmlspecialchars($hakuvirhe) ?>
        </div>
 
    <?php endif; ?>
 
    <div class="laatikko">
 
        <h2>Lisää uusi kirja</h2>
 
        <form method="post" action="client.php">
 
            <label for="nimi">
                Kirjan nimi
            </label>
 
            <input
                type="text"
                id="nimi"
                name="nimi"
                required
            >
 
            <label for="kirjailija">
                Kirjailija
            </label>
 
            <input
                type="text"
                id="kirjailija"
                name="kirjailija"
                required
            >

            <label for="julkaisuvuosi">
                Julkaisuvuosi
            </label>
 
            <input
                type="number"
                id="julkaisuvuosi"
                name="julkaisuvuosi"
                min="1901"
                max="2155"
                step="1"
                required
            >

            <label for="hyllynumero">
                Hyllynumero
            </label>
 
            <input
                type="number"
                id="hyllynumero"
                name="hyllynumero"
                min="0"
                step="1"
                required
            >

            <button type="submit">
                Lisää kirja
            </button>
 
        </form>
 
    </div>
 
    <div class="laatikko">
 
        <h2>Tallennetut kirjat</h2>
 
        <?php if (count($tuotteet) === 0): ?>
 
            <p class="ei-tuotteita">
                Kirjoja ei ole vielä tallennettu.
            </p>
 
        <?php else: ?>
 
            <table>
 
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nimi</th>
                        <th>Kirjailija</th>
                        <th>Julkaisuvuosi</th>
                        <th>Hyllynumero</th>
                        <th>Päivitä</th>
                    </tr>
                </thead>
 
                <tbody>
 
                <?php foreach ($tuotteet as $tuote): //pidetään talua form-silmukoissa, jotta voidaan päivittää suoraan kirjoista?>
                    <?php $paivityslomake = "paivita-kirja-" . (int) ($tuote["kirja_id"] ?? 0); ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars((string) ($tuote["kirja_id"] ?? "")) ?>
                        </td>
 
                        <td>
                            <input
                                class="rivin-kentta"
                                type="text"
                                name="nimi"
                                value="<?= htmlspecialchars((string) ($tuote["nimi"] ?? ""), ENT_QUOTES, "UTF-8") ?>"
                                form="<?= $paivityslomake ?>"
                                required
                            >
                        </td>
 
                        <td>
                            <input
                                class="rivin-kentta"
                                type="text"
                                name="kirjailija"
                                value="<?= htmlspecialchars((string) ($tuote["kirjailija"] ?? ""), ENT_QUOTES, "UTF-8") ?>"
                                form="<?= $paivityslomake ?>"
                                required
                            >
                        </td>
                        <td>
                            <input
                                class="rivin-kentta"
                                type="number"
                                name="julkaisuvuosi"
                                min="1901"
                                max="2155"
                                value="<?= htmlspecialchars((string) ($tuote["julkaisuvuosi"] ?? ""), ENT_QUOTES, "UTF-8") ?>"
                                form="<?= $paivityslomake ?>"
                                required
                            >
                        </td>
                        <td>
                            <input
                                class="rivin-kentta"
                                type="number"
                                name="hyllynumero"
                                min="0"
                                value="<?= htmlspecialchars((string) ($tuote["hyllypaikka"] ?? ""), ENT_QUOTES, "UTF-8") ?>"
                                form="<?= $paivityslomake ?>"
                                required
                            >
                        </td>
                        <td class="rivin-toiminto">
                            <form id="<?= $paivityslomake ?>" method="post" action="client.php">
                                <input type="hidden" name="toiminto" value="paivita">
                                <input type="hidden" name="kirja_id" value="<?= (int) ($tuote["kirja_id"] ?? 0) ?>">
                                <button type="submit">Tallenna</button>
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