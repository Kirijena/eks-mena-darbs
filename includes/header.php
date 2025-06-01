<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senās Teikas</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="<?php echo $base_path; ?>images/favicon.ico" type="image/x-icon">
</head>
<body>
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
$sql = "SELECT DISTINCT Nosaukums FROM eksamens_categories WHERE Nosaukums LIKE '%Mitoloģija' ORDER BY Nosaukums";
$rezultats = mysqli_query($savienojums, $sql);

$mitologijas = [];
while ($row = mysqli_fetch_assoc($rezultats)) {
    $mitologijas[] = $row['Nosaukums'];
}

mysqli_close($savienojums);
?>
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
                    <?php foreach ($mitologijas as $mitologija): ?>
                        <a href="<?php echo $base_path; ?>mifalogija/template.php?mitologija=<?php echo urlencode($mitologija); ?>"
                           <?php echo $current_page === strtolower(str_replace(' ', '_', $mitologija)) ? 'class="active"' : ''; ?>>
                           <?php echo htmlspecialchars($mitologija); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </li>
            <li><a href="<?php echo $base_path; ?>search.php" <?php echo $current_page === 'search' ? 'class="active"' : ''; ?>>🔍 Meklēt</a></li>
            <a href="login.php" class="profile-btn"><i class="fas fa-user"></i>Profile</a>
        </ul>
    </nav>
</header>
