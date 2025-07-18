<?php
// Check for user session and handle record creation
session_start();

// Handle new record creation (only for logged-in users)
if (isset($_POST['create_record']) && isset($_SESSION['user_id'])) {
    // Database connection for record creation
    $serveris = "localhost";
    $lietotajs = "grobina1_belovinceva";
    $parole = "U9R1@kvzL";
    $datubaze = "grobina1_belovinceva";

    $savienojums_create = mysqli_connect($serveris, $lietotajs, $parole, $datubaze);
    mysqli_set_charset($savienojums_create, "utf8mb4");

    if ($savienojums_create) {
        $type_id = (int)$_POST['type_id'];
        $category_id = (int)$_POST['category_id'];
        $description = trim($_POST['description']);
        $country = trim($_POST['country']);
        $first_mention_date = trim($_POST['first_mention_date']);
        $description_text = trim($_POST['description_text']);
        $images = trim($_POST['images']);
        $published = 0; // Always set to 0 for user submissions

        $stmt = $savienojums_create->prepare("INSERT INTO eksamens_entries (type_id, category_id, description, country, first_mention_date, description_text, images, published, created_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iisssssi", $type_id, $category_id, $description, $country, $first_mention_date, $description_text, $images, $published);
        
        if ($stmt->execute()) {
            $success_message = "Ieraksts veiksmīgi izveidots! Tas tiks pārbaudīts pirms publicēšanas.";
        } else {
            $error_message = "Kļūda ieraksta izveidošanā.";
        }
        
        $stmt->close();
        mysqli_close($savienojums_create);
    }
}

if (!isset($current_page)) {
    $current_page = '';
}

// Detect if we're in the mythologies folder
$is_mythology_page = strpos($_SERVER['PHP_SELF'], '/mythologies/') !== false || strpos($_SERVER['PHP_SELF'], '/mifalogija/') !== false;
$base_path = $is_mythology_page ? '../' : '';

// Get all mythologies from database
$serveris = "localhost";
$lietotajs = "grobina1_belovinceva";
$parole = "U9R1@kvzL";
$datubaze = "grobina1_belovinceva";

$savienojums = mysqli_connect($serveris, $lietotajs, $parole, $datubaze);

if (!$savienojums) {
    die("Savienojuma kļūda: " . mysqli_connect_error());
}

mysqli_set_charset($savienojums, "utf8mb4");

// Get all unique mythology types from the database
$sql = "SELECT DISTINCT Nosaukums FROM eksamens_categories WHERE Nosaukums LIKE '%Mitoloģija' ORDER BY Nosaukums";
$rezultats = mysqli_query($savienojums, $sql);

$mitologijas = [];
while ($row = mysqli_fetch_assoc($rezultats)) {
    $mitologijas[] = $row['Nosaukums'];
}

// Get categories for the dropdown (for new record form)
$categories_result = mysqli_query($savienojums, "SELECT id, Nosaukums FROM eksamens_categories ORDER BY Nosaukums");
$categories = [];
while ($cat = mysqli_fetch_assoc($categories_result)) {
    $categories[] = $cat;
}

// Get category IDs for the dropdown
$category_ids_result = mysqli_query($savienojums, "SELECT id_kat, Kategorija FROM eksamens_kategorija ORDER BY Kategorija");
$category_ids = [];
while ($cat_id = mysqli_fetch_assoc($category_ids_result)) {
    $category_ids[] = $cat_id;
}

mysqli_close($savienojums);
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senās Teikas</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="<?php echo $base_path; ?>images/favicon.ico" type="image/x-icon">
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
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            max-height: 80vh;
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

        .new-record-btn {
            background-color: #2196F3;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            margin-left: 10px;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        .new-record-btn:hover {
            background-color: #1976D2;
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
    </style>
</head>
<body>

<?php if (isset($success_message)): ?>
    <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<header class="site-header">
    <div class="logo">
        <i class="fas fa-scroll fa-3x"></i>
        <a href="<?php echo $base_path; ?>index.php"><h1>Senās Teikas</h1></a>
    </div>
    <nav class="site-nav">
        <ul>
            <li><a href="<?php echo $base_path; ?>index.php" <?php echo $current_page === 'index' ? 'class="active"' : ''; ?>>🏠 Sākums</a></li>
            <li class="dropdown">
                <button class="dropbtn">🌍 Mitoloģijas <i class="fas fa-caret-down"></i></button>
                <div class="dropdown-content">
                    <a href="<?php echo $base_path; ?>mifalogija/index.php"
                       <?php echo $current_page === 'mythologies' ? 'class="active"' : ''; ?>>
                       Visas Mitoloģijas
                    </a>
                    <?php foreach ($mitologijas as $mitologija): ?>
                        <a href="<?php echo $base_path; ?>mifalogija/template.php?mitologija=<?php echo urlencode($mitologija); ?>"
                           <?php echo $current_page === strtolower(str_replace(' ', '_', $mitologija)) ? 'class="active"' : ''; ?>>
                           <?php echo htmlspecialchars($mitologija); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </li>
            <li><a href="<?php echo $base_path; ?>search.php" <?php echo $current_page === 'search' ? 'class="active"' : ''; ?>>🔍 Meklēt</a></li>
            <li><a href="<?php echo $base_path; ?>about.php" <?php echo $current_page === 'about' ? 'class="active"' : ''; ?>>📜 Par Vietni</a></li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <li>
                    <button onclick="showCreateRecordForm()" class="new-record-btn">
                        <i class="fas fa-plus"></i> Jauns Ieraksts
                    </button>
                </li>
                <li><a href="<?php echo $base_path; ?>profile.php" class="profile-btn"><i class="fas fa-user"></i> Profils</a></li>
            <?php else: ?>
                <li><a href="<?php echo $base_path; ?>login.php" class="profile-btn"><i class="fas fa-user"></i> Pieslēgties</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<!-- Create Record Modal (only shown for logged-in users) -->
<?php if (isset($_SESSION['user_id'])): ?>
<div id="createRecordModal" class="modal">
    <div class="modal-content">
        <h2><i class="fas fa-plus"></i> Jauns Ieraksts</h2>
        <form method="POST" accept-charset="UTF-8">
            <div class="form-group">
                <label for="type_id">Kategorija</label>
                <select name="type_id" required>
                    <option value="">Izvēlieties kategoriju</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['Nosaukums']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="category_id">Kategorijas ID</label>
                <select name="category_id" required>
                    <option value="">Izvēlieties kategorijas ID</option>
                    <?php foreach ($category_ids as $cat_id): ?>
                        <option value="<?php echo $cat_id['id_kat']; ?>"><?php echo htmlspecialchars($cat_id['Kategorija']); ?></option>
                    <?php endforeach; ?>
                </select>
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
                <input type="text" name="images" placeholder="Attēla URL">
                <small>Ievadiet attēla URL</small>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <button type="submit" name="create_record" class="submit-btn">
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
function showCreateRecordForm() {
    const modal = document.getElementById('createRecordModal');
    const form = modal.querySelector('form');
    
    // Reset form
    form.reset();
    
    // Show modal
    modal.style.display = 'block';
}

function hideCreateRecordModal() {
    document.getElementById('createRecordModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('createRecordModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>
<?php endif; ?>
</body>
</html>