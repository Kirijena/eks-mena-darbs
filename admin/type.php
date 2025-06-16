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
    $stmt = $savienojums->prepare("DELETE FROM eksamens_categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    
    if ($stmt->execute()) {
        $message = 'Datu tips ir veiksmīgi izdzēsts!';
        $message_type = 'success';
    } else {
        $message = 'Datu tipu nevar dzēst!';
        $message_type = 'error';
    }
}

// Create Category
if (isset($_POST['create_category'])) {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $stmt = $savienojums->prepare("INSERT INTO eksamens_categories (Nosaukums) VALUES (?)");
        $stmt->bind_param("s", $name);
        
        if ($stmt->execute()) {
            $message = 'Izveidots jauns datu tips!';
            $message_type = 'success';
        } else {
            $message = 'Nevar izveidot jaunu datu tipu!';
            $message_type = 'error';
        }
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
        $stmt = $savienojums->prepare("UPDATE eksamens_categories SET Nosaukums = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $category_id);
        
        if ($stmt->execute()) {
            $message = 'Tipu dati veiksmīgi atjaunināti!';
            $message_type = 'success';
        } else {
            $message = 'Nav iespējas atjaunināt datu tipu!';
            $message_type = 'error';
        }
    } else {
        $message = 'Nosaukums nedrīkst būt tukšs!';
        $message_type = 'error';
    }
}

// Get all categories
$result = $savienojums->query("SELECT * FROM eksamens_categories ORDER BY id DESC");
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
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-folder"></i> Type Pārvaldība &nbsp</h1>
            <div class="admin-actions">
                <a href="ieraksts.php" class="admin-btn">Ieraksti</a>
                <a href="users.php" class="admin-btn">Lietotāji</a>
                <a href="kategoriju.php" class="admin-btn">Kategoriju</a>
                <button onclick="showCreateCategoryForm()" class="admin-btn">
                    <i class="fas fa-plus"></i> Jauna Kategorija
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
                        <th>Nosaukums</th>
                        <th>Darbības</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($category = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($category['id']) ?></td>
                        <td><?= htmlspecialchars($category['Nosaukums']) ?></td>
                        <td class="action-buttons">
                            <button onclick='showEditCategoryForm(<?= json_encode($category) ?>)' class="edit-btn">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" class="inline-form" onsubmit="return confirm('Vai tiešām vēlaties dzēst šo kategoriju?')">
                                <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
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
                    <input type="text" name="name" placeholder="Nosaukums" required>
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
                    <input type="text" name="name" id="edit_name" placeholder="Nosaukums" required>
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
        document.getElementById('edit_category_id').value = category.id;
        document.getElementById('edit_name').value = category.Nosaukums;
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