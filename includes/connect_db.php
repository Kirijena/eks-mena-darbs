<?php
$serveris = "localhost";
$lietotajs = "grobina1_belovinceva";
$parole = "U9R1@kvzL";
$datubaze = "grobina1_belovinceva";

// Create connection
$savienojums = mysqli_connect($serveris, $lietotajs, $parole, $datubaze);

// Check connection and handle errors
if (!$savienojums) {
    die("Savienojuma kļūda: " . mysqli_connect_error());
}

// Set character set to UTF-8
mysqli_set_charset($savienojums, "utf8mb4");
?>
