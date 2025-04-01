<?php
session_start();
$current_page = 'greek';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senās Teikas - Sengrieķu Mitoloģija</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="content">
    <section class="mythology-section">
            <h2>Sengrieķu Mitoloģija</h2>
            <div class="mythology-content">
                <div class="mythology-intro">
                    <p>Sengrieķu mitoloģija ir bagāta ar stāstiem par Olimpa dieviem, varoņiem un mistiskām būtnēm. Šie stāsti ir ietekmējuši Rietumu kultūru un mākslu gadsimtiem ilgi.</p>
                </div>
                
                <div class="mythology-categories">
                    <div class="category">
                        <h3>Olimpa Dievi</h3>
                        <ul>
                            <li>Zevs - Debesu un pērkona dievs</li>
                            <li>Atēna - Gudrības un kara mākslas dieve</li>
                            <li>Apollons - Saules, mākslas un pareģošanas dievs</li>
                            <li>Afrodīte - Mīlestības un skaistuma dieve</li>
                            <li>Poseidons - Jūras dievs</li>
                            <li>Arējs - Kara dievs</li>
                        </ul>
                        <a href="greek/gods.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Olimpa Dieviem
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Varoņi un Leģendas</h3>
                        <ul>
                            <li>Hērakls - Spēcīgākais varonis</li>
                            <li>Persejs - Medūzas uzvarētājs</li>
                            <li>Odisejs - Trojas kara varonis</li>
                            <li>Tēsejs - Minotaura uzvarētājs</li>
                            <li>Ahillejs - Trojas kara varonis</li>
                            <li>Orfejs - Talantīgais dziedātājs un pazemes ceļotājs</li>
                        </ul>
                        <a href="greek/heroes.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Varoņiem
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Mītiskās Būtnes</h3>
                        <ul>
                            <li>Medūza - Gorgona ar čūsku matiem</li>
                            <li>Minotaurs - Puscilvēks-pusvērsis</li>
                            <li>Hidra - Daudzgalvains ūdens pūķis</li>
                            <li>Kentauri - Puscilvēki-puszirgi</li>
                            <li>Pegass - Spārnots zirgs</li>
                            <li>Fēnikss - Nemirstīgais uguns putns</li>
                        </ul>
                        <a href="greek/creatures.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Būtnēm
                        </a>
                    </div>

                    <div class="category">
                        <h3>Svarīgie Notikumi</h3>
                        <ul>
                            <li>Trojas Karš - Desmit gadus ilgs karš</li>
                            <li>Hērakla Darbi - Divpadsmit neiespējami uzdevumi</li>
                            <li>Pandoras Kaste - Ļaunumu izcelšanās pasaulē</li>
                            <li>Tezeja un Minotavrs - Labirinta stāsts</li>
                            <li>Prometeja Sods - Uguns zagšana dieviem</li>
                            <li>Perseja Piedzīvojumi - Medūzas uzvarēšana</li>
                        </ul>
                        <a href="greek/events.html" class="show-more-btn">
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