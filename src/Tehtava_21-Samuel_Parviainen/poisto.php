<?php
session_start();
$apiOsoite = "http://localhost/Tehtava_21-Samuel_Parviainen/api.php";
    $viesti = $_SESSION["auto_viesti"] ?? "";
    unset($_SESSION["auto_viesti"]);



// Kun lomake lähetetään
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    $id = trim($_POST["id"] ?? "");
    {
 
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
            "DELETE"
        );
 
        // Lähetettävät tiedot
        curl_setopt(
            $curl,
            CURLOPT_POSTFIELDS,
            json_encode([
                "id" => $id,
                
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


if($_SERVER["REQUEST_METHOD"] === "GET") {
    $id = trim((string) ($_GET["id"] ?? ""));
    $elain = trim((string) ($_GET["elain"] ?? ""));
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
<html lang="fi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ajanvarauksen Poisto</title>
        <link rel="stylesheet" href="style.css">
        </head>
    <body>
        <?php include 'header.php'; ?>
        <?php if ($viesti !== ""): ?>
            <div class="viesti">
                <p><?php echo htmlspecialchars($viesti, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <?php endif; 
        
        if ($hakuvirhe !== ""): ?>
            <div class="viesti">
                <p><?php echo htmlspecialchars($hakuvirhe, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        <?php endif; ?>
        
        <h1>Ajanvarauksen Poisto</h1>
        <div class="sailio">
            <div class="lista">
        <h2>Ajanvaraukset</h2>
        <br>
        <hr>
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
                        <th>Poista</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tuotteet as $tuote): 
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
                            <form method="post" action="poisto.php">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                <td><button type="submit">Poista</button></td>
                            </form>
                        </tr>
                    
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Ei ajanvarauksia saatavilla.</p>
        <?php endif; ?>
        </div>