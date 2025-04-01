<?php
session_start();
$current_page = 'egyptian';
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
                            <li>Ra - Saules dievs, radītājs</li>
                            <li>Ozīriss - Mirušo valstības valdnieks</li>
                            <li>Izīda - Maģijas un dzīvības dieve</li>
                            <li>Hors - Debesu dievs, faraonu aizbildnis</li>
                            <li>Anubis - Balzamēšanas un mirušo pavadonis</li>
                            <li>Tots - Gudrības un rakstības dievs</li>
                        </ul>
                        <a href="egyptian/gods.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Ēģiptes Dieviem
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Pēcnāves Dzīve</h3>
                        <ul>
                            <li>Duat - Pazemes valstība</li>
                            <li>Mirušo grāmata - Ceļvedis aizsaulē</li>
                            <li>Sirds svēršana - Dvēseles tiesāšana</li>
                            <li>Mūmifikācija - Ķermeņa saglabāšana</li>
                            <li>Pasaules koks - Dzīvības un nāves cikls</li>
                            <li>Amenti - Pazemes ceļš, caur kuru dvēseles ceļo uz mūžīgo atpūtu</li>
                        </ul>
                        <a href="egyptian/afterlife.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Pēcnāves Dzīvi
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Svētie Simboli un Rituāli</h3>
                        <ul>
                            <li>Ankh - Mūžīgās dzīvības simbols</li>
                            <li>Skarabejs - Atdzimšanas simbols</li>
                            <li>Piramīdas - Faraonu kapavietas</li>
                            <li>Nīlas plūdi - Dzīvības cikla atjaunošanās</li>
                            <li>Hieroglifi - Svētie raksti</li>
                            <li>Horusa acs - Aizsardzības un dziedināšanas simbols</li>
                        </ul>
                        <a href="egyptian/symbols.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Simboliem un Rituāliem
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Mītiskās Būtnes</h3>
                        <ul>
                            <li>Sfinksa - Noslēpumainais cilvēka-lauvas hibrīds</li>
                            <li>Ammit - Dvēseļu rijējs ar krokodila, lauvas un nīlzirga daļām</li>
                            <li>Bennu - Svētais uguns putns, Ra iemiesojums</li>
                            <li>Apeps - Haosa čūska, mūžīgais Ra ienaidnieks</li>
                            <li>Šai - Laimes un likteņa aizbildnis</li>
                            <li>Tueris - Grūtnieču un bērnu aizsardzības dievība</li>
                        </ul>
                        <a href="egyptian/creatures.html" class="show-more-btn">
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