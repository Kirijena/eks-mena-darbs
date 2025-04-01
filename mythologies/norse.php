<?php
session_start();
$current_page = 'norse';
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
                    <p>Skandināvu Mitoloģija ietver leģendas par dieviem (Odiens, Tors, Loki) ], mītiskām būtnēm un motikumiem, tostarp Ragnatoku.</p>
                </div>
                
                <div class="mythology-categories">
                    <div class="category">
                        <h3>Dievi</h3>
                        <ul>
                            <li>Odins - Visu tēvs, gudrības dievs </li>
                            <li>Tors - Pērkona dievs</li>
                            <li>Loki - Viltības dievs</li>
                            <li>Freija - Mīlestības un skaistuma dieve</li>
                            <li>Baldrs - Gaismas un tīrības dievs</li>
                            <li>Hēnirs - Uguns un rūpniecības dievs</li>
                        </ul>
                        <a href="norse/gods.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Skandināvu
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Mītiskās Būtnes</h3>
                        <ul>
                            <li>Valkyras - Kara jaunavas</li>
                            <li>Jotuni - Milži</li>
                            <li>Dvergi - Rūķi</li>
                            <li>Fenrirs - Lielais vilks</li>
                            <li>Nīfas - Ūdens gara būtības</li>
                            <li>Draugar - Mirušie gari</li>
                        </ul>
                        <a href="norse/creatures.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Būtnēm
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Svarīgi Notikumi</h3>
                        <ul>
                            <li>Ragnaroks - Pasaules gals</li>
                            <li>Pasaules koka Igrasīla stasts</li>
                            <li>Tora āmura Mjolnira stāsta</li>
                            <li>Bifrost tilts</li>
                            <li>Freijas laulība ar Odrī</li>
                            <li>Dievu karš</li>
                        </ul>
                        <a href="norse/events.html" class="show-more-btn">
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