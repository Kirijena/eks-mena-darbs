<?php
$serveris = "localhost";
$lietotajs = "grobina1_belovinceva";
$parole = "U9R1@kvzL";
$datubaze = "grobina1_belovinceva";
$savienojums = mysqli_connect($serveris, $lietotajs, $parole, $datubaze);

if (!$savienojums) {
    die("Savienojuma kļūda: " . mysqli_connect_error());
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($query !== '') {
    $stmt = mysqli_prepare($savienojums, "SELECT id, title FROM eksamens_entries WHERE published = 1 AND title LIKE CONCAT('%', ?, '%') LIMIT 10");
    mysqli_stmt_bind_param($stmt, "s", $query);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo '<ul>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<li onclick="location.href=\'mifalogija/entry.php?id=' . $row['id'] . '\'">' . htmlspecialchars($row['title']) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<div style="padding:10px; color: #92400e;">Nav atrasts neviens rezultāts.</div>';
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($savienojums);
?>