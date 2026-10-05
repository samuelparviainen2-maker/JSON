<?php

$viesti = "";

// Lue data API:sta ja dekoodaa JSON
$apiOsoite = "https://api.spot-hinta.fi/Today";
$json = file_get_contents($apiOsoite);
$data = json_decode($json, true);



// Haetaan kaikki tuotteet API:sta
$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $apiOsoite);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$vastaus = curl_exec($curl);

if ($vastaus === false) {
    $tuotteet = [];
    $hakuvirhe = curl_error($curl);
} else {
    $tuotteet = json_decode($vastaus, true);
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
 
    <title>Hintoja</title>
 
    <style>
        body {
            margin: 0;
            padding: 30px;
            
            font-family: Arial, sans-serif;
            background-color: #f2f4f7;
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
 
    <h1>Hintoja lista</h1>
 
    <?php if ($viesti !== ""): ?>
 
        <div class="viesti">
            <?= htmlspecialchars($viesti) ?>
        </div>
 
    <?php endif; ?>
 
    <?php if ($hakuvirhe !== ""): ?>
 
        <div class="virhe">
            Hintojen hakeminen epäonnistui:
            <?= htmlspecialchars($hakuvirhe) ?>
        </div>
 
    <?php endif; ?>
 
    
 
    <div class="laatikko">
 
        <h2>Tallennetut hinnot</h2>
 
        <?php if (count($tuotteet) === 0): ?>
 
            <p class="ei-tuotteita">
                Hintoja ei ole vielä tallennettu.
            </p>
 
        <?php else: ?>
 
            <table>
 
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Aikaleima</th>
                        <th>Hinta ilman veroa</th>
                        <th>Hinta verollinen</th>
 
                <tbody>
 
                <?php foreach ($tuotteet as $tuote): ?>

                    <tr>
                        <td>
                            <?= htmlspecialchars($tuote["Rank"] ?? "") ?>
                        </td>
 
                        <td>
                            <?= htmlspecialchars($tuote["DateTime"] ?? "") ?>
                        </td>
 
                        <td>
                            <?= htmlspecialchars($tuote["PriceNoTax"] ?? "") ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($tuote["PriceWithTax"] ?? "") ?>
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