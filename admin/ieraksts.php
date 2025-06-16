<?php
session_start();
require '../includes/connect_db.php';

// Set charset to UTF-8 for proper Latvian character handling
mysqli_set_charset($savienojums, "utf8");

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['delete_record'])) {
    $record_id = (int)$_POST['record_id'];
    $stmt = $savienojums->prepare("DELETE FROM eksamens_entries WHERE id = ?");
    $stmt->bind_param("i", $record_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['create_record'])) {
    $type_id = (int)$_POST['type_id'];
    $category_id = (int)$_POST['category_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $country = trim($_POST['country']);
    $first_mention_date = trim($_POST['first_mention_date']);
    $description_text = trim($_POST['description_text']);
    $published = isset($_POST['published']) ? 1 : 0;
    
    $image_data = null;
    $upload_error = false;
    $upload_message = "";
    
    if (isset($_FILES['images']) && $_FILES['images']['error'] === UPLOAD_ERR_OK) {
        $max_size = 16 * 1024 * 1024;
        if ($_FILES['images']['size'] > $max_size) {
            $upload_error = true;
            $upload_message = "Attēls ir pārāk liels. Maksimālais izmērs: 16MB";
        }
        
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['images']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $upload_error = true;
            $upload_message = "Neatbalstīts faila tips. Atļautie tipi: JPEG, PNG, GIF, WebP";
        }
        
        $image_info = getimagesize($_FILES['images']['tmp_name']);
        if ($image_info === false) {
            $upload_error = true;
            $upload_message = "Fails nav derīgs attēls";
        }
        
        if (!$upload_error) {
            $image_data = file_get_contents($_FILES['images']['tmp_name']);
            if ($image_data === false) {
                $upload_error = true;
                $upload_message = "Kļūda attēla nolasīšanā";
            }
        }
    } elseif (isset($_FILES['images']) && $_FILES['images']['error'] !== UPLOAD_ERR_NO_FILE) {
        switch ($_FILES['images']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $upload_message = "Attēls ir pārāk liels";
                break;
            case UPLOAD_ERR_PARTIAL:
                $upload_message = "Attēls tika augšupielādēts daļēji";
                break;
            default:
                $upload_message = "Kļūda attēla augšupielādē";
        }
        $upload_error = true;
    }

    $stmt = $savienojums->prepare("INSERT INTO eksamens_entries (type_id, category_id, title, description, country, first_mention_date, description_text, images, published, created_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iissssssi", $type_id, $category_id, $title, $description, $country, $first_mention_date, $description_text, $image_data, $published);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['success_message'] = "Ieraksts veiksmīgi izveidots!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['update_record'])) {
    $record_id = (int)$_POST['record_id'];
    $type_id = (int)$_POST['type_id'];
    $category_id = (int)$_POST['category_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $country = trim($_POST['country']);
    $first_mention_date = trim($_POST['first_mention_date']);
    $description_text = trim($_POST['description_text']);
    $published = isset($_POST['published']) ? 1 : 0;
    
    $image_data = null;
    if (isset($_FILES['images']) && $_FILES['images']['error'] === UPLOAD_ERR_OK) {
        $max_size = 16 * 1024 * 1024;
        if ($_FILES['images']['size'] <= $max_size) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['images']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                $image_info = getimagesize($_FILES['images']['tmp_name']);
                if ($image_info !== false) {
                    $image_data = file_get_contents($_FILES['images']['tmp_name']);
                }
            }
        }
    }
    
    if ($image_data) {
        $stmt = $savienojums->prepare("UPDATE eksamens_entries SET 
            type_id = ?, category_id = ?, title = ?, description = ?, 
            country = ?, first_mention_date = ?, description_text = ?, 
            images = ?, published = ? 
            WHERE id = ?");
        $stmt->bind_param("iissssssii", $type_id, $category_id, $title, $description, 
            $country, $first_mention_date, $description_text, $image_data, $published, $record_id);
    } else {
        $stmt = $savienojums->prepare("UPDATE eksamens_entries SET 
            type_id = ?, category_id = ?, title = ?, description = ?, 
            country = ?, first_mention_date = ?, description_text = ?, 
            published = ? 
            WHERE id = ?");
        $stmt->bind_param("iisssssii", $type_id, $category_id, $title, $description, 
            $country, $first_mention_date, $description_text, $published, $record_id);
    }
    
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['success_message'] = "Ieraksts veiksmīgi atjaunināts!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$result = $savienojums->query("
    SELECT e.*, 
           c.Nosaukums as category_name,
           k.Kategorija as category_id_name
    FROM eksamens_entries e 
    LEFT JOIN eksamens_categories c ON e.type_id = c.id 
    LEFT JOIN eksamens_kategorija k ON e.category_id = k.id_kat
    ORDER BY e.id DESC
");

$categories_result = $savienojums->query("SELECT id, Nosaukums FROM eksamens_categories ORDER BY Nosaukums");
$categories = [];
while ($cat = $categories_result->fetch_assoc()) {
    $categories[] = $cat;
}

$category_ids_result = $savienojums->query("SELECT id_kat, Kategorija FROM eksamens_kategorija ORDER BY Kategorija");
$category_ids = [];
while ($cat_id = $category_ids_result->fetch_assoc()) {
    $category_ids[] = $cat_id;
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mītoloģijas Ierakstu Pārvaldība</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .expanded-image {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .expanded-image-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
        }
        
        .expanded-image-content img {
            max-width: 100%;
            max-height: 80vh;
            display: block;
            margin: 0 auto;
        }
        
        .close-expanded-image {
            position: absolute;
            top: -40px;
            right: 0;
            color: white;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .expanded-image-info {
            color: white;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['success_message'])): ?>
    <div class="success-message" id="successMessage"><?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-book"></i> Mītoloģijas Ierakstu Pārvaldība</h1>
            <div class="admin-actions">
                <a href="users.php" class="admin-btn">Lietotāji</a>
                <a href="type.php" class="admin-btn">Type</a>
                <a href="kategoriju.php" class="admin-btn">Kategoriju</a>
                <button onclick="showCreateRecordForm()" class="new-record-btn">
                    <i class="fas fa-plus"></i> Jauns Ieraksts
                </button>
                <a href="logout.php" class="admin-btn logout">
                    <i class="fas fa-sign-out-alt"></i> Iziet
                </a>
            </div>
        </div>

        <div class="users-table-container">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Kategorija</th>
                        <th>Nosaukums</th>
                        <th>Apraksts</th>
                        <th>Valsts</th>
                        <th>Pirmā pieminējuma datums</th>
                        <th>Detalizēts apraksts</th>
                        <th>Attēli</th>
                        <th>Publicēts</th>
                        <th>Izveidots</th>
                        <th>Darbības</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($record = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['id']) ?></td>
                        <td>
                            <span class="category-name"><?= htmlspecialchars($record['category_name'] ?? 'Nav atrasts') ?></span>
                        </td>
                        <td>
                            <span class="category-id-name"><?= htmlspecialchars($record['category_id_name'] ?? 'Nav atrasts') ?></span>
                        </td>
                        <td class="clickable-text" onclick="showExpandedText('<?= htmlspecialchars($record['title']) ?>')"><?= htmlspecialchars($record['title'] ?? '') ?></td>
                        <td class="clickable-text" onclick="showExpandedText('<?= htmlspecialchars($record['description']) ?>')"><?= htmlspecialchars(substr($record['description'], 0, 50)) ?>...</td>
                        <td class="clickable-text" onclick="showExpandedText('<?= htmlspecialchars($record['country']) ?>')"><?= htmlspecialchars($record['country']) ?></td>
                        <td class="clickable-text" onclick="showExpandedText('<?= htmlspecialchars($record['first_mention_date']) ?>')"><?= htmlspecialchars($record['first_mention_date']) ?></td>
                        <td class="clickable-text" onclick="showExpandedText('<?= htmlspecialchars($record['description_text']) ?>')"><?= htmlspecialchars(substr($record['description_text'], 0, 50)) ?>...</td>
                        <td>
                            <?php if (!empty($record['images'])): ?>
                                <button onclick="showExpandedImage('data:image/jpeg;base64,<?= base64_encode($record['images']) ?>')" class="image-btn">
                                    <i class="fas fa-image"></i> Skatīt attēlu
                                </button>
                            <?php else: ?>
                                <span class="no-image">Nav attēla</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $record['published'] ? 'Jā' : 'Nē' ?></td>
                        <td><?= htmlspecialchars($record['created_date']) ?></td>
                        <td class="action-buttons">
                            <button onclick='showEditRecordForm(<?= json_encode($record) ?>)' class="edit-btn">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" class="inline-form" onsubmit="return confirm('Vai tiešām vēlaties dzēst šo ierakstu?')">
                                <input type="hidden" name="record_id" value="<?= $record['id'] ?>">
                                <button type="submit" name="delete_record" class="delete-btn">
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

    <!-- Expanded Text Viewer -->
    <div id="expandedTextView" class="expanded-text" style="display: none;" onclick="hideExpandedText()">
        <div class="expanded-text-content" onclick="event.stopPropagation()">
            <span class="close-expanded-text" onclick="hideExpandedText()">&times;</span>
            <div id="expandedTextContent"></div>
        </div>
    </div>

    <!-- Expanded Image Viewer -->
    <div id="expandedImageView" class="expanded-image" onclick="hideExpandedImage()">
        <div class="expanded-image-content" onclick="event.stopPropagation()">
            <span class="close-expanded-image" onclick="hideExpandedImage()">&times;</span>
            <img id="expandedImageContent" src="" alt="Mītoloģijas attēls">
            <div class="expanded-image-info">
            </div>
        </div>
    </div>

    <!-- Create Record Modal -->
    <div id="createRecordModal" class="modal">
        <div class="modal-content">
            <h2>Jauns Ieraksts</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="type_id">Tips:</label>
                    <select name="type_id" id="type_id" required>
                        <option value="">-- Izvēlies tipu --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['Nosaukums']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="category_id">Kategorija:</label>
                    <select name="category_id" id="category_id" required>
                        <option value="">-- Izvēlies kategoriju --</option>
                        <?php foreach ($category_ids as $cat_id): ?>
                            <option value="<?= $cat_id['id_kat'] ?>"><?= htmlspecialchars($cat_id['Kategorija']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title">Nosaukums:</label>
                    <input type="text" name="title" id="title" required>
                </div>

                <div class="form-group">
                    <label for="description">Apraksts:</label>
                    <textarea name="description" id="description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="country">Valsts:</label>
                    <input type="text" name="country" id="country">
                </div>

                <div class="form-group">
                    <label for="first_mention_date">Pirmā pieminēšana:</label>
                    <input type="text" name="first_mention_date" id="first_mention_date">
                </div>

                <div class="form-group">
                    <label for="description_text">Detalizēts apraksts:</label>
                    <textarea name="description_text" id="description_text" rows="5"></textarea>
                </div>

                <div class="form-group">
                    <label for="images">Attēls</label>
                    <input type="file" name="images" id="imageInput" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" onchange="previewImage(this)">
                    <div class="file-info">
                        Maksimālais faila izmērs: 16MB<br>
                        Atbalstītie formāti: JPEG, PNG, GIF, WebP
                    </div>
                    <img id="imagePreview" class="file-preview" alt="Attēla priekšskatījums">
                </div>

                <div class="form-group checkbox-group">
                    <label for="published">Publicēt:</label>
                    <input type="checkbox" name="published" id="published" checked>
                </div>

                <button type="submit" name="create_record" class="submit-btn">Saglabāt</button>
                <button type="button" class="cancel-btn" onclick="closeCreateRecordForm()">Atcelt</button>
            </form>
        </div>
    </div>

    <!-- Edit Record Modal -->
    <div id="editRecordModal" class="modal">
        <div class="modal-content">
            <h2>Rediģēt Ierakstu</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="record_id" id="edit_record_id">
                <input type="hidden" name="current_image" id="edit_current_image">
                
                <div class="form-group">
                    <label for="edit_type_id">Tips:</label>
                    <select name="type_id" id="edit_type_id" required>
                        <option value="">-- Izvēlies tipu --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['Nosaukums']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_category_id">Kategorija:</label>
                    <select name="category_id" id="edit_category_id" required>
                        <option value="">-- Izvēlies kategoriju --</option>
                        <?php foreach ($category_ids as $cat_id): ?>
                            <option value="<?= $cat_id['id_kat'] ?>"><?= htmlspecialchars($cat_id['Kategorija']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_title">Nosaukums:</label>
                    <input type="text" name="title" id="edit_title" required>
                </div>

                <div class="form-group">
                    <label for="edit_description">Apraksts:</label>
                    <textarea name="description" id="edit_description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="edit_country">Valsts:</label>
                    <input type="text" name="country" id="edit_country">
                </div>

                <div class="form-group">
                    <label for="edit_first_mention_date">Pirmā pieminēšana:</label>
                    <input type="text" name="first_mention_date" id="edit_first_mention_date">
                </div>

                <div class="form-group">
                    <label for="edit_description_text">Detalizēts apraksts:</label>
                    <textarea name="description_text" id="edit_description_text" rows="5"></textarea>
                </div>

                <div class="form-group">
                    <label for="edit_images">Attēls</label>
                    <input type="file" name="images" id="edit_imageInput" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" onchange="previewEditImage(this)">
                    <div class="file-info">
                        Maksimālais faila izmērs: 16MB<br>
                        Atbalstītie formāti: JPEG, PNG, GIF, WebP
                    </div>
                    <img id="edit_imagePreview" class="file-preview" alt="Attēla priekšskatījums">
                </div>

                <div class="form-group checkbox-group">
                    <label for="edit_published">Publicēt:</label>
                    <input type="checkbox" name="published" id="edit_published">
                </div>

                <button type="submit" name="update_record" class="submit-btn">Saglabāt izmaiņas</button>
                <button type="button" class="cancel-btn" onclick="closeEditRecordForm()">Atcelt</button>
            </form>
        </div>
    </div>

    <script>
    // Expanded text functions
    function showExpandedText(text) {
        const container = document.getElementById('expandedTextView');
        const content = document.getElementById('expandedTextContent');
        
        content.textContent = text;
        container.style.display = 'flex';
    }

    function hideExpandedText() {
        document.getElementById('expandedTextView').style.display = 'none';
    }

    // Expanded image functions
    function showExpandedImage(imageSrc) {
        const container = document.getElementById('expandedImageView');
        const img = document.getElementById('expandedImageContent');
        
        img.src = imageSrc;
        container.style.display = 'flex';
    }

    function hideExpandedImage() {
        document.getElementById('expandedImageView').style.display = 'none';
    }

    // Record form functions
    function showCreateRecordForm() {
        document.getElementById('createRecordModal').style.display = 'block';
    }

    function closeCreateRecordForm() {
        document.getElementById('createRecordModal').style.display = 'none';
    }

    function showEditRecordForm(record) {
        const modal = document.getElementById('editRecordModal');
        
        document.getElementById('edit_record_id').value = record.id;
        document.getElementById('edit_current_image').value = record.images || '';
        document.getElementById('edit_type_id').value = record.type_id || '';
        document.getElementById('edit_category_id').value = record.category_id || '';
        document.getElementById('edit_title').value = record.title || '';
        document.getElementById('edit_description').value = record.description || '';
        document.getElementById('edit_country').value = record.country || '';
        document.getElementById('edit_first_mention_date').value = record.first_mention_date || '';
        document.getElementById('edit_description_text').value = record.description_text || '';
        document.getElementById('edit_published').checked = record.published == 1;
        
        const preview = document.getElementById('edit_imagePreview');
        if (record.images) {
            if (record.images.startsWith('data:image')) {
                preview.src = record.images;
                preview.style.display = 'block';
            } else {
                preview.src = 'data:image/jpeg;base64,' + btoa(record.images);
                preview.style.display = 'block';
            }
        } else {
            preview.style.display = 'none';
        }
        
        modal.style.display = 'block';
    }

    function closeEditRecordForm() {
        document.getElementById('editRecordModal').style.display = 'none';
    }

    // Image preview functions
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            const maxSize = 16 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('Attēls ir pārāk liels! Maksimālais izmērs: 16MB');
                input.value = '';
                preview.style.display = 'none';
                return;
            }
            
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Neatbalstīts faila tips! Atļautie tipi: JPEG, PNG, GIF, WebP');
                input.value = '';
                preview.style.display = 'none';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    }

    function previewEditImage(input) {
        const preview = document.getElementById('edit_imagePreview');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            const maxSize = 16 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('Attēls ir pārāk liels! Maksimālais izmērs: 16MB');
                input.value = '';
                preview.style.display = 'none';
                return;
            }
            
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Neatbalstīts faila tips! Atļautie tipi: JPEG, PNG, GIF, WebP');
                input.value = '';
                preview.style.display = 'none';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    }

    // Global click handler
    window.onclick = function(event) {
        const createModal = document.getElementById('createRecordModal');
        const editModal = document.getElementById('editRecordModal');
        const expandedText = document.getElementById('expandedTextView');
        const expandedImage = document.getElementById('expandedImageView');
        
        if (event.target == createModal) {
            createModal.style.display = 'none';
        }
        if (event.target == editModal) {
            editModal.style.display = 'none';
        }
        if (event.target == expandedText) {
            hideExpandedText();
        }
        if (event.target == expandedImage) {
            hideExpandedImage();
        }
    }

    // Success message fade out
    window.addEventListener('DOMContentLoaded', () => {
        const message = document.getElementById('successMessage');
        if (message) {
            setTimeout(() => {
                message.style.transition = 'opacity 0.5s ease';
                message.style.opacity = '0';
                setTimeout(() => message.remove(), 500);
            }, 3000);
        }
    });
    </script>
</body>
</html>