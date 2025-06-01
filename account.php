<?php
session_start();
require 'includes/connect_db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $savienojums->prepare("SELECT username, name, lastname FROM eksamens_lietotajs WHERE lietotajs_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$current_page = 'account';

// Apstrāde: noņemt patīk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_like_entry_id'])) {
    $entry_id = (int)$_POST['remove_like_entry_id'];
    $remove_stmt = $savienojums->prepare("DELETE FROM liked_entries WHERE user_id = ? AND entry_id = ?");
    $remove_stmt->bind_param("ii", $user_id, $entry_id);
    $remove_stmt->execute();
    header("Location: account.php");
    exit();
}

// Iegūstam lietotāja saglabātos ierakstus
$liked_stmt = $savienojums->prepare("SELECT e.id, e.title FROM eksamens_entries e JOIN liked_entries l ON e.id = l.entry_id WHERE l.user_id = ?");
$liked_stmt->bind_param("i", $user_id);
$liked_stmt->execute();
$liked_result = $liked_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mans Konts - Senās Teikas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
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
            <a class="back-link" onclick="history.back()">← Atpakaļ</a>
        </a>
    </div>

    <div class="saved-entries">
        <h2>Jūsu saglabātie ieraksti</h2>
        <div class="entries-area">
            <?php if ($liked_result->num_rows > 0): ?>
                <ul>
                    <?php while ($entry = $liked_result->fetch_assoc()): ?>
                        <li style="margin-bottom: 10px;">
                            <a href="mifalogija/entry.php?id=<?php echo $entry['id']; ?>">
                                <?php echo htmlspecialchars($entry['title']); ?>
                            </a>
                            <form method="post" style="display:inline-block; margin-left: 10px;">
                                <input type="hidden" name="remove_like_entry_id" value="<?php echo $entry['id']; ?>">
                                <button type="submit" style="color: red; border: none; background: none; cursor: pointer;">✖</button>
                            </form>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Ieraksti nav atrasti</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="js/script.js"></script>
</body>
</html>
