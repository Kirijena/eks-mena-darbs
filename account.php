<?php
session_start();



$current_page = 'account';
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mans Konts - Senās Teikas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="account-container">
        <div class="account-content">
            <h1><i class="fas fa-user-circle"></i> Mans Konts</h1>
            <div class="account-info">
                <h2>Profila Informācija</h2>
                <div class="info-section">
                    <p><strong>Lietotājvārds:</strong> <?php echo htmlspecialchars($_SESSION['user']); ?></p>
                    <!-- Additional account information will go here -->
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html> 