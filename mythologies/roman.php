<?php
session_start();
$current_page = 'roman';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senās Teikas - Romiešu Mitoloģija</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="content">
    <section class="mythology-section">
            <h2>Romiešu Mitoloģija</h2>
            <div class="mythology-content">
                <div class="mythology-intro">
                    <p>Romiešu mitoloģija, kas lielā mērā balstīta uz grieķu mitoloģiju, tomēr attīstīja savu unikālo identitāti un pielāgoja stāstus Romas kultūrai un vērtībām.</p>
                </div>
                
                <div class="mythology-categories">
                    <div class="category">
                        <h3>Galvenie Dievi</h3>
                        <ul>
                            <li>Jupiters - Debesu dievs, dievu valdnieks (grieķu Zevs)</li>
                            <li>Junona - Laulības dieve, Jupitera sieva</li>
                            <li>Marss - Kara dievs (grieķu Arējs)</li>
                            <li>Venera - Mīlestības dieve (grieķu Afrodīte)</li>
                            <li>Minerva - Gudrības dieve (grieķu Atēna)</li>
                            <li>Neptūns - Jūras dievs (grieķu Poseidons)</li>
                        </ul>
                        <a href="roman/gods.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Romas Dieviem
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Romas Leģendas</h3>
                        <ul>
                            <li>Romuls un Rems - Romas dibinātāji</li>
                            <li>Enejs - Trojas varonis, romiešu ciltstēvs</li>
                            <li>Latīns - Latīņu ķēniņš</li>
                            <li>Sabīniešu sievietes - Romas izcelšanās stāsts</li>
                            <li>Horācija brāļi - Varonīgi romiešu karavīri, kas cīnījās pret Alba Longu</li>
                            <li>Numa Pompilijs - Otrs Romas karalis, reliģisko tradīciju veidotājs</li>
                        </ul>
                        <a href="roman/legends.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Leģendām
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Svētās Vietas un Rituāli</h3>
                        <ul>
                            <li>Vestas templis - Mūžīgās uguns mājvieta</li>
                            <li>Kapitolija templis - Jupitera galvenā svētnīca</li>
                            <li>Luperkālijas - Auglības svētki</li>
                            <li>Saturnālijas - Ziemas saulgriežu svinības</li>
                            <li>Dīmeda svētnīca - Miera un labklājības svētki</li>
                            <li>Dīānas mežs - Svēta vieta un medību dievietes pielūgsme</li>
                        </ul>
                        <a href="roman/rituals.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Rituāliem
                        </a>
                    </div>

                    <div class="category">
                        <h3>Mītiskās Būtnes</h3>
                        <ul>
                            <li>Fauns - Meža gars un lauksaimniecības aizbildnis</li>
                            <li>Lārs - Mājas gars un ģimenes aizbildnis</li>
                            <li>Penāti - Ģimenes gari un virtuvēs dzīvojošie aizbildņi</li>
                            <li>Vesta - Uguns dieve un mājas aizbildne</li>
                            <li>Silvāns - Meža dievs un lauksaimniecības aizbildnis</li>
                            <li>Pomona - Augļu dieve un dārza aizbildne</li>
                        </ul>
                        <a href="roman/creatures.html" class="show-more-btn">
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