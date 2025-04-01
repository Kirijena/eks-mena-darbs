<?php
session_start();
$current_page = 'slavic';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senās Teikas - Slāvu Mitoloģija</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="content">
    <section class="mythology-section">
            <h2>Slāvu Mitoloģija</h2>
            <div class="mythology-content">
                <div class="mythology-intro">
                    <p>Slāvu mitoloģija ir bagāta ar dabas gariem, dievībām un rituāliem, kas cieši saistīti ar gadalaiku maiņu un zemkopības cikliem. Tā atspoguļo senslāvu tautu pasaules uzskatu un tradīcijas.</p>
                </div>
                
                <div class="mythology-categories">
                    <div class="category">
                        <h3>Galvenie Dievi</h3>
                        <ul>
                            <li>Peruns - Pērkona un kara dievs</li>
                            <li>Veless - Pazemes un lopu dievs</li>
                            <li>Svarogs - Debesu un uguns dievs</li>
                            <li>Mokoša - Zemes māte, auglības dieve</li>
                            <li>Dažbogs - Saules dievs</li>
                            <li>Stribogs - Vēju valdnieks</li>
                        </ul>
                        <a href="slavic/gods.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Slāvu Dieviem
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Dabas Gari</h3>
                        <ul>
                            <li>Lešijs - Meža gars</li>
                            <li>Rusalka - Ūdens nimfa</li>
                            <li>Domoviks - Mājas gars</li>
                            <li>Vodjaniks - Ūdens gars</li>
                            <li>Vila - Kalnu un meža nimfa</li>
                            <li>Bannik - Pirtī dzīvojošais gars</li>
                        </ul>
                        <a href="slavic/spirits.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Dabas Gariem
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Svētki un Rituāli</h3>
                        <ul>
                            <li>Kupala - Vasaras saulgriežu svētki</li>
                            <li>Masļeņica - Pavasara sagaidīšana</li>
                            <li>Dziady - Senču piemiņas dienas</li>
                            <li>Koļada - Ziemas saulgriežu svinības</li>
                            <li>Raduņica - Pavasara mirušo piemiņas diena</li>
                            <li>Perunovo - Peruna svētki, pavasara sākums</li>
                        </ul>
                        <a href="slavic/festivals.html" class="show-more-btn">
                            <i class="fas fa-arrow-right"></i> Uzzināt Vairāk par Svētkiem
                        </a>
                    </div>
                    
                    <div class="category">
                        <h3>Mītiskās Būtnes</h3>
                        <ul>
                            <li>Baba Jaga - Ragana, kas dzīvo mežā uz vistas kājas mājas</li>
                            <li>Vodjanoi - Ūdens gars, kas dzīvo dziļos ūdeņos</li>
                            <li>Lešij - Meža gars un dzīvnieku aizbildnis</li>
                            <li>Rusalka - Ūdens nimfa ar skaisto balsi</li>
                            <li>Domovoi - Mājas gars un ģimenes sargs</li>
                            <li>Kikimora - Nakts gars, kas nes murgus</li>
                        </ul>
                        <a href="slavic/creatures.html" class="show-more-btn">
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