<?php
session_start();
$current_page = 'mythologies';

$serveris = "localhost";
$lietotajs = "grobina1_belovinceva";
$parole = "U9R1@kvzL";
$datubaze = "grobina1_belovinceva";

$savienojums = mysqli_connect($serveris, $lietotajs, $parole, $datubaze);

if (!$savienojums) {
    die("Savienojuma kļūda: " . mysqli_connect_error());
}

mysqli_set_charset($savienojums, "utf8mb4");

// Get all unique mythology types from the database
$sql = "SELECT DISTINCT Nosaukums FROM eksamens_categories WHERE Nosaukums LIKE '%Mitoloģija' ORDER BY Nosaukums";
$rezultats = mysqli_query($savienojums, $sql);

$mitologijas = [];
while ($row = mysqli_fetch_assoc($rezultats)) {
    $mitologijas[] = $row['Nosaukums'];
}

mysqli_close($savienojums);
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senās Teikas - Mitoloģijas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '/header.php'; ?>

    <main class="content">
        <section class="mythology-section">
            <h2>Mitoloģijas</h2>
            <div class="mythology-content">
                <div class="mythology-intro">
                    <p>Izpētiet dažādas pasaules mitoloģijas un to stāstus.</p>
                </div>
                
                <div class="mythology-list">
                    <?php foreach ($mitologijas as $mitologija): ?>
                        <div class="mythology-item">
                            <a href="template.php?mitologija=<?php echo urlencode($mitologija); ?>" class="mythology-link">
                                <h3><?php echo htmlspecialchars($mitologija); ?></h3>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
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