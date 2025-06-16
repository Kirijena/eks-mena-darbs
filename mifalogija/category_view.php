<?php
if (!isset($current_page)) {
    $current_page = '';
}

// Detect if we're in the mythologies folder
$is_mythology_page = strpos($_SERVER['PHP_SELF'], '/mythologies/') !== false || strpos($_SERVER['PHP_SELF'], '/mifalogija/') !== false;
$base_path = $is_mythology_page ? '../' : '';

// Get parameters
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$type_id = isset($_GET['type_id']) ? (int)$_GET['type_id'] : 0;
$selected_mythology = isset($_GET['mitologija']) ? urldecode($_GET['mitologija']) : '';

// Database connection
$serveris = "localhost";
$lietotajs = "grobina1_belovinceva";
$parole = "U9R1@kvzL";
$datubaze = "grobina1_belovinceva";

$savienojums = mysqli_connect($serveris, $lietotajs, $parole, $datubaze);

if (!$savienojums) {
    die("Savienojuma kļūda: " . mysqli_connect_error());
}

mysqli_set_charset($savienojums, "utf8mb4");

// Get category name - using the correct table name from your reference code
$category_name = "Nezināma kategorija";
if ($category_id > 0) {
    $sql_cat = "SELECT Kategorija FROM eksamens_kategorija WHERE id_kat = ?";
    $stmt_cat = mysqli_prepare($savienojums, $sql_cat);
    mysqli_stmt_bind_param($stmt_cat, "i", $category_id);
    mysqli_stmt_execute($stmt_cat);
    $result_cat = mysqli_stmt_get_result($stmt_cat);
    if ($row_cat = mysqli_fetch_assoc($result_cat)) {
        $category_name = $row_cat['Kategorija'];
    }
    mysqli_stmt_close($stmt_cat);
}

// Get type name
$type_name = "";
if ($type_id > 0) {
    $sql_type = "SELECT Nosaukums FROM eksamens_categories WHERE id = ?";
    $stmt_type = mysqli_prepare($savienojums, $sql_type);
    mysqli_stmt_bind_param($stmt_type, "i", $type_id);
    mysqli_stmt_execute($stmt_type);
    $result_type = mysqli_stmt_get_result($stmt_type);
    if ($row_type = mysqli_fetch_assoc($result_type)) {
        $type_name = $row_type['Nosaukums'];
    }
    mysqli_stmt_close($stmt_type);
}

// Get all records for this category and type - ONLY PUBLISHED RECORDS (published = 1)
// Both category_id AND type_id should be provided for filtering
$records = [];
if ($category_id > 0 && $type_id > 0) {
    // Filter by both category_id and type_id - both are required
    $sql_records = "SELECT id, title, description, country, images, first_mention_date FROM eksamens_entries WHERE category_id = ? AND type_id = ? AND published = 1 ORDER BY title";
    $stmt_records = mysqli_prepare($savienojums, $sql_records);
    mysqli_stmt_bind_param($stmt_records, "ii", $category_id, $type_id);
    mysqli_stmt_execute($stmt_records);
    $result_records = mysqli_stmt_get_result($stmt_records);
    
    while ($row = mysqli_fetch_assoc($result_records)) {
        $records[] = $row;
    }
    mysqli_stmt_close($stmt_records);
} else if ($category_id > 0) {
    // If only category_id is provided, show all records for that category
    $sql_records = "SELECT id, title, description, country, images, first_mention_date FROM eksamens_entries WHERE category_id = ? AND published = 1 ORDER BY title";
    $stmt_records = mysqli_prepare($savienojums, $sql_records);
    mysqli_stmt_bind_param($stmt_records, "i", $category_id);
    mysqli_stmt_execute($stmt_records);
    $result_records = mysqli_stmt_get_result($stmt_records);
    
    while ($row = mysqli_fetch_assoc($result_records)) {
        $records[] = $row;
    }
    mysqli_stmt_close($stmt_records);
} else if ($type_id > 0) {
    // If only type_id is provided, show all records for that type
    $sql_records = "SELECT id, title, description, country, images, first_mention_date FROM eksamens_entries WHERE type_id = ? AND published = 1 ORDER BY title";
    $stmt_records = mysqli_prepare($savienojums, $sql_records);
    mysqli_stmt_bind_param($stmt_records, "i", $type_id);
    mysqli_stmt_execute($stmt_records);
    $result_records = mysqli_stmt_get_result($stmt_records);
    
    while ($row = mysqli_fetch_assoc($result_records)) {
        $records[] = $row;
    }
    mysqli_stmt_close($stmt_records);
}

mysqli_close($savienojums);
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category_name); ?> - <?php echo htmlspecialchars($selected_mythology); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="mifal.css">
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="logo">
            <i class="fas fa-scroll"></i>
            <a href="<?php echo $base_path; ?>index.php">Mitoloģijas</a>
        </div>
        <nav>
            <a href="<?php echo $base_path; ?>mifalogija/template.php?mitologija=<?php echo urlencode($selected_mythology); ?>" class="back-link">
                <i class="fas fa-arrow-left"></i> 
                Atpakaļ uz <?php echo htmlspecialchars($selected_mythology); ?>
            </a>
        </nav>
    </header>

    <!-- Main Container -->
    <div class="container">
        <div class="category-section">
            <h1><i class="fas fa-folder-open"></i> 
                <?php 
                if (!empty($type_name) && !empty($category_name)) {
                    echo htmlspecialchars($type_name) . " - " . htmlspecialchars($category_name);
                } else if (!empty($category_name)) {
                    echo htmlspecialchars($category_name);
                } else if (!empty($type_name)) {
                    echo htmlspecialchars($type_name);
                } else {
                    echo "Ieraksti";
                }
                ?>
            </h1>
            <div class="mythology-subtitle">
                <?php echo htmlspecialchars($selected_mythology); ?>
            </div>
            
            <!-- Records Grid -->
            <?php if (!empty($records)): ?>
                <div class="records-grid">
                    <?php foreach ($records as $record): ?>
                        <div class="record-card">
                            <h3 class="record-title">
                                <?php echo htmlspecialchars($record['title']); ?>
                            </h3>

                            <?php if (!empty($record['description'])): ?>
                                <div class="record-field">
                                    <span class="field-label">Description:</span>
                                    <span class="field-value"><?php echo htmlspecialchars($record['description']); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($record['country'])): ?>
                                <div class="record-field">
                                    <span class="field-label">Valsts:</span>
                                    <span class="field-value"><?php echo htmlspecialchars($record['country']); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($record['first_mention_date'])): ?>
                                <div class="record-field">
                                    <span class="field-label">Pirmā pieminēšana:</span>
                                    <span class="field-value"><?php echo htmlspecialchars($record['first_mention_date']); ?></span>
                                </div>
                            <?php endif; ?>

                             <div class="record-image-wrapper">
                                <img src="show_image.php?id=<?php echo $record['id']; ?>" alt="<?php echo htmlspecialchars($record['title']); ?>" class="record-image">
                            </div>
                            
                            <a href="<?php echo $base_path; ?>mifalogija/entry.php?id=<?php echo $record['id']; ?>" class="learn-more-btn">
                                <i class="fas fa-book-open"></i>
                                Uzzināt vairāk
                            </a>
                        </div>
                        
                    <?php endforeach; ?>
                </div>
                <a class="back-link" onclick="history.back()">← Atpakaļ</a>
            <?php else: ?>
                <div class="no-content">
                    <i class="fas fa-search"></i>
                    <h3>Nav atrasti ieraksti</h3>
                    <p>Šajā sadaļā pašlaik nav pieejami publicēti ieraksti.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>