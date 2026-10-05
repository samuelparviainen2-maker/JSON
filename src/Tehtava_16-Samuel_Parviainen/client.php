<?php
$latitude = 62.8924;
$longitude = 27.6770;
// Met.no API URL
$url = "https://api.met.no/weatherapi/locationforecast/2.0/compact?lat=$latitude&lon=$longitude";
// Luodaan HTTP konteksti headerilla
$options = [
"http" => [
"header" => "User-Agent: OmaSovellus/1.0 oma.samuelparviainen2@gmail.com\r\n"
]
];
$context = stream_context_create($options);
// Haetaan data
$response = file_get_contents($url, false, $context);
if ($response === FALSE) {
    die("Säädatan haku epäonnistui.");
}
// Muutetaan JSON taulukoksi
$data = json_decode($response, true);
// Haetaan ensimmäiset 6 aikapistettä
$aikasarja = array_slice($data['properties']['timeseries'], 0, 6);
function getSuomi($koodi) {
    $taulukko = [
        'clearsky_day' => 'Selkeää',
        'clearsky_night' => 'Selkeää yöllä',
        'partlycloudy_day' => 'Puolipilvistä',
        'partlycloudy_night' => 'Puolipilvistä yöllä',
        'cloudy' => 'Pilvistä',
        'fair_day' => 'Kohtalaisesti selkeää',
        'fair_night' => 'Kohtalaisesti selkeää yöllä',
        'rain' => 'Sataa',
        'light_rain' => 'Kevyt sade',
        'heavy_rain' => 'Rankka sade',
        'snow' => 'Lunta',
        'sleet' => 'Räntää'
    ];
    return $taulukko[$koodi] ?? $koodi;
}

?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Säädata</title>
    <style>
        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, sans-serif;
            background-image: url(https://storage.needpix.com/rsynced_images/sky-and-clouds-1334255_1280.jpg);
            background-size: cover;
            
        }
        table {
            align: center;
            width: 40%;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            border-radius: 10px;
        }
        h1 {
            text-align: center;
            background-color: #f2f4f7;
            padding: 10px;
            border-radius: 10px;
            width: 40%;
            margin: 0 auto 20px auto;
        }
        .lampotila-kylma { background-color: #bfe3ff; }
        .lampotila-cool { background-color: #dfeeff; }
        .lampotila-medio { background-color: #fff0b3; }
        .lampotila-lampo { background-color: #ffd6a5; }
    </style>
</head>
<body>
    <h1>Säädata</h1>
    <table>
        <tr>
            <th>Päivä</th>
            <th>Aika</th>
            <th>Lämpötila (°C)</th>
            <th>Sää</th>
            <th>12-tunnissa</th>
            
        </tr>
<?
foreach ($aikasarja as $aika) {
    $time = $aika['time'];
    [$paiva, $kello] = explode("T", $time);
    $aikaKello = str_replace("Z", "", $kello);
    $temp = $aika['data']['instant']['details']['air_temperature'];
    $symbol = $aika['data']['next_1_hours']['summary']['symbol_code'];
    $symbol12 = $aika['data']['next_12_hours']['summary']['symbol_code'];
    $suomi = getSuomi($symbol);
    $suomi12 = getSuomi($symbol12);

    if ($temp < 0) {
    $luokka = "lampotila-kylma";
} elseif ($temp < 10) {
    $luokka = "lampotila-cool";
} elseif ($temp < 20) {
    $luokka = "lampotila-medio";
} else {
    $luokka = "lampotila-lampo";
}
    echo "<tr><td>$paiva<hr></td><td>$aikaKello<hr></td><td class='$luokka'>$temp<hr></td><td>$suomi<hr></td><td>$suomi12<hr></td></tr>";
}
?>
    </table>