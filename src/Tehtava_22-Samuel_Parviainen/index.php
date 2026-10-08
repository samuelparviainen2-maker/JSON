<?php
session_start();

$apiaddress = "http://localhost/Tehtava_22-Samuel_Parviainen/api.php";
$viesti = $_SESSION["auto_viesti"] ?? "";
unset($_SESSION["auto_viesti"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tapa = $_POST["tapa"] ?? "";
    $teko = $_POST["teko"] ?? "POST";

    $haku = "";
    if(isset($_GET['haku']))
        {
            $haku = $_GET['haku'] ?? ""; //GETille haettava asia jos haetaan endpointilla
        }
    
    

    if($tapa === 'asiakkaat'):
        $asiakkaan_id = $_POST["asiakkaan_id"] ?? "";
        $etunimi = $_POST["etunimi"] ?? $_POST["asiakas-etunimi"] ?? "";
        $sukunimi = $_POST["sukunimi"] ?? $_POST["asiakas-sukunimi"] ?? "";
        $puhelin = $_POST["puhelin"] ?? $_POST["asiakas-puhelin"] ?? "";
        $sahkoposti = $_POST["sahkoposti"] ?? $_POST["asiakas-sahkoposti"] ?? "";
        $syntymaaika = $_POST["syntymaaika"] ?? $_POST["asiakas-syntymaaika"] ?? "";
    
    endif;

    if($tapa === 'huoneet'):
        $huoneen_id = $_POST["huone_id"] ?? "";
        $huonenumero = $_POST["huonenumero"] ?? $_POST["huone-huonenumero"] ?? "";
        $tyyppi = $_POST["tyyppi"] ?? $_POST["huone-tyyppi"] ?? "";
        $kerros = $_POST["kerros"] ?? $_POST["huone-kerros"] ?? "";
        $hinta = $_POST["hinta"] ?? $_POST["huone-hinta"] ?? "";
        $tila = $_POST["tila"] ?? $_POST["huone-tila"] ?? "";

    endif;

    if($tapa === 'varaukset'):
        $varaus_id = $_POST["varaus_id"] ?? "";
        $asiakas_id = $_POST["asiakas_id"] ?? $_POST["varaus-asiakas-id"] ?? "";
        $huone_id = $_POST["huone_id"] ?? $_POST["varaus-huone-id"] ?? "";
        $saapuminen = $_POST["saapuminen"] ?? $_POST["varaus-saapuminen"] ?? "";
        $lahteminen = $_POST["lahteminen"] ?? $_POST["varaus-lahteminen"] ?? "";
        $henkilomaara = $_POST["henkilomaara"] ?? $_POST["varaus-henkilomaara"] ?? "";
        $tila = $_POST["tila"] ?? $_POST["varaus-tila"] ?? "";
        $lisatiedot = $_POST["lisatiedot"] ?? $_POST["varaus-lisatiedot"] ?? "";
    endif;



    //Tiedon siirto apiin
        $curl = curl_init();

        curl_setopt(
            $curl,
            CURLOPT_URL,
            $apiaddress
        );

        curl_setopt(
            $curl,
            CURLOPT_CUSTOMREQUEST,
            $teko
        );
        if($tapa === 'asiakkaat')
            {
                curl_setopt(
                    $curl,
                    CURLOPT_POSTFIELDS,
                    json_encode([
                        
                        "tapa" => $tapa,
                        "haku" => $haku,
                        "asiakkaan_id" => $asiakkaan_id,
                        "etunimi" => $etunimi,
                        "sukunimi" => $sukunimi,
                        "puhelin" => $puhelin,
                        "sahkoposti" => $sahkoposti,
                        "syntymaaika" => $syntymaaika
                    ])
                );

            }

            if($tapa === 'huoneet')
            {
                curl_setopt(
                    $curl,
                    CURLOPT_POSTFIELDS,
                    json_encode([
                        "huoneen_id" => $huoneen_id,
                        "tapa" => $tapa,
                        "haku" => $haku,
                        "huonenumero" => $huonenumero,
                        "tyyppi" => $tyyppi,
                        "kerros" => $kerros,
                        "hinta" => $hinta,
                        "tila" => $tila
                    ])
                );
            }

            if($tapa === 'varaukset')
            {
                curl_setopt(
                    $curl,
                    CURLOPT_POSTFIELDS,
                    json_encode([
                        "varaus_id" => $varaus_id,
                        "tapa" => $tapa,
                        "haku" => $haku,
                        "asiakas_id" => $asiakas_id,
                        "huone_id" => $huone_id,
                        "saapuminen" => $saapuminen,
                        "lahteminen" => $lahteminen,
                        "henkilomaara" => $henkilomaara,
                        "tila" => $tila,
                        "lisatiedot" => $lisatiedot
                    ])
                );
            }

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
    
}


// Haetaan ajanvaraukset API:sta

$tapa = $_POST["tapa"] ?? $_GET["tapa"] ?? "";
$haku = trim((string) ($_POST["haku"] ?? $_GET["haku"] ?? ""));

$parametrit = array_filter(
    ["tapa" => $tapa, "haku" => $haku],
    static fn ($arvo) => $arvo !== ""
);

$apiurl = $apiaddress . "?" . http_build_query($parametrit);

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
    $tuotteet_asiakkaille = [];
    $tuotteet_huoneille = [];
    $tuotteet_varauksille = [];
    $hakuvirhe = curl_error($curl);
} else {
    $api_vastaukset = json_decode(
        $vastaus,
        true
    );

    $hakuvirhe = "";

    if (!is_array($api_vastaukset)) {
        $api_vastaukset = [];
    }

    $tuotteet_asiakkaille = $api_vastaukset["asiakkaat"] ?? [];
    $tuotteet_huoneille = $api_vastaukset["huoneet"] ?? [];
    $tuotteet_varauksille = $api_vastaukset["varaukset"] ?? [];
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
<?php if ($viesti !== ""): ?>
    <div class="viesti">
        <p><?php echo htmlspecialchars($viesti, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <?php endif; 
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

                <input type="hidden" name="tapa" value="<?php echo $_GET['form_tapa']; //Lisätään selvennys siihen mitä yritetään tehdä
                  ?>">
                <input type="hidden" name="teko" value="POST">

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
                        <select name="varaus-asiakas-id" required>
                            <?php
                            foreach ($tuotteet_asiakkaille as $tuote){
                            if (!empty($tuotteet_asiakkaille)){
                                ?>
                                <option value=" <?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8') . '|' . htmlspecialchars($tuote['etunimi'] ?? '', ENT_QUOTES, 'UTF-8') . '.' . htmlspecialchars($tuote['sukunimi'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </option>

                            <?php }} ?>
                        </select>
                        <br>
                        <label for="varaus-huone-id">Huone ID</label>
                        <select name="varaus-huone-id" required>
                            <?php
                            foreach ($tuotteet_huoneille as $tuote){
                            if (!empty($tuotteet_huoneille)){
                                ?>
                                <option value=" <?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8') . '|' . htmlspecialchars($tuote['huonenumero'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </option>

                            <?php }} ?>
                        </select>
                        <br>
                        <label for="varaus-saapuminen">Saapuminen</label>
                        <input type="date" name="varaus-saapuminen" required>
                        <br>
                        <label for="varaus-lahteminen">Lähteminen</label>
                        <input type="date" name="varaus-lahteminen" required>
                        <br>
                        <label for="varaus-henkilomaara">Henkilömäärä</label>
                        <input type="number" name="varaus-henkilomaara" min="1" required>
                        <br>
                        <label for="varaus-tila">Tila</label>
                        <input type="text" name="varaus-tila" maxlength="30" required>
                        <br>
                        <label for="varaus-lisatiedot">Lisätiedot</label><br>
                        <textarea name="varaus-lisatiedot" required></textarea>
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
            <details open>
            <summary>Avaa asiakas lista</summary>
            <div class="content">
                <div class="lista-otsikko">
                <br>
                <h2>Asiakkaat</h2>
                <div class="haku-form">
                    <h3>Hae etunimellä</h3><br>
                    <form action="index.php" method="get">
                        <input type="hidden" name="tapa" value="asiakkaat">
                        <input type="hidden" name="teko" value="GET">

                        <input type="search" name="haku" required>
                        <input type="submit" value="Hae">
                    </form>
                </div>
                </div>
                <br>
                <hr>
                <br>
                
<h2>Asiakkaat</h2>
<?php if (!empty($tuotteet_asiakkaille)): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Etunimi</th>
                <th>Sukunimi</th>
                <th>Puhelin</th>
                <th>Sähköposti</th>
                <th>Syntymäaika</th>
                <th>Muokkaus</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tuotteet_asiakkaille as $tuote): ?>
                <form action="index.php" method="post">
                    <input type="hidden" name="tapa" value="asiakkaat">
                    <input type="hidden" name="teko" value="PUT">
                <tr>
                    <!-- ID -->
                    <td>
                        <input type="number" name="asiakkaan_id" value="<?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                    </td>

                    <!-- Etunimi -->
                    <td>
                        <input type="text" name="etunimi" value="<?php echo htmlspecialchars($tuote['etunimi'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </td>

                    <!-- Sukunimi -->
                    <td>
                        <input type="text" name="sukunimi" value="<?php echo htmlspecialchars($tuote['sukunimi'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </td>

                    <!-- Puhelin -->
                    <td>
                        <input type="tel" name="puhelin" value="<?php echo htmlspecialchars($tuote['puhelin'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </td>

                    <!-- Sähköposti -->
                    <td>
                        <input type="email" name="sahkoposti" value="<?php echo htmlspecialchars($tuote['sahkoposti'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </td>

                    <!-- Syntymäaika -->
                    <td>
                        <input type="date" name="syntymaaika" value="<?php echo htmlspecialchars($tuote['syntymaaika'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </td>
                    <td>
                        <input type="submit" value="Muokkaa">
                    </td>
                </form>

                    <form action="index.php" method="post">
                    <input type="hidden" name="tapa" value="asiakkaat">
                    <input type="hidden" name="teko" value="DELETE">

                    <input type="hidden" name="asiakkaan_id" value="<?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <td><input type="submit" value="Poista"></td>
                </form>

                </tr>
                
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Ei asiakkaita saatavilla.</p>
<?php endif; ?>
                
                
                
            </div>
            </details>

            <details open>
            <summary>Avaa huone lista</summary>
            <div class="content">
                <div class="lista-otsikko">
                <br>
                <h2>Huoneet</h2>
                <div class="haku-form">
                    <h3>Hae huonenumerolla</h3><br>
                    <form action="index.php" method="get">
                        <input type="hidden" name="tapa" value="huoneet">
                        <input type="search" name="haku" required>
                        <input type="submit" value="Hae">
                    </form>
                </div>
                </div>
                <br>
                <hr>
                <br>
                
                <h2>Huoneet</h2>
<?php if (!empty($tuotteet_huoneille)): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Huonenumero</th>
                <th>Tyyppi</th>
                <th>Kerros</th>
                <th>Hinta</th>
                <th>Tila</th>
                <th>Muokkaus</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tuotteet_huoneille as $tuote): ?>
                <form action="index.php" method="post">
                    <input type="hidden" name="tapa" value="huoneet">
                    <input type="hidden" name="teko" value="PUT">
                <tr>
                    <!-- ID -->
                    <td>
                        <input type="number" name="huone_id" value="<?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                    </td>

                    <!-- Huonenumero -->
                    <td>
                        <input type="number" name="huonenumero" value="<?php echo htmlspecialchars($tuote['huonenumero'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </td>

                    <!-- Tyyppi -->
                    <td>
                        <input type="text" name="tyyppi" value="<?php echo htmlspecialchars($tuote['tyyppi'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </td>

                    <!-- Kerros -->
                    <td>
                        <input type="number" name="kerros" value="<?php echo htmlspecialchars($tuote['kerros'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </td>

                    <!-- Hinta -->
                    <td>
                        <input type="number" step="0.01" name="hinta" value="<?php echo htmlspecialchars($tuote['hinta'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </td>

                    <!-- Tila -->
                    <td>
                        <input type="text" name="tila" value="<?php echo htmlspecialchars($tuote['tila'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </td>
                    <td>
                        <input type="submit" value="Muokkaa">
                    </td>
                </form>


                    <form action="index.php" method="post">
                    <input type="hidden" name="tapa" value="huoneet">
                    <input type="hidden" name="teko" value="DELETE">

                    <input type="hidden" name="huone_id" value="<?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <td><input type="submit" value="Poista"></td>
                </form>
                </tr>
                
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Ei huoneita saatavilla.</p>
<?php endif; ?>
            </div>
            </details>

            <details open>
            <summary>Avaa varaus lista</summary>
            <div class="content">
                <div class="lista-otsikko">
                <br>
                <h2>Varaukset</h2>
                <div class="haku-form">
                    <h3>Hae päivällä</h3><br>
                    <form action="index.php" method="get">
                        <input type="hidden" name="tapa" value="varaukset">
                        <input type="hidden" name="teko" value="GET">
                        <input type="date" name="haku" required>
                        <input type="submit" value="Hae">
                    </form>
                </div>
                </div>
                <br>
                <hr>
                <br>

                
                <h2>Varaukset</h2>
