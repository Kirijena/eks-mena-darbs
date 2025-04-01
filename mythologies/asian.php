<?php
session_start();
$current_page = 'asian';
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
                            <li>Jade imperators - Debesu valdnieks</li>
                            <li>Sun Wukong - Pērtiķu karalis</li>
                            <li>Chang'e - Mēness dieve</li>
                            <li>Pangu - Pasaules radītājs</li>
                            <li>Astoņi nemirstīgie - Taoisma svētie</li>
                            <li>Nuwa - Cilvēces radītāja un pasaules glābēja</li>
                        </ul>
                        <a href="asian/chinese.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Ķīniešu Mitoloģiju
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Japāņu Mitoloģija</h3>
                        <ul>
                            <li>Amaterasu - Saules dieve</li>
                            <li>Susanoo - Vētru dievs</li>
                            <li>Jokaji - Nakts gari</li>
                            <li>Kitsune - Lapsu gari</li>
                            <li>Kami - Dabas un senču gari</li>
                            <li>Izanagi un Izanami - Pasaules radītāji</li>
                        </ul>
                        <a href="asian/japanese.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Japāņu Mitoloģiju
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Citas Āzijas Tradīcijas</h3>
                        <ul>
                            <li>Garuda - Hinduisma putnu dievība</li>
                            <li>Nāgas - Indiešu čūsku gari</li>
                            <li>Dokkaebi - Korejiešu goblini</li>
                            <li>Hanuman - Hinduisma pērtiķu dievs</li>
                            <li>Mazu - Ķīniešu jūras dieve</li>
                            <li>Rākšasa - Indiešu ļaunie gari</li>
                        </ul>
                        <a href="asian/other.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Citām Tradīcijām
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Cīņas Mākslas un Karotāji</h3>
                        <ul>
                            <li>Sun Wukong - Pērtiķu karalis ar maģisko nūju</li>
                            <li>Guan Yu - Dievišķais karotājs un taisnīguma simbols</li>
                            <li>Tomoe Gozen - Leģendārā sieviete samuraja</li>
                            <li>Bodhidharma - Šaolina cīņas mākslu dibinātājs</li>
                            <li>Miyamoto Musashi - Nepārspētais zobena meistars</li>
                            <li>Hattori Hanzo - Slavenais nindzja kareivis</li>
                        </ul>
                        <a href="asian/warriors.html" class="show-more-btn">
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