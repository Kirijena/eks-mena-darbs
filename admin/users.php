<?php
session_start();
require '../includes/connect_db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['delete_user'])) {
    $user_id = (int)$_POST['user_id'];
    $stmt = $savienojums->prepare("DELETE FROM eksamens_lietotajs WHERE lietotajs_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

$message = ''; // Для уведомлений на странице

// Проверяем права текущего пользователя
$current_admin_id = $_SESSION['admin_id'];
$check_stmt = $savienojums->prepare("SELECT tiesibas FROM eksamens_lietotajs WHERE lietotajs_id = ?");
$check_stmt->bind_param("i", $current_admin_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$current_user = $check_result->fetch_assoc();
$is_admin = ($current_user['tiesibas'] === 'administrators');

if (isset($_POST['update_user'])) {
    $user_id = (int)$_POST['user_id'];
    $approved = (int)$_POST['approved'];

    if ($is_admin) {
        $stmt = $savienojums->prepare("UPDATE eksamens_lietotajs SET approved = ? WHERE lietotajs_id = ?");
        $stmt->bind_param("ii", $approved, $user_id);
        $stmt->execute();
    } else {
        $message = 'Jums nav tiesību mainīt statusu!';
    }
}

if (isset($_POST['update_tiesibas'])) {
    $user_id = (int)$_POST['user_id'];
    $tiesibas = $_POST['tiesibas'];
    if (in_array($tiesibas, ['administrators', 'moderators', 'lietotajs'])) {
        if ($is_admin) {
            $stmt = $savienojums->prepare("UPDATE eksamens_lietotajs SET tiesibas = ? WHERE lietotajs_id = ?");
            $stmt->bind_param("si", $tiesibas, $user_id);
            $stmt->execute();
        } else {
            $message = 'Jums nav tiesību mainīt lomu!';
        }
    }
}

if (isset($_POST['create_user'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = trim($_POST['name']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $approved = isset($_POST['approved']) ? 1 : 0;
    $tiesibas = 'lietotajs'; // Новые пользователи по умолчанию с правами lietotajs
    $stmt = $savienojums->prepare("INSERT INTO eksamens_lietotajs (username, password, name, lastname, email, approved, tiesibas) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $username, $password, $name, $lastname, $email, $approved, $tiesibas);
    $stmt->execute();
}

if (isset($_POST['update_user_details'])) {
    $user_id = (int)$_POST['user_id'];
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $approved = isset($_POST['approved']) ? 1 : 0;

    $stmt = $savienojums->prepare("UPDATE eksamens_lietotajs SET username = ?, name = ?, lastname = ?, email = ?, approved = ? WHERE lietotajs_id = ?");
    $stmt->bind_param("ssssii", $username, $name, $lastname, $email, $approved, $user_id);
    $stmt->execute();
}

$result = $savienojums->query("SELECT * FROM eksamens_lietotajs ORDER BY lietotajs_id DESC");
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lietotāju Pārvaldība - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-users-cog"></i> Lietotāju Pārvaldība &nbsp</h1>
            <div class="admin-actions">
                <a href="ieraksts.php" class="admin-btn">Ieraksti</a>
                <a href="type.php" class="admin-btn">Type</a>
                <a href="kategoriju.php" class="admin-btn">Kategoriju</a>
                <button onclick="showCreateUserForm()" class="admin-btn">
                    <i class="fas fa-user-plus"></i> Jauns Lietotājs
                </button>
                <a href="logout.php" class="admin-btn logout">
                    <i class="fas fa-sign-out-alt"></i> Iziet
                </a>
            </div>
        </div>

        <div class="message" id="messageBox"><?= htmlspecialchars($message) ?></div>

        <div class="users-table-container">
            <table class="users-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Lietotājvārds</th>
                    <th>Vārds</th>
                    <th>Uzvārds</th>
                    <th>E-pasts</th>
                    <th>Statuss</th>
                    <th>Loma</th> 
                    <th>Darbības</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['lietotajs_id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['lastname']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <form method="POST" class="inline-form">
                                <input type="hidden" name="user_id" value="<?= $user['lietotajs_id'] ?>">
                                <select name="approved" onchange="this.form.submit()" class="status-select">
                                    <option value="0" <?= !$user['approved'] ? 'selected' : '' ?>>Neapstiprināts</option>
                                    <option value="1" <?= $user['approved'] ? 'selected' : '' ?>>Apstiprināts</option>
                                </select>
                                <input type="hidden" name="update_user" value="1">
                            </form>
                        </td>
                        <td>
                            <form method="POST" class="inline-form">
                                <input type="hidden" name="user_id" value="<?= $user['lietotajs_id'] ?>">
                                <select name="tiesibas" onchange="this.form.submit()" class="role-select">
                                    <option value="lietotajs" <?= $user['tiesibas'] == 'lietotajs' ? 'selected' : '' ?>>Lietotājs</option>
                                    <option value="moderators" <?= $user['tiesibas'] == 'moderators' ? 'selected' : '' ?>>Moderators</option>
                                    <option value="administrators" <?= $user['tiesibas'] == 'administrators' ? 'selected' : '' ?>>Administrators</option>
                                </select>
                                <input type="hidden" name="update_tiesibas" value="1">
                            </form>
                        </td>
                        <td class="action-buttons">
                            <button onclick="showEditUserForm(<?= htmlspecialchars(json_encode($user)) ?>)" class="edit-btn">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" class="inline-form" onsubmit="return confirm('Vai tiešām vēlaties dzēst šo lietotāju?')">
                                <input type="hidden" name="user_id" value="<?= $user['lietotajs_id'] ?>">
                                <button type="submit" name="delete_user" class="delete-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Модальное окно для добавления/редактирования пользователя -->
    <!-- (оставляем без изменений) -->

    <script>
        function showCreateUserForm() {
            document.getElementById('createUserModal').style.display = 'block';
        }

        function hideCreateUserModal() {
            document.getElementById('createUserModal').style.display = 'none';
        }

        function showEditUserForm(user) {
            document.getElementById('edit_user_id').value = user.lietotajs_id;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_lastname').value = user.lastname;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_approved').checked = user.approved == 1;
            document.getElementById('editUserModal').style.display = 'block';
        }

        function hideEditUserModal() {
            document.getElementById('editUserModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('createUserModal') || event.target == document.getElementById('editUserModal')) {
                hideCreateUserModal();
                hideEditUserModal();
            }
        }

        // Показать сообщение, если оно есть, и скрыть его через 3 секунды
        window.onload = function() {
            var msg = document.getElementById('messageBox');
            if (msg.textContent.trim() !== '') {
                msg.style.display = 'block';
                setTimeout(function() {
                    msg.style.display = 'none';
                }, 3000);
            }
        }
    </script>
</body>
</html>
