<?php
header("Content-Type: application/json; charset=utf-8");

require 'tietokanta.php';

$metodi = $_SERVER["REQUEST_METHOD"];

///////////////////
//POST
///////////////////

if($metodi === "POST"):
$tiedot = json_decode(file_get_contents("php://input"), true);

$tapa = $tiedot["tapa"] ?? "";

                        
if($tapa === "asiakkaat")
    {
        $etunimi = $tiedot["etunimi"] ?? "";
        $sukunimi = $tiedot["sukunimi"] ?? "";
        $puhelin = $tiedot["puhelin"] ?? "";
        $sahkoposti = $tiedot["sahkoposti"] ?? "";
        $syntymaaika = $tiedot["syntymaaika"] ?? "";

        $lause = $conn->prepare("INSERT INTO $tapa (etunimi, sukunimi, puhelin, sahkoposti, syntymaaika) VALUES (:etunimi, :sukunimi, :puhelin, :sahkoposti, :syntymaaika)");

        $lause->bindValue(":etunimi", $etunimi, PDO::PARAM_STR);
        $lause->bindValue(":sukunimi", $sukunimi, PDO::PARAM_STR);
        $lause->bindValue(":puhelin", $puhelin, PDO::PARAM_STR);
        $lause->bindValue(":sahkoposti", $sahkoposti, PDO::PARAM_STR);
        $lause->bindValue(":syntymaaika", $syntymaaika, PDO::PARAM_STR);
        $lause->execute();
    }

if($tapa === "huoneet")
    {
        $huonenumero = $tiedot["huonenumero"] ?? "";
        $tyyppi = $tiedot["tyyppi"] ?? "";
        $kerros = $tiedot["kerros"] ?? "";
        $hinta = $tiedot["hinta"] ?? "";
        $tila = $tiedot["tila"] ?? "";

        $lause = $conn->prepare("INSERT INTO $tapa (huonenumero, tyyppi, kerros, hinta, tila) VALUES (:huonenumero, :tyyppi, :kerros, :hinta, :tila)");

        $lause->bindValue(":huonenumero", $huonenumero, PDO::PARAM_STR);
        $lause->bindValue(":tyyppi", $tyyppi, PDO::PARAM_STR);
        $lause->bindValue(":kerros", $kerros, PDO::PARAM_INT); 
        $lause->bindValue(":hinta", $hinta, PDO::PARAM_STR);
        $lause->bindValue(":tila", $tila, PDO::PARAM_STR);
        $lause->execute();
    }

