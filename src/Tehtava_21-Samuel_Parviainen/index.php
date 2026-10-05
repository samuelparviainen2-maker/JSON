<?php
session_start();

$apiaddress = "http://localhost/Tehtava_21-Samuel_Parviainen/api.php";
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

if($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = trim((string) ($_POST["id"] ?? ""));
    $omistaja = $_POST["omistaja"] ?? "";
    $elain = $_POST["elain"] ?? "";
    $laji = $_POST["laji"] ?? "";
    $ika = $_POST["ika"] ?? "";
    $puhelin = $_POST["puhelin"] ?? "";
    $kaynti = explode("T", $_POST["kayntipaiva"] ?? "");
    $kayntipaiva = $kaynti[0] ?? "";
    $kellonaika = $kaynti[1] ?? "";
    $syy = $_POST["syy"] ?? "";
    $tila = $_POST["tila"] ?? "";

    if($tila === "Varattu" || $tila === "Peruttu" || $tila === "Saapunut") {
        $curl = curl_init();

        curl_setopt(
            $curl,
            CURLOPT_URL,
            $apiaddress
        );

        curl_setopt(
            $curl,
            CURLOPT_CUSTOMREQUEST,
            "POST"
        );

        curl_setopt(
            $curl,
            CURLOPT_POSTFIELDS,
            json_encode([
                "id" => $id,
                "tila" => $tila,
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

        curl_setopt(
            $curl,
            CURLOPT_RETURNTRANSFER,
            true
        );

        $vastaus = curl_exec($curl);

        if ($vastaus === false) {
            $_SESSION["auto_viesti"] = "Virhe API-pyynnössä: " . curl_error($curl);
            
        } else {
            $_SESSION["auto_viesti"] = "Ajanvarauksen tila päivitetty onnistuneesti.";
        }
        
header("Location: index.php");
exit;
        curl_close($curl);
    } else {
        $_SESSION["auto_viesti"] = "Virheellinen tila. Sallitut arvot ovat: Varattu, Peruttu, Suoritettu.";
    }
}
// Haetaan kaikki autot API:sta
$curl = curl_init();

curl_setopt(
    $curl,
    CURLOPT_URL,
    $apiaddress . ($id !== "" ? "?id=" . urlencode($id) : "")
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
<html lang="fi">
<head>
    <title>Ajanvarauksen hallinta</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Ajanvarauksen hallinta</h1>
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
    <div class="haku">
        <form method="post" action="index.php">
            <label for="omistaja">Omistaja:</label>
            <input type="text" id="omistaja" name="omistaja" value="<?php echo htmlspecialchars($omistaja, ENT_QUOTES, 'UTF-8'); ?>">
            <br>
            <label for="elain">Eläin:</label>
            <input type="text" id="elain" name="elain" value="<?php echo htmlspecialchars($elain, ENT_QUOTES, 'UTF-8'); ?>">
            <br>
            <label for="laji">Laji:</label>
            <input type="text" id="laji" name="laji" value="<?php echo htmlspecialchars($laji, ENT_QUOTES, 'UTF-8'); ?>">
            <br>
            <label for="ika">Ikä:</label>
            <input type="text" id="ika" name="ika" value="<?php echo htmlspecialchars($ika, ENT_QUOTES, 'UTF-8'); ?>">
            <br>
            <label for="puhelin">Puhelin:</label>
            <input type="text" id="puhelin" name="puhelin" value="<?php echo htmlspecialchars($puhelin, ENT_QUOTES, 'UTF-8'); ?>">
            <br>
            <label for="kayntipaiva">Käyntiaika:</label>
            <input type="datetime-local" id="kayntipaiva" name="kayntipaiva" value="<?php echo htmlspecialchars($kayntipaiva, ENT_QUOTES, 'UTF-8'); ?>">
            <br>
            <label for="syy">Syy:</label>
            <input type="text" id="syy" name="syy" value="<?php echo htmlspecialchars($syy, ENT_QUOTES, 'UTF-8'); ?>">
            <br>
            <label for="tila">Tila:</label>
            <input type="text" id="tila" name="tila" value="<?php echo htmlspecialchars($tila, ENT_QUOTES, 'UTF-8'); ?>">
            <br>
            <hr>
            <br>
            <button type="submit">Lisää ajanvaraus</button>
        </form>
    </div>
    <div class="sisalto">
        <h2>Ajanvaraukset</h2>
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tuotteet as $tuote): 
                    if((isset($_GET["id"]) && $_GET["id"] == $tuote['id']) || (isset($_GET["haku"]) && $_GET["haku"] == $tuote['elain'])) {
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['omistaja'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['elain'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['laji'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['ika'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['puhelin'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['kayntipaiva'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['kellonaika'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['syy'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['tila'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                        <?php
                    }
                    else {
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['omistaja'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['elain'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['laji'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['ika'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['puhelin'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['kayntipaiva'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['kellonaika'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['syy'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($tuote['tila'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php } ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Ei ajanvarauksia saatavilla.</p>
        <?php endif; ?>
    </div>
