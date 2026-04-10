<?php
// Load environment variables secara manual (simpel)
$env = parse_ini_file('.env');

$dataHumidity = $_GET['humidity'] ?? 0;
$dataSuhu = $_GET['suhu'] ?? 0;

// Koneksi Database
$conn = mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME']);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Gunakan Prepared Statements supaya aman dari SQL Injection
$sql = "INSERT INTO datasensor (tglData, suhu, kelembapan) VALUES (NOW(), ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "dd", $dataSuhu, $dataHumidity);

if (mysqli_stmt_execute($stmt)) {
    echo "Data berhasil disimpan: Suhu $dataSuhu, Humidity $dataHumidity";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>