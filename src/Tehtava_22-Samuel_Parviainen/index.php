<?php
session_start();

$apiaddress = "http://localhost/Tehtava_21-Samuel_Parviainen/api.php";
$viesti = $_SESSION["auto_viesti"] ?? "";
unset($_SESSION["auto_viesti"]);

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $tapa = $_POST['tapa'];

    if($tapa === 'asiakkaat'):
        $etunimi = $_POST["asiakas-etunimi"] ?? "";
        $sukunimi = $_POST["asiakas-sukunimi"] ?? "";
        $puhelin = $_POST["asiakas-puhelin"] ?? "";
        $sahkoposti = $_POST["asiakas-sahkoposti"] ?? "";
        $syntymaaika = $_POST["asiakas-syntymaaika"] ?? "";
    
    endif;

    if($tapa === 'huoneet'):
        $huonenumero = $_POST["huone-huonenumero"] ?? "";
        $tyyppi = $_POST["huone-tyyppi"] ?? "";
        $kerros = $_POST["huone-kerros"] ?? "";
        $hinta = $_POST["huone-hinta"] ?? "";
        $tila = $_POST["huone-tila"] ?? "";

    endif;

    if($tapa === 'varaukset'):
        $asiakas_id = $_POST["varaus-asiakas-id"] ?? "";
        $huone_id = $_POST["varaus-huone-id"] ?? "";
        $saapuminen = $_POST["varaus-saapuminen"] ?? "";
        $lahteminen = $_POST["varaus-lahteminen"] ?? "";
        $henkilomaara = $_POST["varaus-henkilomaara"] ?? "";
        $tila = $_POST["varaus-tila"] ?? "";
        $lisatiedot = $_POST["varaus-lisatiedot"] ?? "";
    endif;
}


// Haetaan ajanvaraukset API:sta
$parametrit = array_filter(
    ["id" => $id, "elain" => $elain],
    static fn ($arvo) => $arvo !== ""
);
$kysely = http_build_query($parametrit);
$apiurl = $apiaddress . ($kysely !== "" ? "?" . $kysely : "");

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
        <title>Hotellin hallinta</title>
        <link rel="stylesheet" href="style.css">
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
</head>
<body>
<?php
include "header.php";
?>

<div class="sisalto">
    <div class="formi">

        <div class="valinta">

            <form action="index.php" method="get">
                <h2>Mitä lisäät</h2>

                <label for="asiakkaat">Asiakkaita</label>
                <input type="radio" name="form_tapa" id="asiakkaat" value="asiakkaat">
                <br>
                <label for="huoneet">Huoneita</label>
                <input type="radio" name="form_tapa" id="huoneet" value="huoneet">
                <br>
                <label for="varaukset">Varauksia</label>
                <input type="radio" name="form_tapa" id="varaukset" value="varaukset" checked>
                <br>
                <hr>
                <br>
                <input type="submit" value="Valitse">
            </form>
        </div>
        <div class="kysy">
<?php                 if(isset($_GET['form_tapa'])):  ?>

            <h2>Lisää <?php echo $_GET['form_tapa'];?></h2>
            <br>
            <form action="index.php" method="post">

                <input type="hidden" name="tapa" value="<?php echo $_GET['form_tapa'];   ?>">

                <?php

                    if($_GET['form_tapa'] === 'asiakkaat'){?>

                        <label for="asiakas-etunimi">Etunimi</label>
                        <input type="text" name="asiakas-etunimi" maxlength="50" required>
                        <br>
                        <label for="asiakas-sukunimi">Sukunimi</label>
                        <input type="text" name="asiakas-sukunimi" maxlength="80" required>
                        <br>
                        <label for="asiakas-puhelin">Puhelin</label>
                        <input type="text" name="asiakas-puhelin" maxlength="13" required>
                        <br>
                        <label for="asiakas-sahkoposti">Sähköposti</label>
                        <input type="text" name="asiakas-sahkoposti" maxlength="150" required>
                        <br>
                        <label for="asiakas-syntymaaika">Syntymäaika</label>
                        <input type="date" name="asiakas-syntymaaika" required>
                        <br>
                        <hr>
                        <br>
                        <input type="submit" value="Lisää">
                    <?php }
                    
                    if($_GET['form_tapa'] === 'huoneet'){?>

                        <label for="huone-huonenumero">Huonenumero</label>
                        <input type="number" name="huone-huonenumero" min="0" required>
                        <br>
                        <label for="huone-tyyppi">Tyyppi</label>
                        <input type="text" name="huone-tyyppi" maxlength="50" required>
                        <br>
                        <label for="huone-kerros">Kerros</label>
                        <input type="number" name="huone-kerros" min="0" required>
                        <br>
                        <label for="huone-hinta">Hinta</label>
                        <input type="number" name="huone-hinta" step="0.01" min="0" max="9999999999" required>
                        <br>
                        <label for="huone-tila">Tila</label>
                        <input type="text" name="huone-tila" maxlength="50" required>
                        <br>
                        <hr>
                        <br>
                        <input type="submit" value="Lisää">
                    <?php }

                    if($_GET['form_tapa'] === 'varaukset'){?>

                        <label for="varaus-asiakas-id">Asiakas ID</label>
                        <select name="" required>
                            <?php
                            foreach ($tuotteet as $tuote){
                            if (!empty($tuotteet)){
                                ?>
                                <option value=" <?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8') . '|' . htmlspecialchars($tuote['etunimi'] ?? '', ENT_QUOTES, 'UTF-8') . '.' . htmlspecialchars($tuote['sukunimi'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </option>

                            <?php }} ?>
                        </select>
                        <br>
                        <label for="varaus-huone-id">Huone ID</label>
                        <select name="" required>
                            <?php
                            foreach ($tuotteet as $tuote){
                            if (!empty($tuotteet)){
                                ?>
                                <option value=" <?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8') . '|' . htmlspecialchars($tuote['huonenumero'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </option>

                            <?php }} ?>
                        </select>
                        <br>
                        <label for="varaus-saapuminen">Saapuminen</label>
                        <input type="date" name="" required>
                        <br>
                        <label for="varaus-lahteminen">Lähteminen</label>
                        <input type="date" name="" required>
                        <br>
                        <label for="varaus-henkilomaara">Henkilömäärä</label>
                        <input type="number" name="" min="1" required>
                        <br>
                        <label for="varaus-tila">Tila</label>
                        <input type="text" name="" maxlength="30" required>
                        <br>
                        <label for="varaus-lisatiedot">Lisätiedot</label><br>
                        <textarea name="" required></textarea>
                        <br>
                        <hr>
                        <br>
                        <input type="submit" value="Lisää">
                        
                    <?php }
                endif;
                ?>

            </form>
        </div>
    </div>


    <div class="taulu">

        <div class="otsikko">
            <h1>&#127976;Hotellin huoneiden hallinta&#128214;</h1>
        </div>
        <div class="lista">
            <details>
            <summary>Avaa asiakas lista</summary>
            <div class="content">
                <br>
                <h2>Asiakkaat</h2>
                <br>
                <hr>
                <br>
            </div>
            </details>

            <details>
            <summary>Avaa huone lista</summary>
            <div class="content">
                <br>
                <h2>Huoneet</h2>
                <br>
                <hr>
                <br>

            </div>
            </details>

            <details>
            <summary>Avaa varaus lista</summary>
            <div class="content">
                <br>
                <h2>Varaukset</h2>
                <br>
                <hr>
                <br>
            </div>
            </details>
        </div>
    </div>
</div>

</body>