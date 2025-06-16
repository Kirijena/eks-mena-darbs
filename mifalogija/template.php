<?php
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
$sql = "SELECT DISTINCT Nosaukums, id FROM eksamens_categories WHERE Nosaukums LIKE '%Mitoloģija' ORDER BY Nosaukums";
$rezultats = mysqli_query($savienojums, $sql);

$mitologijas = [];
while ($row = mysqli_fetch_assoc($rezultats)) {
    $mitologijas[] = $row;
}

// Get the selected mythology from URL parameter
$selected_mythology = isset($_GET['mitologija']) ? urldecode($_GET['mitologija']) : '';
$selected_type_id = null;

// Find the type_id for the selected mythology
foreach ($mitologijas as $mitologija) {
    if ($mitologija['Nosaukums'] === $selected_mythology) {
        $selected_type_id = $mitologija['id'];
        break;
    }
}

// Get content for the selected mythology (original eksamens_categories records)
$mythology_content = [];
if (!empty($selected_mythology)) {
    $sql_content = "SELECT * FROM eksamens_categories WHERE Nosaukums = ? ORDER BY id";
    $stmt = mysqli_prepare($savienojums, $sql_content);
    mysqli_stmt_bind_param($stmt, "s", $selected_mythology);
    mysqli_stmt_execute($stmt);
    $content_result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($content_result)) {
        $mythology_content[] = $row;
    }
    mysqli_stmt_close($stmt);
}

// Get records by type_id, grouped by category_id - ONLY PUBLISHED RECORDS
$grouped_records = [];
if (!empty($selected_type_id)) {
    $sql_records = "SELECT id, category_id, title FROM eksamens_entries WHERE type_id = ? AND published = 1 ORDER BY category_id, title";
    $stmt_records = mysqli_prepare($savienojums, $sql_records);
    mysqli_stmt_bind_param($stmt_records, "i", $selected_type_id);
    mysqli_stmt_execute($stmt_records);
    $records_result = mysqli_stmt_get_result($stmt_records);
    
    while ($row = mysqli_fetch_assoc($records_result)) {
        $category_id = $row['category_id'];
        if (!isset($grouped_records[$category_id])) {
            $grouped_records[$category_id] = [];
        }
        $grouped_records[$category_id][] = $row;
    }
    mysqli_stmt_close($stmt_records);
}

// Get category names for better display - CORRECTED VERSION
$category_names = [];
if (!empty($grouped_records)) {
    $category_ids = array_keys($grouped_records);
    if (!empty($category_ids)) {
        // Use simple query instead of prepared statement for better reliability
        $category_ids_str = implode(',', array_map('intval', $category_ids));
        $sql_categories = "SELECT id_kat, Kategorija FROM eksamens_kategorija WHERE id_kat IN ($category_ids_str)";
        $result_categories = mysqli_query($savienojums, $sql_categories);
        
        if ($result_categories) {
            while ($row = mysqli_fetch_assoc($result_categories)) {
                $category_names[$row['id_kat']] = $row['Kategorija'];
            }
        }
    }
}

// DON'T CLOSE DATABASE CONNECTION YET - we'll close it at the end
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo !empty($selected_mythology) ? htmlspecialchars($selected_mythology) : 'Mitoloģijas'; ?></title>
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
            <a href="../login.php" class="profile-btn">
                <i class="fas fa-user"></i>Profile
            </a>
            <a href="<?php echo $base_path; ?>index.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Atpakaļ uz sākumu
            </a>
        </nav>
    </header>

    <!-- Main Container -->
    <div class="container">
        <div class="mythology-section">
            <h1><i class="fas fa-gods"></i> Mitoloģiju Kolekcija</h1>
            
            <!-- Navigation Menu -->
            <div class="mythology-navigation">
                <?php foreach ($mitologijas as $mitologija): ?>
                    <a href="<?php echo $base_path; ?>mifalogija/template.php?mitologija=<?php echo urlencode($mitologija['Nosaukums']); ?>" 
                       <?php echo $selected_mythology === $mitologija['Nosaukums'] ? 'class="active"' : ''; ?>>
                        <i class="fas fa-star"></i>
                        <?php echo htmlspecialchars($mitologija['Nosaukums']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Content Area -->
            <div class="mythology-content">
                <?php if (!empty($selected_mythology)): ?>
                    <h2 class="mythology-title">
                        <i class="fas fa-crown"></i>
                        <?php echo htmlspecialchars($selected_mythology); ?>
                    </h2>
                    
                    <!-- Records grouped by category -->
                    <?php if (!empty($grouped_records)): ?>
                        <hr class="section-divider">
                        <h2 class="mythology-title">
                            <i class="fas fa-list"></i>
                            Saistītie ieraksti
                        </h2>
                        
                        <?php foreach ($grouped_records as $category_id => $records): ?>
                            <div class="category-group">
                                <div class="category-header">
                                    <h3>
                                        <i class="fas fa-folder"></i>
                                        <?php echo isset($category_names[$category_id]) ? htmlspecialchars($category_names[$category_id]) : "Kategorija #$category_id"; ?>
                                    </h3>
                                    <a href="<?php echo $base_path; ?>mifalogija/category_view.php?category_id=<?php echo $category_id; ?>&mitologija=<?php echo urlencode($selected_mythology); ?>" class="view-all-btn">
                                        <i class="fas fa-eye"></i>
                                        Skatīt visus
                                    </a>
                                </div>
                                
                                <div class="records-list">
                                    <?php 
                                    // Limit to maximum 3 records per category
                                    $limited_records = array_slice($records, 0, 3);
                                    foreach ($limited_records as $record): 
                                    ?>
                                        <a href="<?php echo $base_path; ?>mifalogija/entry.php?id=<?php echo $record['id']; ?>" class="record-item">
                                            <div class="record-title">
                                                <?php echo htmlspecialchars($record['title']); ?>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (empty($mythology_content) && empty($grouped_records)): ?>
                        <div class="no-content">
                            <i class="fas fa-search"></i>
                            <h3>Nav atrasts saturs</h3>
                            <p>Šai mitoloģijai pašlaik nav pieejama informācija.</p>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="no-content">
                        <i class="fas fa-hand-point-up"></i>
                        <h3>Izvēlieties mitoloģiju</h3>
                        <p>Lūdzu, izvēlieties mitoloģiju no saraksta augšā, lai skatītu tās saturu.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php
    mysqli_close($savienojums);
    ?>
</body>
</html>