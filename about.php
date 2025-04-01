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

    <main class="content">
        <section class="about-section">
            <h2>Par Mūsu Vietni</h2>
            <div class="about-content">
                <p>Senās Teikas ir vietne, kas veltīta pasaules mitoloģiju un seno stāstu izpētei un saglabāšanai. Mūsu mērķis ir apkopot un dalīties ar bagātīgo kultūras mantojumu, ko mums atstājušas dažādas civilizācijas.</p>
                
                <h3>Mūsu Misija</h3>
                <p>Mēs strādājam, lai:</p>
                <ul>
                    <li>Saglabātu un popularizētu pasaules tautu mitoloģisko mantojumu</li>
                    <li>Radītu pieejamu un interaktīvu platformu mitoloģijas izpētei</li>
                    <li>Veidotu tiltu starp pagātni un tagadni caur senajiem stāstiem</li>
                </ul>

                <?php if(isset($_SESSION['user'])): ?>
                    <div class="admin-info">
                        <h3>Administratora Informācija</h3>
                        <p>Piesakieties kā <?php echo htmlspecialchars($_SESSION['user']); ?></p>
                        <p>Pēdējā apmeklējuma laiks: <?php echo date('d.m.Y H:i'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <div id="authModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <?php include 'includes/login-form.php'; ?>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html> 