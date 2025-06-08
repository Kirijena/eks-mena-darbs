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
    <style>
        .search-container {
            position: relative;
            max-width: 900px; /* увеличили максимальную ширину */
            margin: 20px auto;
            width: 90vw; /* занять почти всю ширину окна */
        }

        #searchInput {
            width: 100%;
            padding: 18px 20px; /* увеличили отступы для большего поля */
            border: 3px solid #92400e;
            border-radius: 8px;
            font-size: 24px; /* большой шрифт */
            font-weight: 600;
            box-sizing: border-box;
            outline-offset: 3px;
            transition: border-color 0.3s ease;
        }

        #searchInput:focus {
            border-color: #b45309;
            box-shadow: 0 0 10px #b45309aa;
        }

        .suggestions-box {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff8dc;
            border: 3px solid #92400e;
            border-top: none;
            z-index: 1000;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
            max-height: 350px; /* увеличена высота */
            overflow-y: auto;
            font-size: 20px; /* крупный шрифт результатов */
            font-weight: 500;
        }

        .suggestions-box ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .suggestions-box li {
            padding: 18px 25px; /* большие отступы */
            border-bottom: 1px solid #e2c089;
            cursor: pointer;
            transition: background 0.3s;
        }

        .suggestions-box li:hover {
            background-color: #fde68a;
        }

        .h2 {
            font-size: 2rem;
        }

    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="content">
        <section class="search-section">
            <h2>Meklēt Teikas</h2>
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Ievadiet meklējamo vārdu...">
                <div id="suggestions" class="suggestions-box"></div>
            </div>
        </section>
    </main>

    <div id="authModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <?php include 'includes/login-form.php'; ?>
        </div>
    </div>

    <script>
    document.getElementById('searchInput').addEventListener('input', function () {
        const query = this.value.trim();
        const suggestionsBox = document.getElementById('suggestions');

        if (query.length === 0) {
            suggestionsBox.innerHTML = '';
            return;
        }

        fetch('search_suggestions.php?q=' + encodeURIComponent(query))
            .then(response => response.text())
            .then(data => {
                suggestionsBox.innerHTML = data;
            })
            .catch(error => {
                console.error('Kļūda meklēšanas laikā:', error);
            });
    });
    </script>
</body>
</html>
