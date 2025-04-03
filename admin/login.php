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
        $stmt = $savienojums->prepare("SELECT lietotajs_id, username, password FROM eksamens_lietotajs WHERE username = ? AND approved = 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user["password"])) {
                $_SESSION["admin_id"] = $user["lietotajs_id"];
                $_SESSION["admin_username"] = $user["username"];
                header("Location: users.php");
                exit();
            } else {
                $error = "Nepareizs lietotājvārds vai parole.";
            }
        } else {
            $error = "Nav atrasts apstiprināts lietotājs ar šādu lietotājvārdu.";
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
    <div class="admin-login-container">
        <div class="admin-login-box">
            <div class="admin-header">
                <i class="fas fa-user-shield fa-3x"></i>
                <h1>Admin Panel</h1>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form class="admin-login-form" method="POST">
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
                <button type="submit" class="admin-submit-btn">
                    <i class="fas fa-sign-in-alt"></i> Pieteikties
                </button>
            </form>
            
            <a href="../index.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Atpakaļ uz mājas lapu
            </a>
        </div>
    </div>
</body>
</html>