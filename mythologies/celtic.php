<?php
session_start();
$current_page = 'celtic';
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
                            <li>Dagda - Visu tēvs, gudrības un maģijas dievs</li>
                            <li>Morrigana - Kara un likteņa dieve</li>
                            <li>Lugh - Saules un mākslu dievs</li>
                            <li>Brigida - Dzejnieku un amatnieku dieve</li>
                            <li>Cernunns - Meža un dabas dievs</li>
                            <li>Danu - Māte dieve, Tuatha Dé Danann ciltstēvs</li>
                        </ul>
                        <a href="celtic/gods.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Ķeltu Dieviem
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Mistiskās Būtnes</h3>
                        <ul>
                            <li>Sidhe - Pakalnu fejas</li>
                            <li>Leprechaun - Kurpnieku rūķis</li>
                            <li>Banshee - Nāves vēstnese</li>
                            <li>Selkie - Roņu tautas pārstāvji</li>
                            <li>Kelpie - Ūdens zirgs</li>
                            <li>Pooka - Pārvērtību gars</li>
                        </ul>
                        <a href="celtic/creatures.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Mistiskajām Būtnēm
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Svētās Vietas un Tradīcijas</h3>
                        <ul>
                            <li>Tara - Senīru karaļu rezidence</li>
                            <li>Samhain - Gadumijas svētki</li>
                            <li>Beltane - Pavasara svētki</li>
                            <li>Druīdu rituāli - Senā priesteru tradīcija</li>
                            <li>Ogham - Senā rakstu sistēma</li>
                            <li>Stonehenge - Senā svētā vieta un rituālu centrs.</li>
                        </ul>
                        <a href="celtic/traditions.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Tradīcijām
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Svētās Vietas un Simboli</h3>
                        <ul>
                            <li>Triskelions - Trīs spirāļu simbols ar dziļu nozīmi</li>
                            <li>Ņūgreindža - Senais saules templis Īrijā</li>
                            <li>Avalona - Mistiskā sala, kur dzīvo fejas</li>
                            <li>Keltu Krusts - Saules un kristietības apvienojums</li>
                            <li>Svētie Avoti - Dziedināšanas un gudrības vietas</li>
                            <li>Ogama Raksti - Senā ķeltu rakstu sistēma</li>
                        </ul>
                        <a href="celtic/sacred-places.html" class="show-more-btn">
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