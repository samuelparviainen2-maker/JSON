<?php
 
$apiOsoite = "http://localhost/Kirjalistan_API_ja_kayttoliittyma-Samuel_Parviainen/api.php";
$viesti = "";
 
 
// Kun lomake lähetetään
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    $nimi = trim($_POST["nimi"] ?? "");
    $kirjoittaja = trim($_POST["kirjoittaja"] ?? "");
    $julkaisu = trim($_POST["julkaisupvm"] ?? "");

 
    if ($nimi === "" || $kirjoittaja === ""|| $julkaisu === "") {
 
        $viesti = "Täytä kirjan nimi, kirjoittaja ja julkaisuvuosi.";
 
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
            [
                "Nimi" => $nimi,
                "Kirjoittaja" => $kirjoittaja,
                "JulkaisuVuosi" => $julkaisu
            ]
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
 
    <title>Kirjojen hallinta</title>
 
    <style>
        body {
            margin: 0;
            padding: 30px;
            
            font-family: Arial, sans-serif;
            background-image: url(https://i.programmerhumor.io/2025/04/f54bc5c0353cc68bb6e80d0bb6882f095e51baac805800c7fcc24e74a6fde69d.jpeg);
        }
 
        .sisalto {
            max-width: 800px;
            margin: 0 auto;
        }
 
        h1 {
            color: #243447;
            background-color: white;
        }
 
        .laatikko {
            margin-bottom: 25px;
            padding: 25px;
            background-color: white;
            border-radius: 8px;
        }
 
        label {
            display: block;
            margin-top: 12px;
            margin-bottom: 5px;
            font-weight: bold;
        }
 
        input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #b8c0cc;
            border-radius: 4px;
        }
 
        button {
            margin-top: 18px;
            padding: 10px 20px;
            color: white;
            background-color: #1769aa;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
 
        button:hover {
            background-color: #12558a;
        }
 
        .viesti {
            padding: 12px;
            margin-bottom: 20px;
            background-color: #e7f3ff;
            border-left: 5px solid #1769aa;
        }
 
        .virhe {
            padding: 12px;
            margin-bottom: 20px;
            background-color: #ffe7e7;
            border-left: 5px solid #b00020;
        }
 
        table {
            width: 100%;
            border-collapse: collapse;
        }
 
        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #dddddd;
            text-align: left;
        }
 
        th {
            color: white;
            background-color: #243447;
        }
 
        tr:hover {
            background-color: #f5f7fa;
        }
 
        .ei-tuotteita {
            color: #666666;
        }
    </style>
</head>
 
<body>
 
<div class="sisalto">
 
    <h1>Tuotteiden hallinta</h1>
 
    <?php if ($viesti !== ""): ?>
 
        <div class="viesti">
            <?= htmlspecialchars($viesti) ?>
        </div>
 
    <?php endif; ?>
 
    <?php if ($hakuvirhe !== ""): ?>
 
        <div class="virhe">
            Tuotteiden hakeminen epäonnistui:
            <?= htmlspecialchars($hakuvirhe) ?>
        </div>
 
    <?php endif; ?>
 
    <div class="laatikko">
 
        <h2>Lisää uusi kirja</h2>
 
        <form method="post" action="client.php">
 
        <label for="nimi">Nimi</label><br>
        <input type="text" name="nimi" required>
        <br><br>
        <label for="kirjoittaja">Kirjoittaja</label><br>
        <input type="text" name="kirjoittaja" required>
        <br><br>
        <label for="julkaisupvm">Julkaisuvuosi</label><br>
        <input type="number" name="julkaisupvm" min="1900" max="2099" step="1" required>

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
                        <th>Kirjoittaja</th>
                        <th>Julkaisuvuosi</th>
                    </tr>
                </thead>
 
                <tbody>
 
                <?php foreach ($tuotteet as $tuote): ?>

                    <tr>
                        <td>
                            <?= htmlspecialchars($tuote["id"]) ?>
                        </td>
 
                        <td>
                            <?= htmlspecialchars($tuote["nimi"]) ?>
                        </td>
 
                        <td>
                            <?= htmlspecialchars($tuote["kirjoittaja"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($tuote["julkaisuvuosi"]) ?>
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