<?php
session_start();
$current_page = 'celtic';

$serveris = "localhost";
$lietotajs = "grobina1_belovinceva";
$parole = "U9R1@kvzL";
$datubaze = "grobina1_belovinceva";

$savienojums = mysqli_connect($serveris, $lietotajs, $parole, $datubaze);


if (!$savienojums) {
    die("Savienojuma kļūda: " . mysqli_connect_error());
}


mysqli_set_charset($savienojums, "utf8mb4");


function iegutDatus($savienojums, $zem_tipa) {
    $dati = [];
    $sql = "SELECT virsraksti, iss_apraksts FROM eksamens_ieraksti WHERE zem_tipa = ? AND veids = 'Keltu Mitologija' ORDER BY ieraksti_id";

    $stmt = mysqli_prepare($savienojums, $sql);
    if (!$stmt) {
        die("Kļūda SQL pieprasījuma sagatavošanā: " . mysqli_error($savienojums));
    }

    mysqli_stmt_bind_param($stmt, "s", $zem_tipa);
    mysqli_stmt_execute($stmt);
    $rezultats = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($rezultats)) {
        $dati[] = [
            "nosaukums" => $row["virsraksti"],
            "apraksts" => $row["iss_apraksts"]
        ];
    }

    mysqli_stmt_close($stmt);
    return $dati;
}


$dievi_un_dievibas = iegutDatus($savienojums, "Dievi un Dievibas");
$mitiskas_butnes = iegutDatus($savienojums, "Mitiskas Butnes");
$svetas_vietas_un_tradicijas = iegutDatus($savienojums, "Svetas Vietas un Tradicijas");
$svetas_vietas_un_simboli = iegutDatus($savienojums, "Svetas Vietas un Simboli");

mysqli_close($savienojums);
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senās Teikas - Ķeltu Mitoloģija</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="content">
    <section class="mythology-section">
            <h2>Ķeltu Mitoloģija</h2>
            <div class="mythology-content">
                <div class="mythology-intro">
                    <p>Ķeltu mitoloģija ir sena Īrijas, Skotijas, Velsas un citu ķeltu zemju tradīciju kopums. Tā ir bagāta ar stāstiem par varoņiem, dieviem, feju tautām un maģiskiem notikumiem.</p>
                </div>
                
                <div class="mythology-categories">
                    <div class="category">
                        <h3>Dievi un Dievības</h3>
                        <ul>
                            <?php if (!empty($dievi_un_dievibas)): ?>
                                <?php foreach ($dievi_un_dievibas as $butne): ?>
                                    <li><?php echo htmlspecialchars($butne["nosaukums"]); ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Nav pieejamu datu</li>
                            <?php endif; ?>
                        </ul>
                        <a class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Ķeltu Dieviem
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Mistiskās Būtnes</h3>
                        <ul>
                            <?php if (!empty($mitiskas_butnes)): ?>
                                <?php foreach ($mitiskas_butnes as $butne): ?>
                                    <li><?php echo htmlspecialchars($butne["nosaukums"]); ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Nav pieejamu datu</li>
                            <?php endif; ?>
                        </ul>
                        <a class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Mistiskajām Būtnēm
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Svētās Vietas un Tradīcijas</h3>
                        <ul>
                            <?php if (!empty($svetas_vietas_un_tradicijas)): ?>
                                <?php foreach ($svetas_vietas_un_tradicijas as $butne): ?>
                                    <li><?php echo htmlspecialchars($butne["nosaukums"]); ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Nav pieejamu datu</li>
                            <?php endif; ?>
                        </ul>
                        <a class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Tradīcijām
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Svētās Vietas un Simboli</h3>
                        <ul>
                            <?php if (!empty($svetas_vietas_un_simboli)): ?>
                                <?php foreach ($svetas_vietas_un_simboli as $butne): ?>
                                    <li><?php echo htmlspecialchars($butne["nosaukums"]); ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Nav pieejamu datu</li>
                            <?php endif; ?>
                        </ul>
                        <a class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Svētajām Vietām
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div id="authModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <?php include '../includes/login-form.php'; ?>
        </div>
    </div>

    <script src="../script.js"></script>
</body>
</html> 