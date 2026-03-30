<?php
function loadEnv($path) {
    if (!file_exists($path)) return false;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}
loadEnv(__DIR__ . '/.env');

$host = $_ENV['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';
$db   = $_ENV['DB_DATABASE'] ?? 'eslolin';

// 1. Koneksi awal ke MySQL Server
$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
    die("❌ Koneksi Server Gagal: " . mysqli_connect_error() . "\n");
}

// 2. Bikin Database jika belum ada
$sql_db = "CREATE DATABASE IF NOT EXISTS $db";
if (mysqli_query($conn, $sql_db)) {
    echo "✅ Database '$db' siap!\n";
} else {
    die("❌ Gagal bikin Database: " . mysqli_error($conn) . "\n");
}

mysqli_select_db($conn, $db);

// 3. Bikin Tabel Sensor (Sesuai Bab 3)
// Pakai $_ENV['DB_TABLE'] biar sinkron sama .env lu
$tableName = $_ENV['DB_TABLE'] ?? 'sensor';
$sql_table = "CREATE TABLE IF NOT EXISTS $tableName (
    idSensor INT(11) AUTO_INCREMENT PRIMARY KEY,
    namaSensor VARCHAR(50) NOT NULL,
    statusSensor INT(1) NOT NULL DEFAULT 0,
    keterangan VARCHAR(100)
)";

if (mysqli_query($conn, $sql_table)) {
    echo "✅ Tabel '$tableName' berhasil dibuat!\n";
} else {
    die("❌ Gagal bikin Tabel: " . mysqli_error($conn) . "\n");
}

mysqli_close($conn);
?>