<?php if (!empty($tuotteet_varauksille)): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Asiakas</th>
                <th>Huone</th>
                <th>Saapuminen</th>
                <th>Lähteminen</th>
                <th>Henkilömäärä</th>
                <th>Tila</th>
                <th>Lisätiedot</th>
                <th>Muokkaus</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tuotteet_varauksille as $tuote): ?>
                <form action="index.php" method="post">
                    <input type="hidden" name="tapa" value="varaukset">
                    <input type="hidden" name="teko" value="PUT">
                <tr>

                    <!-- ID (piilotettu tai lukittu kenttä muokkauksessa) -->
                <td>
                    <input type="number" name="varaus_id" value="<?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                </td>

                <!-- Asiakas ID -->
                <td>
                    <input type="number" name="asiakas_id" value="<?php echo htmlspecialchars($tuote['asiakas_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </td>

                <!-- Huone ID -->
                <td>
                    <input type="number" name="huone_id" value="<?php echo htmlspecialchars($tuote['huone_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </td>

                <!-- Saapumispäivä -->
                <td>
                    <input type="date" name="saapuminen" value="<?php echo htmlspecialchars($tuote['saapuminen'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </td>

                <!-- Lähtöpäivä -->
                <td>
                    <input type="date" name="lahteminen" value="<?php echo htmlspecialchars($tuote['lahteminen'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </td>

                <!-- Henkilömäärä -->
                <td>
                    <input type="number" name="henkilomaara" min="1" value="<?php echo htmlspecialchars($tuote['henkilomaara'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </td>

                <!-- Tila -->
                <td>
                    <input type="text" name="tila" value="<?php echo htmlspecialchars($tuote['tila'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </td>

                <!-- Lisätiedot -->
                <td>
                    <input type="text" name="lisatiedot" value="<?php echo htmlspecialchars($tuote['lisatiedot'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </td>
                <td>
                    <input type="submit" value="muokkaa">
                </td>
                </form>

                <form action="index.php" method="post">
                    <input type="hidden" name="tapa" value="varaukset">
                    <input type="hidden" name="teko" value="DELETE">

                    <input type="hidden" name="varaus_id" value="<?php echo htmlspecialchars($tuote['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <td><input type="submit" value="Poista"></td>
                </form>
                </tr>
                
                
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Ei varauksia saatavilla.</p>
<?php endif; ?>
                
            </div>
            </details>
        </div>
    </div>
</div>

</body>