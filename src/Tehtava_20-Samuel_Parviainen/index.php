<?php
session_start();

$apiOsoite = "http://localhost/Tehtava_20-Samuel_Parviainen/autot_api.php";
$viesti = $_SESSION["auto_viesti"] ?? "";
unset($_SESSION["auto_viesti"]);
$auto_id = trim($_GET["auto_id"] ?? "");

// Haetaan kaikki autot API:sta
$curl = curl_init();

curl_setopt(
    $curl,
    CURLOPT_URL,
    $apiOsoite . ($auto_id !== "" ? "?auto_id=" . urlencode($auto_id) : "")
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

    if ($auto_id !== "") {
        $autoIdInt = (int) $auto_id;
        $tuotteet = array_values(array_filter(
            $tuotteet,
            static fn ($tuote) => ((int) ($tuote["auto_id"] ?? 0)) === $autoIdInt
        ));

        if (count($tuotteet) === 0) {
            $viesti = "Autoa ei löytynyt ID:llä " . htmlspecialchars((string) $auto_id, ENT_QUOTES, 'UTF-8') . ".";
        } else {
            $viesti = "Auto löytyi ID:llä " . htmlspecialchars((string) $auto_id, ENT_QUOTES, 'UTF-8') . ".";
        }
    }
}

curl_close($curl);
 
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <title>Autojen hallinta</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Autojen hallinta</h1>
    
    <?php include 'header.php'; ?>
    <br>
    <br>


    <div class="sisalto">
 
    <h1>Autojen haku</h1>
 
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
 
        <h2>Hae auto</h2>
 
        <form method="get" action="index.php">
 
            <label for="auto_id">
                Auton ID
            </label>
 
            <input
                type="number"
                name="auto_id"
                id="auto_id"
                value="<?= htmlspecialchars($auto_id) ?>"
                min="1"
                required
            >
 
            <button type="submit">
                Hae auto
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