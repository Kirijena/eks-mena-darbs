<?php
session_start();
$current_page = 'index';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senās Teikas - Sākums</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="content">
        <section class="welcome">
            <h2>Laipni lūgti senajās leģendās</h2>
            <p>Izpētiet dažādu kultūru mītus, atklājiet stāstus un rodiet iedvesmu.</p>
            
            <?php if(isset($_SESSION['user'])): ?>
                <div class="user-welcome">
                    <p>Sveiciens, <?php echo htmlspecialchars($_SESSION['user']); ?>!</p>
                    <a href="logout.php" class="logout-btn">Iziet</a>
                </div>
            <?php endif; ?>
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