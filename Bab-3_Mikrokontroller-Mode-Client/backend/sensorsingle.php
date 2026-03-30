<?php
// --- TARA'S FIX: Load Environment Variables ---
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

// --- Koneksi Database (Ambil dari .env) ---
$servername = $_ENV['DB_HOST'] ?? 'localhost';
$username   = $_ENV['DB_USERNAME'] ?? 'root';
$password   = $_ENV['DB_PASSWORD'] ?? '';
$dbname     = $_ENV['DB_DATABASE'] ?? 'eslolin';
$tableName  = $_ENV['DB_TABLE_SINGLE'] ?? 'sensor';

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Cek Koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// --- Query Data (Sesuai Buku Hal. 48) ---
// Kita ambil nilai sensor dengan idSensor = 1
$sql = "SELECT * FROM $tableName WHERE idSensor = 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Output data: cuma kirim angka statusnya saja (0 atau 1)
    while($row = mysqli_fetch_assoc($result)) {
        // TARA FIX: Pastikan case-sensitive kolom database bener (statusSensor)
        echo $row["statusSensor"]; 
    }
} else {
    echo "0 results";
}

mysqli_close($conn);
?>