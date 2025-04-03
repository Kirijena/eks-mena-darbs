<?php
if (!isset($current_page)) {
    $current_page = '';
}

// Detect if we're in the mythologies folder
$is_mythology_page = strpos($_SERVER['PHP_SELF'], '/mythologies/') !== false;
$base_path = $is_mythology_page ? '../' : '';
?>
<header class="site-header">
    <div class="logo">
        <i class="fas fa-scroll fa-3x"></i>
        <h1>Senās Teikas</h1>
    </div>
    <nav class="site-nav">
        <ul>
            <li><a href="<?php echo $base_path; ?>index.php" <?php echo $current_page === 'index' ? 'class="active"' : ''; ?>>🏠 Sākums</a></li>
            <li><a href="<?php echo $base_path; ?>latest.php" <?php echo $current_page === 'latest' ? 'class="active"' : ''; ?>>🏛 Jaunākie Ieraksti</a></li>
            <li><a href="<?php echo $base_path; ?>all-entries.php" <?php echo $current_page === 'all-entries' ? 'class="active"' : ''; ?>>📖 Visi Ieraksti</a></li>
            <li class="dropdown">
                <button class="dropbtn">🌍 Mitoloģijas <i class="fas fa-caret-down"></i></button>
                <div class="dropdown-content">
                    <a href="<?php echo $base_path; ?>mythologies/norse.php" <?php echo $current_page === 'norse' ? 'class="active"' : ''; ?>>Skandināvu Mitoloģija</a>
                    <a href="<?php echo $base_path; ?>mythologies/greek.php" <?php echo $current_page === 'greek' ? 'class="active"' : ''; ?>>Sengrieķu mitoloģija</a>
                    <a href="<?php echo $base_path; ?>mythologies/roman.php" <?php echo $current_page === 'roman' ? 'class="active"' : ''; ?>>Romiešu mitoloģija</a>
                    <a href="<?php echo $base_path; ?>mythologies/egyptian.php" <?php echo $current_page === 'egyptian' ? 'class="active"' : ''; ?>>Ēģiptes mitoloģija</a>
                    <a href="<?php echo $base_path; ?>mythologies/slavic.php" <?php echo $current_page === 'slavic' ? 'class="active"' : ''; ?>>Slāvu mitoloģija</a>
                    <a href="<?php echo $base_path; ?>mythologies/celtic.php" <?php echo $current_page === 'celtic' ? 'class="active"' : ''; ?>>Ķeltu mitoloģija</a>
                    <a href="<?php echo $base_path; ?>mythologies/asian.php" <?php echo $current_page === 'asian' ? 'class="active"' : ''; ?>>Āzijas mitoloģija</a>
                </div>
            </li>
            <li><a href="<?php echo $base_path; ?>search.php" <?php echo $current_page === 'search' ? 'class="active"' : ''; ?>>🔍 Meklēt</a></li>
            <li><a href="<?php echo $base_path; ?>about.php" <?php echo $current_page === 'about' ? 'class="active"' : ''; ?>>📜 Par Vietni</a></li>
        </ul>
    </nav>
    <?php if(isset($_SESSION['user'])): ?>
        <button id="authButton" class="auth-btn" onclick="window.location.href='<?php echo $base_path; ?>logout.php'">
            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user']); ?>
        </button>
    <?php else: ?>
        <button id="authButton" class="auth-btn" onclick="document.getElementById('authModal').style.display='block'">
            <i class="fas fa-user"></i> Pieteikties
        </button>
    <?php endif; ?>
</header>
