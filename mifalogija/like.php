<?php
session_start();
require 'includes/connect_db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['entry_id'])) {
    echo json_encode(['status' => 'error']);
    exit;
}

$user_id = $_SESSION['user_id'];
$entry_id = (int) $_POST['entry_id'];

// Проверим, уже ли лайкнут
$stmt = $savienojums->prepare("SELECT * FROM eksamens_likes WHERE user_id = ? AND entry_id = ?");
$stmt->bind_param("ii", $user_id, $entry_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Удалить лайк
    $del = $savienojums->prepare("DELETE FROM eksamens_likes WHERE user_id = ? AND entry_id = ?");
    $del->bind_param("ii", $user_id, $entry_id);
    $del->execute();
    echo json_encode(['status' => 'unliked']);
} else {
    // Добавить лайк
    $ins = $savienojums->prepare("INSERT INTO eksamens_likes (user_id, entry_id) VALUES (?, ?)");
    $ins->bind_param("ii", $user_id, $entry_id);
    $ins->execute();
    echo json_encode(['status' => 'liked']);
}
?>
