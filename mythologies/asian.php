<?php
session_start();
$current_page = 'asian';

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
    $sql = "SELECT virsraksti, iss_apraksts FROM eksamens_ieraksti WHERE zem_tipa = ? AND veids = 'Azijas Mitologija' ORDER BY ieraksti_id";

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


$kiniesu_mitologija = iegutDatus($savienojums, "Kiniesu Mitologija");
$japaņu_mitologija = iegutDatus($savienojums, "Japaņu Mitologija");
$citas_azijas_tradicijas = iegutDatus($savienojums, "Citas Azijas Tradicijas");
$cinas_makslas_un_karotaji = iegutDatus($savienojums, "Cīnas Makslas un Karotaji");

mysqli_close($savienojums);
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senās Teikas - Āzijas Mitoloģija</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="content">
    <section class="mythology-section">
            <h2>Āzijas Mitoloģija</h2>
            <div class="mythology-content">
                <div class="mythology-intro">
                    <p>Āzijas mitoloģija aptver plašu kultūru un tradīciju klāstu, ieskaitot ķīniešu, japāņu, korejiešu un citu Āzijas tautu mitoloģiskos stāstus un ticējumus.</p>
                </div>
                
                <div class="mythology-categories">
                    <div class="category">
                        <h3>Ķīniešu Mitoloģija</h3>
                        <ul>
                            <?php if (!empty($kiniesu_mitologija)): ?>
                                <?php foreach ($kiniesu_mitologija as $butne): ?>
                                    <li><?php echo htmlspecialchars($butne["nosaukums"]); ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Nav pieejamu datu</li>
                            <?php endif; ?>
                        </ul>
                        <a class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Ķīniešu Mitoloģiju
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Japāņu Mitoloģija</h3>
                        <ul>
                            <?php if (!empty($japaņu_mitologija)): ?>
                                <?php foreach ($japaņu_mitologija as $butne): ?>
                                    <li><?php echo htmlspecialchars($butne["nosaukums"]); ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Nav pieejamu datu</li>
                            <?php endif; ?>
                        </ul>
                        <a class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Japāņu Mitoloģiju
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Citas Āzijas Tradīcijas</h3>
                        <ul>
                            <?php if (!empty($citas_azijas_tradicijas)): ?>
                                <?php foreach ($citas_azijas_tradicijas as $butne): ?>
                                    <li><?php echo htmlspecialchars($butne["nosaukums"]); ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Nav pieejamu datu</li>
                            <?php endif; ?>
                        </ul>
                        <a class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Citām Tradīcijām
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Cīņas Mākslas un Karotāji</h3>
                        <ul>
                            <?php if (!empty($cinas_makslas_un_karotaji)): ?>
                                <?php foreach ($cinas_makslas_un_karotaji as $butne): ?>
                                    <li><?php echo htmlspecialchars($butne["nosaukums"]); ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Nav pieejamu datu</li>
                            <?php endif; ?>
                        </ul>
                        <a class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Karotājiem
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