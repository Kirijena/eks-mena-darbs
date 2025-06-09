<?php
session_start();
require '../includes/connect_db.php';

// Set charset to UTF-8 for proper Latvian character handling
mysqli_set_charset($savienojums, "utf8");

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle file upload
function handleFileUpload($file) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return '';
    }
    
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 16 * 1024 * 1024; // 16MB
    
    if (!in_array($file['type'], $allowed_types)) {
        return '';
    }
    
    if ($file['size'] > $max_size) {
        return '';
    }
    
    $upload_dir = '../uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return $new_filename;
    }
    
    return '';
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
    
    // Handle image upload or URL
    $images = '';
    if (isset($_FILES['images']) && $_FILES['images']['error'] === UPLOAD_ERR_OK) {
        $images = handleFileUpload($_FILES['images']);
    } elseif (!empty(trim($_POST['image_url']))) {
        $images = trim($_POST['image_url']);
    }

    $stmt = $savienojums->prepare("INSERT INTO eksamens_entries (type_id, category_id, title, description, country, first_mention_date, description_text, images, published, created_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iisssssi", $type_id, $category_id, $title, $description, $country, $first_mention_date, $description_text, $images, $published);
    $stmt->execute();
    $stmt->close();
    
    $success_message = "Ieraksts veiksmīgi izveidots!";
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
    
    // Handle image upload or URL
    $images = trim($_POST['current_image']); // Keep current image by default
    if (isset($_FILES['images']) && $_FILES['images']['error'] === UPLOAD_ERR_OK) {
        $images = handleFileUpload($_FILES['images']);
    } elseif (!empty(trim($_POST['image_url']))) {
        $images = trim($_POST['image_url']);
    }

    $stmt = $savienojums->prepare("UPDATE eksamens_entries SET type_id = ?, category_id = ?, title = ?, description = ?, country = ?, first_mention_date = ?, description_text = ?, images = ?, published = ? WHERE id = ?");
    $stmt->bind_param("iissssssii", $type_id, $category_id, $title, $description, $country, $first_mention_date, $description_text, $images, $published, $record_id);
    $stmt->execute();
    $stmt->close();
    
    $success_message = "Ieraksts veiksmīgi atjaunots!";
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

// Get categories for the dropdown
$categories_result = $savienojums->query("SELECT id, Nosaukums FROM eksamens_categories ORDER BY Nosaukums");
$categories = [];
while ($cat = $categories_result->fetch_assoc()) {
    $categories[] = $cat;
}

