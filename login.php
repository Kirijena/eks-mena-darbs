<?php
session_start();
require 'includes/connect_db.php'; // Подключаем файл с подключением к БД

$activeForm = (isset($_POST['register'])) ? 'register' : 'login';

if (isset($_SESSION['user_id'])) {
    header('Location: account.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (!empty($username) && !empty($password)) {
        // Подготавливаем запрос к таблице lietotajs
        $stmt = $savienojums->prepare("SELECT lietotajs_id, username, password, approved FROM eksamens_lietotajs WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if ($user["approved"] == 1) { // Проверяем, одобрен ли пользователь
                if (password_verify($password, $user["password"])) {
                    $_SESSION["user_id"] = $user["lietotajs_id"];
                    $_SESSION["username"] = $user["username"];
                    header("Location: account.php"); // Перенаправляем в account.php
                    exit();
                } else {
                    $error = "Nepareizs lietotājvārds vai parole.";
                }
            } else {
                $error = "Jūsu konts vēl nav apstiprināts.";
            }
        } else {
            $error = "Nepareizs  vai parole.";
        }

        $stmt->close();
    } else {
        $error = "Lūdzu, aizpildiet visus laukus.";
    }
}

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $name = trim($_POST['name']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);

    // Validation
    if (empty($username) || empty($password) || empty($confirm_password) || empty($name) || empty($lastname) || empty($email)) {
        $error = "Visiem laukiem jābūt aizpildītiem";
    } elseif ($password !== $confirm_password) {
        $error = "Paroles nesakrīt";
    } elseif (strlen($password) < 8) {
        $error = "Parolei jābūt vismaz 8 rakstzīmes garai";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Nederīga e-pasta adrese";
    } else {
        // Check if username already exists
        $stmt = $savienojums->prepare("SELECT lietotajs_id FROM eksamens_lietotajs WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Lietotājvārds jau eksistē";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $savienojums->prepare("INSERT INTO eksamens_lietotajs (username, password, name, lastname, email) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $hashed_password, $name, $lastname, $email);
            
            if ($stmt->execute()) {
                // Get the new user's ID
                $new_user_id = $savienojums->insert_id;
                
                // Automatically log in the user
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['username'] = $username;
                
                // Redirect to account page
                header("Location: account.php");
                exit();
            } else {
                $error = "Kļūda reģistrācijas laikā. Lūdzu, mēģiniet vēlreiz.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senās Teikas - Ieej Valstībā</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <div class="logo">
                    <i class="fas fa-scroll fa-3x"></i>
                </div>
                <h1>Senās Teikas</h1>
                <p>Izpēti seno mītu valstību</p>
            </div>

            <div class="auth-tabs">
                <button class="tab-btn <?php echo $activeForm === 'login' ? 'active' : ''; ?>" data-tab="login">
                    <i class="fas fa-sign-in-alt"></i> Pieteikties
                </button>
                <button class="tab-btn <?php echo $activeForm === 'register' ? 'active' : ''; ?>" data-tab="register">
                    <i class="fas fa-user-plus"></i> Reģistrēties
                </button>
            </div>

            <div class="auth-content">
                <?php if (isset($error)): ?>
                    <p class="error-message"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                <?php if ($success): ?>
                    <p class="success-message"><?= htmlspecialchars($success) ?></p>
                <?php endif; ?>

                <form id="loginForm" class="auth-form <?php echo $activeForm === 'login' ? 'active' : ''; ?>" action="login.php" method="POST">
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

                <form id="registerForm" class="auth-form <?php echo $activeForm === 'register' ? 'active' : ''; ?>" method="POST">
                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" name="username" placeholder="Lietotājvārds" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" name="name" placeholder="Vārds" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" name="lastname" placeholder="Uzvārds" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" placeholder="E-pasts" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" placeholder="Parole" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="confirm_password" placeholder="Apstiprināt paroli" required>
                        </div>
                    </div>
                   
                    <button type="submit" name="register" class="submit-btn">
                        <i class="fas fa-book-open"></i> Sāc savu ceļojumu
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
