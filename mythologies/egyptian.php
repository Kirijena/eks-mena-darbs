<?php
session_start();
$current_page = 'egyptian';

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
    $sql = "SELECT virsraksti, iss_apraksts FROM eksamens_ieraksti WHERE zem_tipa = ? AND veids = 'Egiptes Mitologija' ORDER BY ieraksti_id";

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


$galvenie_dievi = iegutDatus($savienojums, "Galvenie Dievi");
$pecnaves_dzive = iegutDatus($savienojums, "Pecnaves Dzive");
$svetie_simboli_un_rituali = iegutDatus($savienojums, "Svetie Simboli un Rituali");
$mitiskas_butnes = iegutDatus($savienojums, "Mitiskas Butnes");

mysqli_close($savienojums);
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senās Teikas - Ēģiptes Mitoloģija</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="content">
    <section class="mythology-section">
            <h2>Ēģiptes Mitoloģija</h2>
            <div class="mythology-content">
                <div class="mythology-intro">
                    <p>Senās Ēģiptes mitoloģija ir viena no senākajām zināmajām reliģiskajām sistēmām, kas attīstījās Nīlas ielejā. Tā ietver sarežģītas attiecības starp dieviem, faraoniem un pēcnāves dzīvi.</p>
                </div>
                
                <div class="mythology-categories">
                    <div class="category">
                        <h3>Galvenie Dievi</h3>
                        <ul>
                            <?php if (!empty($galvenie_dievi)): ?>
                                <?php foreach ($galvenie_dievi as $butne): ?>
                                    <li><?php echo htmlspecialchars($butne["nosaukums"]); ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Nav pieejamu datu</li>
                            <?php endif; ?>
                        </ul>
                        <a  class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Ēģiptes Dieviem
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Pēcnāves Dzīve</h3>
                        <ul>
                            <?php if (!empty($pecnaves_dzive)): ?>
                                <?php foreach ($pecnaves_dzive as $butne): ?>
                                    <li><?php echo htmlspecialchars($butne["nosaukums"]); ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Nav pieejamu datu</li>
                            <?php endif; ?>
                        </ul>
                        <a class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Pēcnāves Dzīvi
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Svētie Simboli un Rituāli</h3>
                        <ul>
                            <?php if (!empty($svetie_simboli_un_rituali)): ?>
                                <?php foreach ($svetie_simboli_un_rituali as $butne): ?>
                                    <li><?php echo htmlspecialchars($butne["nosaukums"]); ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Nav pieejamu datu</li>
                            <?php endif; ?>
                        </ul>
                        <a  class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Simboliem un Rituāliem
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Mītiskās Būtnes</h3>
                        <ul>
                            <?php if (!empty($mitiskas_butnes)): ?>
                                <?php foreach ($mitiskas_butnes as $butne): ?>
                                    <li><?php echo htmlspecialchars($butne["nosaukums"]); ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Nav pieejamu datu</li>
                            <?php endif; ?>
                        </ul>
                        <a  class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Mītiskajām Būtnēm
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