if($tapa === "varaukset")
    {
        $asiakas_id = $tiedot["asiakas_id"] ?? "";
        $huone_id = $tiedot["huone_id"] ?? "";
        $saapuminen = $tiedot["saapuminen"] ?? "";
        $lahteminen = $tiedot["lahteminen"] ?? "";
        $henkilomaara = $tiedot["henkilomaara"] ?? "";
        $tila = $tiedot["tila"] ?? "";
        $lisatiedot = $tiedot["lisatiedot"] ?? "";

        $lause = $conn->prepare("INSERT INTO $tapa (asiakas_id, huone_id, saapuminen, lahteminen, henkilomaara, tila, lisatiedot) VALUES (:asiakas_id, :huone_id, :saapuminen, :lahteminen, :henkilomaara, :tila, :lisatiedot)");

        $lause->bindValue(":asiakas_id", $asiakas_id, PDO::PARAM_INT);
        $lause->bindValue(":huone_id", $huone_id, PDO::PARAM_INT);
        $lause->bindValue(":saapuminen", $saapuminen, PDO::PARAM_STR);
        $lause->bindValue(":lahteminen", $lahteminen, PDO::PARAM_STR);
        $lause->bindValue(":henkilomaara", $henkilomaara, PDO::PARAM_INT);
        $lause->bindValue(":tila", $tila, PDO::PARAM_STR);
        $lause->bindValue(":lisatiedot", $lisatiedot, PDO::PARAM_STR);
        $lause->execute();
    }

    echo json_encode([
        "onnistui" => true,
        "viesti" => "Ajanvaraus lisätty onnistuneesti."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

endif;



///////////////////
//PUT
///////////////////



if($metodi === "PUT"):
$tiedot = json_decode(file_get_contents("php://input"), true);
    $tapa = $tiedot["tapa"] ?? "";

if($tapa === "asiakkaat")
    {
        $asiakkaan_id = $tiedot["asiakkaan_id"] ?? "";
        $etunimi = $tiedot["etunimi"] ?? "";
        $sukunimi = $tiedot["sukunimi"] ?? "";
        $puhelin = $tiedot["puhelin"] ?? "";
        $sahkoposti = $tiedot["sahkoposti"] ?? "";
        $syntymaaika = $tiedot["syntymaaika"] ?? "";

        $lause = $conn->prepare("UPDATE asiakkaat
SET etunimi = :etunimi,
    sukunimi = :sukunimi,
    puhelin = :puhelin,
    sahkoposti = :sahkoposti,
    syntymaaika = :syntymaaika
WHERE id = :asiakkaan_id");

        $lause->bindValue(":etunimi", $etunimi, PDO::PARAM_STR);
        $lause->bindValue(":sukunimi", $sukunimi, PDO::PARAM_STR);
        $lause->bindValue(":puhelin", $puhelin, PDO::PARAM_STR);
        $lause->bindValue(":sahkoposti", $sahkoposti, PDO::PARAM_STR);
        $lause->bindValue(":syntymaaika", $syntymaaika, PDO::PARAM_STR);
        $lause->bindValue(":asiakkaan_id", $asiakkaan_id, PDO::PARAM_INT);
        $lause->execute();
    }

if($tapa === "huoneet")
    {
        $huoneen_id = $tiedot["huoneen_id"] ?? "";
        $huonenumero = $tiedot["huonenumero"] ?? "";
        $tyyppi = $tiedot["tyyppi"] ?? "";
        $kerros = $tiedot["kerros"] ?? "";
        $hinta = $tiedot["hinta"] ?? "";
        $tila = $tiedot["tila"] ?? "";

        $lause = $conn->prepare("UPDATE $tapa SET huonenumero = :huonenumero, tyyppi = :tyyppi, kerros = :kerros, hinta = :hinta, tila = :tila WHERE id = :huoneen_id");

        $lause->bindValue(":huonenumero", $huonenumero, PDO::PARAM_STR);
        $lause->bindValue(":tyyppi", $tyyppi, PDO::PARAM_STR);
        $lause->bindValue(":kerros", $kerros, PDO::PARAM_INT); 
        $lause->bindValue(":hinta", $hinta, PDO::PARAM_STR);
        $lause->bindValue(":tila", $tila, PDO::PARAM_STR);
        $lause->bindValue(":huoneen_id", $huoneen_id, PDO::PARAM_INT);
        $lause->execute();
    }

if($tapa === "varaukset")
    {
        $varauksen_id = $tiedot["varaus_id"] ?? "";
        $asiakas_id = $tiedot["asiakas_id"] ?? "";
        $huone_id = $tiedot["huone_id"] ?? "";
        $saapuminen = $tiedot["saapuminen"] ?? "";
        $lahteminen = $tiedot["lahteminen"] ?? "";
        $henkilomaara = $tiedot["henkilomaara"] ?? "";
        $tila = $tiedot["tila"] ?? "";
        $lisatiedot = $tiedot["lisatiedot"] ?? "";

        $lause = $conn->prepare("UPDATE $tapa SET asiakas_id = :asiakas_id, huone_id = :huone_id, saapuminen = :saapuminen, lahteminen = :lahteminen, henkilomaara = :henkilomaara, tila = :tila, lisatiedot = :lisatiedot WHERE id = :varauksen_id");

        $lause->bindValue(":asiakas_id", $asiakas_id, PDO::PARAM_INT);
        $lause->bindValue(":huone_id", $huone_id, PDO::PARAM_INT);
        $lause->bindValue(":saapuminen", $saapuminen, PDO::PARAM_STR);
        $lause->bindValue(":lahteminen", $lahteminen, PDO::PARAM_STR);
        $lause->bindValue(":henkilomaara", $henkilomaara, PDO::PARAM_INT);
        $lause->bindValue(":tila", $tila, PDO::PARAM_STR);
        $lause->bindValue(":lisatiedot", $lisatiedot, PDO::PARAM_STR);
        $lause->bindValue(":varauksen_id", $varauksen_id, PDO::PARAM_INT);
        $lause->execute();


        
        
    }

    

    echo json_encode([
        "onnistui" => true,
        "viesti" => "Ajanvaraus lisätty onnistuneesti."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
endif;





///////////////////
//DELETE
///////////////////

if($metodi === "DELETE"):

    $tiedot = json_decode(file_get_contents("php://input"), true);
    $tapa = $tiedot["tapa"] ?? "";
    if(isset($tiedot["asiakkaan_id"])){
        $id = $tiedot["asiakkaan_id"];
    }
    elseif(isset($tiedot["huoneen_id"])){
        $id = $tiedot["huoneen_id"];
    }
    elseif(isset($tiedot["varaus_id"])){
        $id = $tiedot["varaus_id"];
    }

    if ($id === false || $id < 1) {
        http_response_code(400);
        echo json_encode([
            "onnistui" => false,
            "viesti" => "Virheellinen ID."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $lause = $conn->prepare("DELETE FROM $tapa WHERE id = :id");
    $lause->bindValue(":id", $id, PDO::PARAM_INT);
    $lause->execute();

    echo json_encode([
        "onnistui" => true,
        "viesti" => "Ajanvaraus poistettu onnistuneesti."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

endif;




///////////////////
//GET
///////////////////

if ($metodi === "GET") {
    
    $tapa = $_GET["tapa"] ?? "";
    $sallitutTaulut = ["asiakkaat", "huoneet", "varaukset"];

    //Tarkistaa jos haettava taulu on taulu tietokannassa, ilman sql koodia
    if ($tapa !== "" && !in_array($tapa, $sallitutTaulut, true)) { 
        http_response_code(400);
        echo json_encode(["onnistui" => false, "viesti" => "Virheellinen tietotyyppi."]);
        exit;
    }

    $haku = trim((string) ($_GET["haku"] ?? ""));
    if ($haku !== "" && $tapa === "asiakkaat") {
        $lause_asiakkaille = $conn->prepare("SELECT * FROM asiakkaat WHERE etunimi LIKE :haku"); //Hakee rivin tietystä taulusta, joka sisältää haettavan asian
        $lause_asiakkaille->bindValue(":haku", "%$haku%", PDO::PARAM_STR);
    } else {
        $lause_asiakkaille = $conn->prepare("SELECT * FROM asiakkaat");
    }

    if ($haku !== "" && $tapa === "huoneet") {
        $lause_huoneille = $conn->prepare("SELECT * FROM huoneet WHERE CAST(huonenumero AS CHAR) LIKE :haku");
        $lause_huoneille->bindValue(":haku", "%$haku%", PDO::PARAM_STR);
    } else {
        $lause_huoneille = $conn->prepare("SELECT * FROM huoneet");
    }

    if ($haku !== "" && $tapa === "varaukset") {
        $lause_varauksille = $conn->prepare("SELECT
            varaukset.id,
            varaukset.asiakas_id,
            varaukset.huone_id,
            varaukset.saapuminen,
            varaukset.lahteminen,
            varaukset.henkilomaara,
            varaukset.tila,
            varaukset.lisatiedot,
            huoneet.huonenumero,
            asiakkaat.etunimi,
            asiakkaat.sukunimi
            FROM varaukset
            LEFT JOIN huoneet ON varaukset.huone_id = huoneet.id
            LEFT JOIN asiakkaat ON varaukset.asiakas_id = asiakkaat.id
            WHERE varaukset.saapuminen = :haku");
        $lause_varauksille->bindValue(":haku", $haku, PDO::PARAM_STR);
    } else {
        $lause_varauksille = $conn->prepare("SELECT
            varaukset.id,
            varaukset.asiakas_id,
            varaukset.huone_id,
            varaukset.saapuminen,
            varaukset.lahteminen,
            varaukset.henkilomaara,
            varaukset.tila,
            varaukset.lisatiedot,
            huoneet.huonenumero,
            asiakkaat.etunimi,
            asiakkaat.sukunimi
            FROM varaukset
            LEFT JOIN huoneet ON varaukset.huone_id = huoneet.id
            LEFT JOIN asiakkaat ON varaukset.asiakas_id = asiakkaat.id");
    }

    $lause_asiakkaille->execute();
    $vastaus_asiakkaille = $lause_asiakkaille->fetchAll(PDO::FETCH_ASSOC);
    $lause_huoneille->execute();
    $vastaus_huoneille = $lause_huoneille->fetchAll(PDO::FETCH_ASSOC);
    $lause_varauksille->execute();
    $vastaus_varauksille = $lause_varauksille->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "asiakkaat" => $vastaus_asiakkaille,
        "huoneet" => $vastaus_huoneille,
        "varaukset" => $vastaus_varauksille,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
?>
