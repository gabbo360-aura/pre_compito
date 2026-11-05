<?php
// 1. Inizializziamo le variabili vuote
$prodotto = '';
$prezzo = '';
$sconto = '';

$err_prodotto = '';
$err_prezzo = '';
$err_sconto = '';

$risultato = null;

// 2. Se il form viene inviato con il tasto Calcola
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['prodotto'])) {
        $prodotto = trim($_POST['prodotto']);
    }
    if (isset($_POST['prezzo'])) {
        $prezzo = trim($_POST['prezzo']);
    }
    if (isset($_POST['sconto'])) {
        $sconto = trim($_POST['sconto']);
    }

    $valido = true;

    //CONTROLLO PRODOTTO
    if ($prodotto === '') {
        $err_prodotto = 'Inserisci il nome del prodotto';
        $valido = false;
    } elseif (strlen($prodotto) > 30) {
        $err_prodotto = 'Massimo 30 caratteri';
        $valido = false;
    }

    //CONTROLLO PREZZO
    if ($prezzo === '') {
        $err_prezzo = 'Inserisci il prezzo';
        $valido = false;
    } elseif (!is_numeric($prezzo) || $prezzo <= 0) {
        $err_prezzo = 'Il prezzo deve essere maggiore di 0';
        $valido = false;
    }

    //CONTROLLO SCONTO
    if ($sconto === '') {
        $err_sconto = 'Inserisci lo sconto';
        $valido = false;
    } elseif (!ctype_digit($sconto) || $sconto < 1 || $sconto > 90) {
        $err_sconto = 'Lo sconto deve essere un intero da 1 a 90';
        $valido = false;
    }

    //SE TUTTO È CORRETTO
    if ($valido) {
        $prezzo_num = (float)$prezzo;
        $sconto_num = (int)$sconto;

        // Calcoli
        $risparmio = $prezzo_num * ($sconto_num / 100);
        $prezzo_finale = $prezzo_num - $risparmio;

        // Determina la fascia e il colore della card
        if ($sconto_num < 20) {
            $fascia = 'PICCOLO';
            $colore_card = '#E3F2FD'; // Azzurro
            $bordo_card = '#2196F3';
        } elseif ($sconto_num <= 49) {
            $fascia = 'BUONO';
            $colore_card = '#E8F5E9'; // Verde
            $bordo_card = '#4CAF50';
        } else {
            $fascia = 'SUPER';
            $colore_card = '#FFF3E0'; // Arancione
            $bordo_card = '#FF9800';
        }

        // Dati pronti da mostrare
        $risultato = [
            'prodotto' => $prodotto,
            'prezzo' => number_format($prezzo_num, 2, ',', '.'),
            'sconto' => $sconto_num,
            'risparmio' => number_format($risparmio, 2, ',', '.'),
            'prezzo_finale' => number_format($prezzo_finale, 2, ',', '.'),
            'fascia' => $fascia,
            'colore' => $colore_card,
            'bordo' => $bordo_card
        ];

        // Salvataggio nel file log.csv
        $riga = date('Y-m-d H:i') . ";" . $prodotto . ";" . $prezzo_num . ";" . $sconto_num . ";" . round($risparmio, 2) . ";" . round($prezzo_finale, 2) . ";" . $fascia . "\n";
        file_put_contents('log.csv', $riga, FILE_APPEND);
    }
}

// 3. Lettura dello storico dal file log.csv
$storico = [];
if (file_exists('log.csv')) {
    $righe = file('log.csv', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($righe as $r) {
        $storico[] = explode(';', $r);
    }
}
?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Calcolatore di sconto</title>
    <link rel="stylesheet" href="stile.css">

</head>
<body>
    <h1>Calcolatore di sconto</h1>
    <form method="post" action="index.php">
        <label for="prodotto">Prodotto</label>
        <input type="text" id="prodotto" name="prodotto">
        <label for="prezzo">Prezzo (€)</label>
        <input type="text" id="prezzo" name="prezzo" class="errore">
        <p class="msg-errore">Il prezzo deve essere maggiore di 0</p>
        <label for="sconto">Sconto (%)</label>
        <input type="text" id="sconto" name="sconto">
        <button type="submit">Calcola</button>
    </form>
</body>
</html>