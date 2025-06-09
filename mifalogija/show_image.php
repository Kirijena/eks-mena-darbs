<?php
$serveris = "localhost";
$lietotajs = "grobina1_belovinceva";
$parole = "U9R1@kvzL";
$datubaze = "grobina1_belovinceva";

$savienojums = mysqli_connect($serveris, $lietotajs, $parole, $datubaze);
if (!$savienojums) {
    die("Savienojuma kļūda: " . mysqli_connect_error());
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT images FROM eksamens_entries WHERE id = ?";
$stmt = mysqli_prepare($savienojums, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $imageData = $row['images'];
    
    // Определим тип изображения (можно сохранить в базе MIME-типы отдельно, но тут авто-определение)
    $finfo = finfo_open();
    $mime_type = finfo_buffer($finfo, $imageData, FILEINFO_MIME_TYPE);
    finfo_close($finfo);

    header("Content-Type: " . $mime_type);
    echo $imageData;
} else {
    http_response_code(404);
    echo "Attēls nav atrasts.";
}

mysqli_stmt_close($stmt);
mysqli_close($savienojums);
?>