// Get category IDs for the dropdown
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
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 2% auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .file-info {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .file-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: none;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            border: 1px solid #f5c6cb;
        }

        .submit-btn, .cancel-btn {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .submit-btn {
            background-color: #4CAF50;
            color: white;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }

        .cancel-btn {
            background-color: #f44336;
            color: white;
        }

        .cancel-btn:hover {
            background-color: #da190b;
        }
    </style>
</head>
<body>
    <?php if (isset($success_message)): ?>
        <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
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
                        <td><?= htmlspecialchars($record['title'] ?? '') ?></td>
                        <td><?= htmlspecialchars(substr($record['description'], 0, 50)) ?>...</td>
                        <td><?= htmlspecialchars($record['country']) ?></td>
                        <td><?= htmlspecialchars($record['first_mention_date']) ?></td>
                        <td><?= htmlspecialchars(substr($record['description_text'], 0, 50)) ?>...</td>
                        <td>
                            <?php if (!empty($record['images'])): ?>
                                <button onclick="showImageModal('<?= htmlspecialchars($record['images']) ?>')" class="image-btn">
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

    <!-- Image Viewer Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-content image-modal-content">
            <span class="close-image" onclick="hideImageModal()">&times;</span>
            <img id="modalImage" src="" alt="Mythology Image" style="max-width: 100%; max-height: 80vh; display: block; margin: 0 auto;">
            <div class="image-info">
                <p>Noklikšķiniet uz attēla, lai aizvērtu</p>
            </div>
        </div>
    </div>

    <!-- Create/Edit Record Modal -->
    <div id="createRecordModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle"><i class="fas fa-plus"></i> Jauns Ieraksts</h2>
            <form id="recordForm" method="POST" accept-charset="UTF-8" enctype="multipart/form-data">
                <input type="hidden" id="recordId" name="record_id" value="">
                <input type="hidden" id="currentImage" name="current_image" value="">
                
                <div class="form-group">
                    <label for="type_id">Kategorija</label>
                    <select name="type_id" id="typeId" required>
                        <option value="">Izvēlieties kategoriju</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['Nosaukums']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="category_id">Kategorijas ID</label>
                    <select name="category_id" id="categoryId" required>
                        <option value="">Izvēlieties kategorijas ID</option>
                        <?php foreach ($category_ids as $cat_id): ?>
                            <option value="<?php echo $cat_id['id_kat']; ?>"><?php echo htmlspecialchars($cat_id['Kategorija']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title">Nosaukums</label>
                    <input type="text" name="title" id="title" placeholder="Ieraksta nosaukums" required accept-charset="UTF-8">
                </div>

                <div class="form-group">
                    <label for="description">Apraksts</label>
                    <input type="text" name="description" id="description" placeholder="Īss apraksts" required accept-charset="UTF-8">
                </div>

                <div class="form-group">
                    <label for="country">Valsts</label>
                    <input type="text" name="country" id="country" placeholder="Valsts" required accept-charset="UTF-8">
                </div>

                <div class="form-group">
                    <label for="first_mention_date">Pirmā pieminējuma datums</label>
                    <input type="text" name="first_mention_date" id="firstMentionDate" placeholder="Pirmā pieminējuma datums" accept-charset="UTF-8">
                </div>

                <div class="form-group">
                    <label for="description_text">Detalizēts apraksts</label>
                    <textarea name="description_text" id="descriptionText" placeholder="Detalizēts apraksts" rows="4" accept-charset="UTF-8"></textarea>
                </div>

                <div class="form-group">
                    <label for="images">Attēls (fails)</label>
                    <input type="file" name="images" id="imageInput" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" onchange="previewImage(this)">
                    <div class="file-info">
                        Maksimālais faila izmērs: 16MB<br>
                        Atbalstītie formāti: JPEG, PNG, GIF, WebP
                    </div>
                    <img id="imagePreview" class="file-preview" alt="Attēla priekšskatījums">
                </div>

                <div class="form-group">
                    <label for="image_url">VAI Attēla URL</label>
                    <input type="text" name="image_url" id="imageUrl" placeholder="https://example.com/image.jpg">
                    <div class="file-info">Ja augšup netika augšupielādēts fails, var ievadīt attēla URL</div>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="published" id="published">
                        <label for="published">Publicēts</label>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 20px;">
                    <button type="submit" id="submitBtn" name="create_record" class="submit-btn">
                        <i class="fas fa-save"></i> Saglabāt
                    </button>
                    <button type="button" onclick="hideCreateRecordModal()" class="cancel-btn">
                        <i class="fas fa-times"></i> Atcelt
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function showImageModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        
        // Check if it's a URL or a file path
        if (imageSrc.startsWith('http') || imageSrc.startsWith('/') || imageSrc.startsWith('../')) {
            modalImg.src = imageSrc;
        } else {
            // Assume it's in an uploads directory
            modalImg.src = '../uploads/' + imageSrc;
        }
        
        modal.style.display = 'block';
    }

    function hideImageModal() {
        document.getElementById('imageModal').style.display = 'none';
    }

    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const file = input.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    }

    function showCreateRecordForm() {
        const modal = document.getElementById('createRecordModal');
        const form = document.getElementById('recordForm');
        const title = document.getElementById('modalTitle');
        const submitBtn = document.getElementById('submitBtn');
        
        // Reset form
        form.reset();
        document.getElementById('recordId').value = '';
        document.getElementById('currentImage').value = '';
        
        // Hide image preview
        document.getElementById('imagePreview').style.display = 'none';
        
        // Set create mode
        title.innerHTML = '<i class="fas fa-plus"></i> Jauns Ieraksts';
        submitBtn.name = 'create_record';
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Saglabāt';
        
        // Show modal
        modal.style.display = 'block';
    }

    function showEditRecordForm(record) {
        const modal = document.getElementById('createRecordModal');
        const form = document.getElementById('recordForm');
        const title = document.getElementById('modalTitle');
        const submitBtn = document.getElementById('submitBtn');
        
        // Set form values
        document.getElementById('recordId').value = record.id;
        document.getElementById('currentImage').value = record.images || '';
        document.getElementById('typeId').value = record.type_id || '';
        document.getElementById('categoryId').value = record.category_id || '';
        document.getElementById('title').value = record.title || '';
        document.getElementById('description').value = record.description || '';
        document.getElementById('country').value = record.country || '';
        document.getElementById('firstMentionDate').value = record.first_mention_date || '';
        document.getElementById('descriptionText').value = record.description_text || '';
        document.getElementById('imageUrl').value = record.images || '';
        document.getElementById('published').checked = record.published == 1;
        
        // Hide image preview initially
        document.getElementById('imagePreview').style.display = 'none';
        
        // Set edit mode
        title.innerHTML = '<i class="fas fa-edit"></i> Rediģēt Ierakstu';
        submitBtn.name = 'update_record';
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Saglabāt Izmaiņas';
        
        // Show modal
        modal.style.display = 'block';
    }

    function hideCreateRecordModal() {
        document.getElementById('createRecordModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const createModal = document.getElementById('createRecordModal');
        const imageModal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        
        if (event.target == createModal) {
            createModal.style.display = 'none';
        }
        
        if (event.target == imageModal || event.target == modalImg) {
            imageModal.style.display = 'none';
        }
    }
    </script>
</body>
</html>