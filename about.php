<?php
session_start();
$current_page = 'about';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senās Teikas - Par Vietni</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

<div class="about-container">
    <section class="hero-section">
        <div class="hero-content">
            <h1>Par mums</h1>
            <p>Laipni lūdzam mūsu mājaslapā, kas veltīta mitoloģijai!</p>
        </div>
    </section>

    <section class="about-section">
        <div class="content">
            <h2>Misija</h2>
            <p>Mēs cenšamies apkopot un piedāvāt informāciju par mītiem no visas pasaules. Mūsu mājaslapa piedāvā dziļākas izpētes, resursus zinātniekiem un iedvesmu radošiem cilvēkiem, kas interesējas par mitoloģiju.</p>
            
            <h2>Vēsture</h2>
            <p>Наш проєкт почався з ідеї створити унікальне джерело інформації про міфи та легенди різних культур. Кожного року ми розширюємо нашу базу даних, додаючи нові міфологічні персонажі, святі місця, ритуали та інше. Ми пишаємося тим, що змогли створити платформу для обміну знаннями між дослідниками та любителями міфології.</p>
            
            <h2>Ko mēs piedāvājam?</h2>
            <ul>
                <li>Detalizētas mitoloģisko būtņu apraksti.</li>
                <li>Audzējošas un senas kultūras rituāli un stāsti.</li>
                <li>Interaktīvie resursi mītu izpētei.</li>
                <li>Forumi un platformas ideju apmaiņai.</li>
            </ul>

            <h2>Mūsu komanda</h2>
            <p>Mūsu komanda sastāv no pieredzējušiem pētniekiem, rakstniekiem un tīmekļa izstrādātājiem, kuri ir aizrautīgi par mitoloģiju un vēlas dalīties ar savām zināšanām ar pasauli. Mēs nepārtraukti strādājam pie mūsu satura un funkcionalitātes uzlabošanas.</p>

            <h2>Kontakti</h2>
            <p>Ja jums ir jautājumi vai ieteikumi, lūdzu, sazinieties ar mums:</p>
            <ul>
                <li>Epasts: info@mythologywebsite.com</li>
                <li>Telefons: +1 (234) 567-890</li>
                <li>Adrese: 123 Mythology Lane, Pilsēta, Valsts</li>
            </ul>
        </div>
    </section>

    
</div>

    <script src="script.js"></script>
</body>
</html> 