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
    <style>
        :root {
            --color-primary: #92400e;
            --color-secondary: #b45309;
            --color-background: #fef3c7;
            --color-surface: rgba(255, 255, 255, 0.9);
            --color-text: #78350f;
            --color-text-light: #92400e;
            --color-border: #d97706;
            --primary-color: #92400e;
            --primary-dark: #b45309;
            --bg-primary: #fef3c7;
            --bg-secondary: #fef3c7;
            --text-dark: #2C1810;
            --text-light: #FFF8F0;
            --accent-color: #D4A373;
            --border-color: #d97706;
            --shadow-color: rgba(139, 69, 19, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(to bottom, #fee0c7, #e9aa75);
            min-height: 100vh;
            color: var(--color-text);
            padding-top: 80px;
        }

        /* Header Styles */
        .site-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--color-surface);
            padding: 15px 30px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo a {
            text-decoration: none;
            color: var(--color-primary);
            transition: color 0.3s ease;
            font-size: 1.8rem;
            font-weight: 700;
            font-family: 'Cinzel', serif;
        }

        .logo i {
            font-size: 30px;
            color: var(--color-primary);
        }

        .back-link {
            color: var(--color-text-light);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .profile-btn{
            color: var(--color-text-light);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
            padding: 1rem;
        }


        .back-link:hover {
            color: var(--color-primary);
        }

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        /* Mythology Section */
        .mythology-section {
            background: var(--color-surface);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 8px 25px rgba(139, 69, 19, 0.1);
            border: 1px solid rgba(146, 64, 14, 0.1);
        }

        .mythology-section h1 {
            color: var(--color-primary);
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 30px;
            font-family: 'Cinzel', serif;
            font-weight: 700;
            position: relative;
            padding-bottom: 15px;
        }

        .mythology-section h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(to right, var(--color-primary), var(--color-secondary));
            border-radius: 2px;
        }

        /* Navigation Menu */
        .mythology-navigation {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            margin-bottom: 40px;
            padding: 20px 0;
            border-bottom: 2px solid rgba(146, 64, 14, 0.1);
        }

        .mythology-navigation a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: rgba(254, 243, 199, 0.5);
            color: var(--color-text);
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .mythology-navigation a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.3s;
        }

        .mythology-navigation a:hover {
            background: var(--color-primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(146, 64, 14, 0.3);
        }

        .mythology-navigation a:hover::before {
            left: 100%;
        }

        .mythology-navigation a.active {
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            color: white;
            border-color: var(--color-primary);
            box-shadow: 0 4px 15px rgba(146, 64, 14, 0.3);
        }

        .mythology-navigation a i {
            font-size: 16px;
        }

        /* Content Area */
        .mythology-content {
            margin-top: 20px;
        }

        .mythology-title {
            color: var(--color-primary);
            font-size: 2.2rem;
            font-family: 'Cinzel', serif;
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }

        .mythology-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(to right, var(--color-primary), var(--color-secondary));
            border-radius: 2px;
        }

        /* Mythology Items Grid */
        .mythology-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .mythology-item {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.1);
            border-left: 4px solid var(--color-primary);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .mythology-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(146, 64, 14, 0.05), transparent);
            transition: left 0.3s;
        }

        .mythology-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(139, 69, 19, 0.15);
        }

        .mythology-item:hover::before {
            left: 100%;
        }

        .mythology-item h3 {
            color: var(--color-primary);
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 15px;
            font-family: 'Cinzel', serif;
            position: relative;
            z-index: 1;
        }

        .mythology-item .item-id {
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }

        .mythology-item .field-group {
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
        }

        .mythology-item .field-label {
            font-weight: 600;
            color: var(--color-primary);
            display: inline-block;
            min-width: 100px;
            margin-right: 10px;
        }

        .mythology-item .field-value {
            color: var(--color-text);
            line-height: 1.5;
        }

        /* Category Groups Styles */
        .category-group {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.1);
            border-left: 4px solid var(--color-secondary);
        }

        .category-group .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .category-group h3 {
            color: var(--color-primary);
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0;
            font-family: 'Cinzel', serif;
            position: relative;
            padding-bottom: 10px;
        }

        .category-group h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(to right, var(--color-primary), var(--color-secondary));
            border-radius: 1px;
        }

        .view-all-btn {
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .view-all-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(146, 64, 14, 0.3);
            text-decoration: none;
            color: white;
        }

        .records-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }

        .record-item {
            background: rgba(254, 243, 199, 0.3);
            padding: 15px 20px;
            border-radius: 8px;
            border: 1px solid rgba(146, 64, 14, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .record-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(146, 64, 14, 0.1), transparent);
            transition: left 0.3s;
        }

        .record-item:hover {
            background: rgba(146, 64, 14, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.1);
            text-decoration: none;
            color: inherit;
        }

        .record-item:hover::before {
            left: 100%;
        }

        .record-title {
            color: var(--color-text);
            font-weight: 600;
            font-size: 1rem;
            line-height: 1.4;
            position: relative;
            z-index: 1;
        }

        .record-id {
            color: var(--color-text-light);
            font-size: 0.85rem;
            opacity: 0.7;
            margin-top: 5px;
        }

        /* Empty State */
        .no-content {
            text-align: center;
            padding: 60px 20px;
            color: var(--color-text-light);
        }

        .no-content i {
            font-size: 4rem;
            color: rgba(146, 64, 14, 0.3);
            margin-bottom: 20px;
            display: block;
        }

        .no-content h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--color-primary);
            font-family: 'Cinzel', serif;
        }

        .no-content p {
            font-size: 1.1rem;
            opacity: 0.8;
            font-style: italic;
        }

        /* Section Divider */
        .section-divider {
            border: none;
            height: 2px;
            background: linear-gradient(to right, transparent, var(--color-primary), transparent);
            margin: 40px 0;
            opacity: 0.3;
        }


        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }
            
            .site-header {
                padding: 10px 15px;
            }
            
            .container {
                padding: 0 15px;
                margin: 10px auto;
            }
            
            .mythology-section {
                padding: 25px 20px;
            }
            
            .mythology-section h1 {
                font-size: 2rem;
            }
            
            .mythology-navigation {
                justify-content: flex-start;
                overflow-x: auto;
                padding-bottom: 10px;
            }
            
            .mythology-navigation a {
                white-space: nowrap;
            }
            
            .mythology-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .mythology-title {
                font-size: 1.8rem;
            }

            .records-list {
                grid-template-columns: 1fr;
            }

            .category-group .category-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }

        @media (max-width: 480px) {
            .mythology-section h1 {
                font-size: 1.8rem;
            }
            
            .mythology-title {
                font-size: 1.5rem;
            }
            
            .mythology-item, .category-group {
                padding: 20px;
            }
        }
    </style>
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
    // Close database connection at the very end
    mysqli_close($savienojums);
    ?>
</body>
</html>