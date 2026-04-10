<?php
// Load config dari .env
$env = parse_ini_file('.env');

// 1. Koneksi awal ke MySQL (tanpa pilih DB dulu)
$conn = mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS']);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 2. Buat Database
$dbName = $env['DB_NAME'];
$sqlCreateDB = "CREATE DATABASE IF NOT EXISTS $dbName";
if (mysqli_query($conn, $sqlCreateDB)) {
    echo "✅ Database '$dbName' siap!<br>";
} else {
    die("❌ Gagal buat database: " . mysqli_error($conn));
}

// 3. Pilih Database
mysqli_select_db($conn, $dbName);

// 4. Buat Tabel datasensor
$sqlCreateTable = "CREATE TABLE IF NOT EXISTS datasensor (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    tglData DATETIME DEFAULT CURRENT_TIMESTAMP,
    suhu FLOAT NOT NULL,
    kelembapan FLOAT NOT NULL
)";

if (mysqli_query($conn, $sqlCreateTable)) {
    echo "✅ Tabel 'datasensor' siap!<br>";
} else {
    echo "❌ Gagal buat tabel: " . mysqli_error($conn);
}

mysqli_close($conn);

echo "<br><b>Semua setup selesai. Sekarang ESP32 sudah bisa kirim data!</b>";
?>