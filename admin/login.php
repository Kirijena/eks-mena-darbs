<?php
session_start();
require '../includes/connect_db.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: users.php');
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (!empty($username) && !empty($password)) {
        $stmt = $savienojums->prepare("SELECT lietotajs_id, username, password, tiesibas FROM eksamens_lietotajs WHERE username = ? AND approved = 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user["password"])) {
                // Проверяем права пользователя
                if ($user["tiesibas"] === 'administrators' || $user["tiesibas"] === 'moderators') {
                    $_SESSION["admin_id"] = $user["lietotajs_id"];
                    $_SESSION["admin_username"] = $user["username"];
                    header("Location: users.php");
                    exit();
                } else {
                    $error = "Nepietiekamas tiesības.";
                }
            } else {
                $error = "Nepareizs lietotājvārds vai parole.";
            }
        } else {
            $error = "Nav atrasts apstiprināts lietotājs ar šādu datus.";
        }
        $stmt->close();
    } else {
        $error = "Lūdzu, aizpildiet visus laukus.";
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Senās Teikas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <div class="logo">
                    <i class="fas fa-scroll fa-3x"></i>
                </div>
                <h1>Admin daļa</h1>
            </div>

            <div class="auth-content">
                <?php if (isset($error)): ?>
                    <p class="error-message"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <form id="loginForm" class="auth-form" action="login.php" method="POST">
                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" name="username" placeholder="Lietotājvārds" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" placeholder="Parole" required>
                        </div>
                    </div>
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-door-open"></i> Ieej Valstībā
                    </button>
                </form>
                
                <button onclick="history.back()" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Atpakaļ
                </button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script src="js/auth.js"></script>
</body>
</html>
