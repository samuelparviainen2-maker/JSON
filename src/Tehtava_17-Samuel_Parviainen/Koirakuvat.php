<?php
$json = file_get_contents('https://dog.ceo/api/breeds/image/random/10');
$data = json_decode($json, true);

if(!is_array($data) || !isset($data['message'])) {
    die("Koirakuvien haku epäonnistui.");
}
else {
    $koirakuvat = $data['message'];
}

function getRandomKoirakuva($koirakuvat) {
    $randomIndex = array_rand($koirakuvat);
    return htmlspecialchars($koirakuvat[$randomIndex]);
}
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koirakuvat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f4f7;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        form {
            text-align: center;
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
        }
        button:hover {
            background-color: #45a049;

        }
        img {
            display: block;
            margin: 20px auto;
            max-width: 100%;
            height: auto;
            border-radius: 10px;

        }
        </style>
</head>
<body>
    <h1>Koirakuvat</h1>

    <?php if($_SERVER['REQUEST_METHOD'] == 'POST') { 
        $koirakuva = getRandomKoirakuva($koirakuvat);
        echo "<img src='$koirakuva' alt='Koirakuva' style='max-width: 100%; height: auto;'>";
    } ?>

    <form method="post">
        <button type="submit">Hae satunnainen koirakuva</button>
    </form>

</body>
</html>
