<?php
session_start();
$current_page = 'search';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senās Teikas - Meklēšana</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="content">
        <section class="search-section">
            <h2>Meklēt Teikas</h2>
            <div class="search-container">
                <form method="GET" action="search.php" class="search-box">
                    <input type="text" name="q" id="searchInput" 
                           placeholder="Ievadiet meklējamo vārdu...">
                    <select name="category" id="categoryFilter">
                        <option value="">Visas kategorijas</option>
                    </select>
                    <button type="submit" id="searchButton">
                        <i class="fas fa-search"></i> Meklēt
                    </button>
                </form>
                <div class="search-results">
                    <div class="no-results">
                        <p>Nav atrasts neviens rezultāts.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div id="authModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <?php include 'includes/login-form.php'; ?>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
