<?php
// Подключение к базе данных
$serveris = "localhost";
$lietotajs = "grobina1_belovinceva";
$parole = "U9R1@kvzL";
$datubaze = "grobina1_belovinceva";

$savienojums = mysqli_connect($serveris, $lietotajs, $parole, $datubaze);
if (!$savienojums) {
    die("Savienojuma kļūda: " . mysqli_connect_error());
}
mysqli_set_charset($savienojums, "utf8mb4");

// Получаем название мифологии из URL
$nosaukums = isset($_GET['mitologija']) ? $_GET['mitologija'] : '';

// Получаем информацию о выбранной мифологии и её type_id
$vaicajums = $savienojums->prepare("SELECT id, Nosaukums, type_id, description FROM eksamens_categories WHERE Nosaukums = ?");
$vaicajums->bind_param("s", $nosaukums);
$vaicajums->execute();
$rezultats = $vaicajums->get_result();

$atrasta_mitologija = null;
$entries = [];
if ($rezultats && $rezultats->num_rows > 0) {
    $atrasta_mitologija = $rezultats->fetch_assoc();
    $type_id = $atrasta_mitologija['type_id'];
    // Получаем все записи из eksamens_entries с этим type_id
    $entries_query = $savienojums->prepare("SELECT * FROM eksamens_entries WHERE type_id = ? AND published = 1");
    $entries_query->bind_param("i", $type_id);
    $entries_query->execute();
    $entries_result = $entries_query->get_result();
    while ($row = $entries_result->fetch_assoc()) {
        $entries[] = $row;
    }
    $entries_query->close();
}
$vaicajums->close();
mysqli_close($savienojums);
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($atrasta_mitologija['Nosaukums'] ?? "Mitoloģija"); ?></title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .mitologija-nosaukums {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2C1810;
            text-align: center;
        }
        .saturs-ietvars {
            border: 2px solid #92400e;
            padding: 25px;
            margin-bottom: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .entries-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .entry-card {
            border: 2px solid #92400e;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .entry-card:hover {
            transform: translateY(-5px);
        }
        .entry-title {
            font-size: 20px;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 10px;
        }
        .entry-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .entry-country {
            color: #92400e;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .entry-meta {
            font-size: 13px;
            color: #888;
            margin-bottom: 5px;
        }
        .no-entries {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="content">
        <?php if ($atrasta_mitologija): ?>
            <div class="mitologija-nosaukums"><?php echo htmlspecialchars($atrasta_mitologija['Nosaukums']); ?></div>
            <div class="saturs-ietvars">
                <h2>Apraksts</h2>
                <p><?php echo htmlspecialchars($atrasta_mitologija['description'] ?? 'Apraksts nav pieejams.'); ?></p>
            </div>
            <h2>Saistītie ieraksti</h2>
            <?php if (!empty($entries)): ?>
                <div class="entries-grid">
                    <?php foreach ($entries as $entry): ?>
                        <div class="entry-card">
                            <div class="entry-title"><?php echo htmlspecialchars($entry['title']); ?></div>
                            <div class="entry-country">Valsts: <?php echo htmlspecialchars($entry['country']); ?></div>
                            <div class="entry-description"><?php echo htmlspecialchars($entry['description']); ?></div>
                            <?php if (!empty($entry['first_mention_date'])): ?>
                                <div class="entry-meta">Pirmā pieminēšana: <?php echo htmlspecialchars($entry['first_mention_date']); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($entry['description_text'])): ?>
                                <div class="entry-meta">Papildu apraksts: <?php echo htmlspecialchars($entry['description_text']); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-entries">
                    <p>Nav atrasts nevienu saistītu ierakstu.</p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-entries">
                <p>Mitoloģija nav atrasta vai nav norādīta.</p>
            </div>
        <?php endif; ?>
    </main>

    <script src="../script.js"></script>
</body>
</html>
