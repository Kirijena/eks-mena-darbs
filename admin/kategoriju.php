<?php
session_start();
require '../includes/connect_db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Initialize message variables
$message = '';
$message_type = '';

// Delete Category
if (isset($_POST['delete_category'])) {
    $category_id = (int)$_POST['category_id'];
    $stmt = $savienojums->prepare("DELETE FROM eksamens_kategorija WHERE id_kat = ?");
    $stmt->bind_param("i", $category_id);

    if ($stmt->execute()) {
        $message = 'Datu kategorija ir veiksmīgi izdzēsta!';
        $message_type = 'success';
    } else {
        $message = 'Datu kategoriju nevar izdzēst!';
        $message_type = 'error';
    }
    $stmt->close();
}

// Create Category
if (isset($_POST['create_category'])) {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $stmt = $savienojums->prepare("INSERT INTO eksamens_kategorija (Kategorija) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            $message = 'Izveidota jauna datu kategorija!';
            $message_type = 'success';
        } else {
            $message = 'Nevar izveidot jaunu datu kategoriju!';
            $message_type = 'error';
        }
        $stmt->close();
    } else {
        $message = 'Nosaukums nedrīkst būt tukšs!';
        $message_type = 'error';
    }
}

// Update Category
if (isset($_POST['update_category'])) {
    $category_id = (int)$_POST['category_id'];
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $stmt = $savienojums->prepare("UPDATE eksamens_kategorija SET Kategorija = ? WHERE id_kat = ?");
        $stmt->bind_param("si", $name, $category_id);
        if ($stmt->execute()) {
            $message = 'Kategorijas dati veiksmīgi atjaunināti!';
            $message_type = 'success';
        } else {
            $message = 'Nav iespējas atjaunināt datu kategoriju!';
            $message_type = 'error';
        }
        $stmt->close();
    } else {
        $message = 'Nosaukums nedrīkst būt tukšs!';
        $message_type = 'error';
    }
}

// Get all categories
$result = $savienojums->query("SELECT * FROM eksamens_kategorija ORDER BY id_kat DESC");
?>


<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategoriju Pārvaldība - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-weight: bold;
            display: none;
            animation: slideDown 0.3s ease-out;
        }
        
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-left: 5px solid #28a745;
        }
        
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-left: 5px solid #dc3545;
        }
        
        .message.success::before {
            content: "\f00c";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            margin-right: 10px;
            color: #28a745;
        }
        
        .message.error::before {
            content: "\f071";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            margin-right: 10px;
            color: #dc3545;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-book"></i> Kategoriju Pārvaldība &nbsp</h1>
            <div class="admin-actions">
                <a href="users.php" class="admin-btn">Lietotāji</a>
                <a href="ieraksts.php" class="admin-btn">Ieraksti</a>
                <a href="type.php" class="admin-btn">Type</a>
                <button onclick="showCreateCategoryForm()" class="admin-btn">
                    <i class="fas fa-plus"></i> Jauns Ieraksts
                </button>
                <a href="logout.php" class="admin-btn logout">
                    <i class="fas fa-sign-out-alt"></i> Iziet
                </a>
            </div>
        </div>

        <?php if (!empty($message)): ?>
        <div class="message <?= htmlspecialchars($message_type) ?>" id="messageBox"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="users-table-container">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kategorija</th>
                        <th>Darbības</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($category = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($category['id_kat']) ?></td>
                        <td><?= htmlspecialchars($category['Kategorija']) ?></td>
                        <td class="action-buttons">
                            <button onclick='showEditCategoryForm(<?= json_encode($category) ?>)' class="edit-btn">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" class="inline-form" onsubmit="return confirm('Vai tiešām vēlaties dzēst šo kategoriju?')">
                                <input type="hidden" name="category_id" value="<?= $category['id_kat'] ?>">
                                <button type="submit" name="delete_category" class="delete-btn">
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

    <!-- Create Category Modal -->
    <div id="createCategoryModal" class="modal">
        <div class="modal-content">
            <h2><i class="fas fa-plus"></i> Jauna Kategorija</h2>
            <form method="POST" class="create-category-form">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Kategorija" required>
                </div>
                <div class="form-actions">
                    <button type="submit" name="create_category" class="submit-btn">
                        <i class="fas fa-save"></i> Saglabāt
                    </button>
                    <button type="button" onclick="hideCreateCategoryModal()" class="cancel-btn">
                        <i class="fas fa-times"></i> Atcelt
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="modal">
        <div class="modal-content">
            <h2><i class="fas fa-edit"></i> Rediģēt Kategoriju</h2>
            <form method="POST" class="edit-category-form">
                <input type="hidden" name="category_id" id="edit_category_id">
                <div class="form-group">
                    <input type="text" name="name" id="edit_name" placeholder="Kategorija" required>
                </div>
                <div class="form-actions">
                    <button type="submit" name="update_category" class="submit-btn">
                        <i class="fas fa-save"></i> Saglabāt
                    </button>
                    <button type="button" onclick="hideEditCategoryModal()" class="cancel-btn">
                        <i class="fas fa-times"></i> Atcelt
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function showCreateCategoryForm() {
        document.getElementById('createCategoryModal').style.display = 'block';
    }

    function hideCreateCategoryModal() {
        document.getElementById('createCategoryModal').style.display = 'none';
    }

    function showEditCategoryForm(category) {
        document.getElementById('edit_category_id').value = category.id_kat;
        document.getElementById('edit_name').value = category.Kategorija;
        // Нет поля description, поэтому не устанавливаем его
        document.getElementById('editCategoryModal').style.display = 'block';
    }

    function hideEditCategoryModal() {
        document.getElementById('editCategoryModal').style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('createCategoryModal') || event.target == document.getElementById('editCategoryModal')) {
            hideCreateCategoryModal();
            hideEditCategoryModal();
        }
    }

    window.onload = function() {
        var msg = document.getElementById('messageBox');
        if (msg && msg.textContent.trim() !== '') {
            msg.style.display = 'block';
            setTimeout(function() {
                msg.style.display = 'none';
            }, 3000);
        }
    }
    </script>
</body>
</html>
