<?php
session_start();
require 'includes/connect_db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$stmt = $savienojums->prepare("SELECT username, name, lastname FROM eksamens_lietotajs WHERE lietotajs_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$current_page = 'account';
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mans Konts - Senās Teikas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>


    <main class="account-container">
        <div class="profile-section">
            <div class="profile-photo">
                <i class="fas fa-user"></i>
            </div>
            <h1 class="user-name"><?php echo htmlspecialchars($user['name'] . ' ' . $user['lastname']); ?></h1>
            <p class="user-username">@<?php echo htmlspecialchars($user['username']); ?></p>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Iziet no konta
            </a>
        </div>

        <div class="saved-entries">
            <h2>Jūsu saglabātie ieraksti</h2>
            <div class="entries-area">
                <p>Ieraksti nav atrasti</p>
            </div>
        </div>
    </main>


    <script src="js/script.js"></script>
</body>
</html> 