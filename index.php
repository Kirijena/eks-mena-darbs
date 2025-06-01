<?php
session_start();
$current_page = 'index';

$serveris = "localhost";
$lietotajs = "grobina1_belovinceva";
$parole = "U9R1@kvzL";
$datubaze = "grobina1_belovinceva";

$savienojums = mysqli_connect($serveris, $lietotajs, $parole, $datubaze);
if (!$savienojums) {
    die("Savienojuma kļūda: " . mysqli_connect_error());
}
mysqli_set_charset($savienojums, "utf8mb4");

$sql_entries_count = "SELECT COUNT(*) AS count FROM eksamens_entries";
$result_entries = mysqli_query($savienojums, $sql_entries_count);
$entries_count = 0;
if ($result_entries) {
    $row = mysqli_fetch_assoc($result_entries);
    $entries_count = $row['count'];
}

$sql_types_count = "SELECT COUNT(*) AS count FROM eksamens_categories";
$result_types = mysqli_query($savienojums, $sql_types_count);
$types_count = 0;
if ($result_types) {
    $row = mysqli_fetch_assoc($result_types);
    $types_count = $row['count'];
}

$sql_categories_count = "SELECT COUNT(*) AS count FROM eksamens_kategorija";
$result_categories = mysqli_query($savienojums, $sql_categories_count);
$categories_count = 0;
if ($result_categories) {
    $row = mysqli_fetch_assoc($result_categories);
    $categories_count = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Senās Teikas - Mitoloģijas Enciklopēdija</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="style.css" />
    <style>
        main {
            max-width: 1200px; /* увеличил ширину главного контента */
            margin: 40px auto;
            padding: 0 20px; /* отступы по бокам */
        }
        .intro-section {
            background: var(--color-surface);
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 40px;
        }
        .intro-section h1, .intro-section h2 {
            color: var(--color-primary);
            font-family: 'Cinzel', serif;
            margin-bottom: 20px;
        }
        .intro-section p {
            font-size: 1.1rem;
            line-height: 1.5;
            margin-bottom: 30px;
            color: var(--color-text);
        }
        .btn-categories {
            display: inline-block;
            padding: 12px 30px;
            background: var(--color-primary);
            color: white;
            font-size: 1.1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .btn-categories:hover {
            background: var(--color-secondary);
        }
        .stats-section {
            display: flex;
            justify-content: space-between;
            color: var(--color-text);
            margin-bottom: 60px;
        }
        .stat-box {
            background: rgba(146, 64, 14, 0.05);
            border-radius: 8px;
            padding: 20px 25px;
            flex: 1;
            margin: 0 10px;
            text-align: center;
            font-size: 1.1rem;
            font-weight: 600;
            box-shadow: 0 1px 5px rgba(0,0,0,0.1);
            transition: background-color 0.3s ease;
        }
        .stat-box:first-child {
            margin-left: 0;
        }
        .stat-box:last-child {
            margin-right: 0;
        }
        .stat-box i {
            color: var(--color-primary);
            margin-bottom: 10px;
            display: block;
            font-size: 1.8rem;
        }
        .stat-number {
            font-size: 2rem;
            margin-top: 8px;
            color: var(--color-text-light);
        }
        .stat-box:hover {
            background-color: var(--color-background);
        }
        footer {
            width: 100%;
            background-color: var(--color-primary);
            color: white;
            padding: 30px 20px;
            box-sizing: border-box;
        }
        footer .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        footer a {
            color: var(--color-warning);
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
        footer .social-icons a {
            margin: 0 8px;
            color: var(--color-warning);
            font-size: 1.5rem;
            transition: color 0.3s ease;
        }
        footer .social-icons a:hover {
            color: white;
        }
        footer h2 {
            margin-bottom: 15px;
        }
        footer p {
            margin-bottom: 10px;
        }
        /* Адаптивность для мобильных */
        @media (max-width: 768px) {
            .stats-section {
                flex-direction: column;
                gap: 20px;
                margin-bottom: 40px;
            }
            .stat-box {
                margin: 0;
            }
            main {
                margin: 20px auto;
                padding: 0 15px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="intro-section">
            <h1>Senās Teikas - Mitoloģijas Enciklopēdija</h1>
            <p>
                Šis vietne ir veltīta mitoloģijas pētniecībai un iepazīšanai.<br>  
                Te jūs atradīsiet informāciju par dažādām mitoloģijām, to dieviem, leģendām, svētajām vietām un tradīcijām.<br>  
                Izpētiet pasaules mitoloģiju bagātības un atklājiet senos stāstus, kas iedvesmojuši cilvēci gadsimtiem ilgi.
            </p>
            <a href="mifalogija/template.php" class="btn-categories">Apskatīt mitoloģiju kategorijas</a>
        </section>

        <section class="stats-section" aria-label="Statistika">
            <div class="stat-box" role="region" aria-labelledby="entries-label">
                <i class="fas fa-file-lines" aria-hidden="true"></i>
                <div id="entries-label">Ieraksti kopā</div>
                <div class="stat-number"><?php echo $entries_count; ?></div>
            </div>
            <div class="stat-box" role="region" aria-labelledby="types-label">
                <i class="fas fa-list-alt" aria-hidden="true"></i>
                <div id="types-label">Tipi kopā</div>
                <div class="stat-number"><?php echo $types_count; ?></div>
            </div>
            <div class="stat-box" role="region" aria-labelledby="categories-label">
                <i class="fas fa-folder-open" aria-hidden="true"></i>
                <div id="categories-label">Kategorijas kopā</div>
                <div class="stat-number"><?php echo $categories_count; ?></div>
            </div>
        </section>

        <section class="intro-section" id="searchInput">
            <h2>Meklēšanas funkcija</h2>
            <p>
                Meklēšanas funkcija ļauj ātri un ērti atrast interesējošās teikas un mitoloģiskos stāstus mūsu datubāzē.<br> 
                Ievadiet meklējamo vārdu meklēšanas laukā un saņemiet tūlītējus ieteikumus, kas palīdzēs atrast precīzāko informāciju.
            </p>
            <a href="search.php" class="btn-categories">Pāriet uz meklēšanu</a>
        </section>
    </main>

    <footer>
        <div class="footer-container">
            <h2>Par mums</h2>
            <p>
                Mitoloģijas Enciklopēdija — jūsu uzticamais avots senajiem stāstiem un leģendām.<br>
                Mēs esam neliela komanda, kas mīl un vēlas dalīties ar zināšanām par pasaules mitoloģiju.
            </p>
            <p>
                <strong>Kontaktinformācija:</strong><br/>
                E-pasts: <a href="mailto:info@mitologija.lv">info@mitologija.lv</a><br/>
                Tālr.: <a href="tel:+37112345678">+371 123 45678</a><br/>
                Adrese: Brīvības iela 10, Rīga, Latvija
            </p>
            <p class="social-icons">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            </p>
            <p>© 2025 Senās Teikas. Visas tiesības aizsargātas.</p>
        </div>
    </footer>
</body>
</html>
