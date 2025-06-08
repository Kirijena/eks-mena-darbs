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
    $images = trim($_POST['images']);
    $published = isset($_POST['published']) ? 1 : 0;

    $stmt = $savienojums->prepare("INSERT INTO eksamens_entries (type_id, category_id, title, description, country, first_mention_date, description_text, images, published, created_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iisssssi", $type_id, $category_id, $title, $description, $country, $first_mention_date, $description_text, $images, $published);
    $stmt->execute();
    $stmt->close();
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
    $images = trim($_POST['images']);
    $published = isset($_POST['published']) ? 1 : 0;

    $stmt = $savienojums->prepare("UPDATE eksamens_entries SET type_id = ?, category_id = ?, title = ?, description = ?, country = ?, first_mention_date = ?, description_text = ?, images = ?, published = ? WHERE id = ?");
    $stmt->bind_param("iissssssii", $type_id, $category_id, $title, $description, $country, $first_mention_date, $description_text, $images, $published, $record_id);
    $stmt->execute();
    $stmt->close();
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
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-book"></i> Mītoloģijas Ierakstu Pārvaldība</h1>
            <div class="admin-actions">
                <a href="users.php" class="admin-btn">Lietotāji</a>
                <a href="type.php" class="admin-btn">Type</a>
                <a href="kategoriju.php" class="admin-btn">Kategoriju</a>
                <button onclick="showCreateRecordForm()" class="admin-btn">
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
            <h2><i class="fas fa-plus"></i> Jauns Ieraksts</h2>
            <form method="POST" accept-charset="UTF-8">
                <div class="form-group">
                    <label for="type_id">Kategorija</label>
                    <select name="type_id" required>
                        <option value="">Izvēlieties kategoriju</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['Nosaukums']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="category_id">Kategorijas ID</label>
                    <select name="category_id" required>
                        <option value="">Izvēlieties kategorijas ID</option>
                        <?php foreach ($category_ids as $cat_id): ?>
                            <option value="<?= $cat_id['id_kat'] ?>"><?= htmlspecialchars($cat_id['Kategorija']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title">Nosaukums</label>
                    <input type="text" name="title" placeholder="Nosaukums" required accept-charset="UTF-8">
                </div>

                <div class="form-group">
                    <label for="description">Apraksts</label>
                    <input type="text" name="description" placeholder="Apraksts" required accept-charset="UTF-8">
                </div>

                <div class="form-group">
                    <label for="country">Valsts</label>
                    <input type="text" name="country" placeholder="Valsts" required accept-charset="UTF-8">
                </div>

                <div class="form-group">
                    <label for="first_mention_date">Pirmā pieminējuma datums</label>
                    <input type="text" name="first_mention_date" placeholder="Pirmā pieminējuma datums" accept-charset="UTF-8">
                </div>

                <div class="form-group">
                    <label for="description_text">Detalizēts apraksts</label>
                    <textarea name="description_text" placeholder="Detalizēts apraksts" rows="4" accept-charset="UTF-8"></textarea>
                </div>

                <div class="form-group">
                    <label for="images">Attēli</label>
                    <input type="file" name="image_upload" accept="image/*" onchange="handleImageUpload(this)">
                    <input type="text" name="images" placeholder="Attēla URL vai faila nosaukums" readonly>
                    <small>Augšupielādējiet attēlu vai ievadiet URL</small>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="published" value="1">
                        Publicēts
                    </label>
                </div>

                <button type="submit" name="create_record" class="submit-btn">Saglabāt</button>
                <button type="button" onclick="hideCreateRecordModal()" class="cancel-btn">
                    <i class="fas fa-times"></i> Atcelt
                </button>
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

    function handleImageUpload(input) {
        const file = input.files[0];
        const imageUrlInput = input.parentNode.querySelector('input[name="images"]');
        
        if (file) {
            // In a real implementation, you would upload the file to server
            // For now, we'll just show the filename
            imageUrlInput.value = file.name;
            
            // You can add actual file upload logic here
            // uploadFile(file).then(filename => {
            //     imageUrlInput.value = filename;
            // });
        }
    }

    function showCreateRecordForm() {
        const modal = document.getElementById('createRecordModal');
        const form = modal.querySelector('form');

        // Atiestatīt visus laukus
        form.reset();

        // Iztīrīt vecos hidden input un pogas no rediģēšanas
        form.querySelectorAll('input[name="record_id"]').forEach(el => el.remove());
        
        // Noņemt update pogu, ja tāda ir
        form.querySelectorAll('button[name="update_record"]').forEach(el => el.remove());
        
        // Pārbaudīt, vai create_record poga jau ir formā. Ja nē – pievieno.
        if (!form.querySelector('button[name="create_record"]')) {
            const submitBtn = document.createElement('button');
            submitBtn.type = 'submit';
            submitBtn.name = 'create_record';
            submitBtn.className = 'submit-btn';
            submitBtn.textContent = 'Saglabāt';
            form.insertBefore(submitBtn, form.querySelector('.cancel-btn'));
        }

        // Atjaunot virsrakstu
        modal.querySelector('h2').innerHTML = '<i class="fas fa-plus"></i> Jauns Ieraksts';

        // Parādīt modāli
        modal.style.display = 'block';
    }

    function hideCreateRecordModal() {
        document.getElementById('createRecordModal').style.display = 'none';
    }

    function showEditRecordForm(record) {
        const modal = document.getElementById('createRecordModal');
        const form = modal.querySelector('form');

        // Iestatīt vērtības formas laukos
        form.querySelector('select[name="type_id"]').value = record.type_id;
        form.querySelector('select[name="category_id"]').value = record.category_id;
        form.querySelector('input[name="title"]').value = record.title || '';
        form.querySelector('input[name="description"]').value = record.description;
        form.querySelector('input[name="country"]').value = record.country;
        form.querySelector('input[name="first_mention_date"]').value = record.first_mention_date;
        form.querySelector('textarea[name="description_text"]').value = record.description_text;
        form.querySelector('input[name="images"]').value = record.images;
        form.querySelector('input[name="published"]').checked = record.published == 1;

        // Mainīt virsrakstu
        modal.querySelector('h2').innerHTML = '<i class="fas fa-edit"></i> Rediģēt Ierakstu';

        // Noņemt vecas pogas
        form.querySelectorAll('button[name="create_record"]').forEach(el => el.remove());
        form.querySelectorAll('button[name="update_record"]').forEach(el => el.remove());
        form.querySelectorAll('input[name="record_id"]').forEach(el => el.remove());

        // Pievienot slēptu lauku ar ieraksta ID
        const recordIdInput = document.createElement('input');
        recordIdInput.type = 'hidden';
        recordIdInput.name = 'record_id';
        recordIdInput.value = record.id;
        form.appendChild(recordIdInput);

        // Pievienot pogu "update_record"
        const submitBtn = document.createElement('button');
        submitBtn.type = 'submit';
        submitBtn.name = 'update_record';
        submitBtn.className = 'submit-btn';
        submitBtn.textContent = 'Saglabāt izmaiņas';
        form.insertBefore(submitBtn, form.querySelector('.cancel-btn'));

        // Parādīt modālo logu
        modal.style.display = 'block';
    }

    // Aizvērt modālo logu, noklikšķinot ārpus tā
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