<?php
session_start();
$current_page = 'norse';

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
    $sql = "SELECT virsraksti, iss_apraksts FROM eksamens_ieraksti WHERE zem_tipa = ? AND veids = 'Skandināvu Mitoloģija' ORDER BY ieraksti_id";

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

$dievi = iegutDatus($savienojums, "Dievi");
$miskas_butnes = iegutDatus($savienojums, "Mītiskās Būtnes");
$svarigi_notikumi = iegutDatus($savienojums, "Svarīgi Notikumi");

mysqli_close($savienojums);
?>


<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senās Teikas - Skandināvu Mitoloģija</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="content">
        <section class="mythology-section">
            <h2>Skandināvu Mitoloģija</h2>
            <div class="mythology-content">
                <div class="mythology-intro">
                    <p>Skandināvu Mitoloģija ietver leģendas par dieviem (Odins, Tors, Loki), mītiskām būtnēm un notikumiem, tostarp Ragnaroku.</p>
                </div>
                
                <div class="mythology-categories">
                <div class="category">
            <h3>Dievi</h3>
                 <ul>
                    <?php if (!empty($dievi)): ?>
                    <?php foreach ($dievi as $dievs): ?>
                        <li><?php echo htmlspecialchars($dievs["nosaukums"]); ?></li>
                    <?php endforeach; ?>
                 <?php else: ?>
                    <li>Nav pieejamu datu</li>
                <?php endif; ?>
                </ul>
                <a class="show-more-btn">
                 <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Dieviem
                    </a>
                </div>

                    
                    <div class="category">
                        <h3>Mītiskās Būtnes</h3>
                        <ul>
                            <?php if (!empty($miskas_butnes)): ?>
                                <?php foreach ($miskas_butnes as $butne): ?>
                                    <li><?php echo htmlspecialchars($butne["nosaukums"]); ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Nav pieejamu datu</li>
                            <?php endif; ?>
                        </ul>
                        <a class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Būtnēm
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Svarīgi Notikumi</h3>
                        <ul>
                            <?php if (!empty($svarigi_notikumi)): ?>
                                <?php foreach ($svarigi_notikumi as $notikums): ?>
                                    <li><?php echo htmlspecialchars($notikums["nosaukums"]); ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Nav pieejamu datu</li>
                            <?php endif; ?>
                        </ul>
                        <a class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Notikumiem
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
