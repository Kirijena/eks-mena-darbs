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
            display: flex;
            align-items: center;
            gap: 8px;
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

        /* Category Section */
        .category-section {
            background: var(--color-surface);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 8px 25px rgba(139, 69, 19, 0.1);
            border: 1px solid rgba(146, 64, 14, 0.1);
        }

        .category-section h1 {
            color: var(--color-primary);
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 10px;
            font-family: 'Cinzel', serif;
            font-weight: 700;
            position: relative;
            padding-bottom: 15px;
        }

        .category-section h1::after {
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

        .mythology-subtitle {
            text-align: center;
            color: var(--color-text-light);
            font-size: 1.2rem;
            margin-bottom: 40px;
            font-style: italic;
        }

        /* Records Grid */
        .records-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .record-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.1);
            border-left: 4px solid var(--color-primary);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .record-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(146, 64, 14, 0.05), transparent);
            transition: left 0.3s;
        }

        .record-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(139, 69, 19, 0.15);
        }

        .record-card:hover::before {
            left: 100%;
        }

        .record-title {
            color: var(--color-primary);
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 15px;
            font-family: 'Cinzel', serif;
            position: relative;
            z-index: 1;
        }

        .record-field {
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
        }

        .field-label {
            font-weight: 600;
            color: var(--color-primary);
            display: inline-block;
            min-width: 120px;
            margin-right: 10px;
        }

        .field-value {
            color: var(--color-text);
            line-height: 1.5;
        }

        .learn-more-btn {
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
            position: relative;
            z-index: 1;
        }

        .learn-more-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(146, 64, 14, 0.3);
            text-decoration: none;
            color: white;
        }

        .learn-more-btn i {
            font-size: 0.8rem;
        }

        .back-link {
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
            position: relative;
            z-index: 1;
        }

        .back-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(146, 64, 14, 0.3);
            text-decoration: none;
            color: white;
        }

        .back-link i {
            font-size: 0.8rem;
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

        .record-image-wrapper {
            margin-top: 20px;
            text-align: center;
        }

        .record-image {
            max-width: 50%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            object-fit: cover;
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
            
            .category-section {
                padding: 25px 20px;
            }
            
            .category-section h1 {
                font-size: 2rem;
            }
            
            .records-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        @media (max-width: 480px) {
            .category-section h1 {
                font-size: 1.8rem;
            }
            
            .record-card {
                padding: 20px;
            }

            .records-grid {
                grid-template-columns: 1fr;
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