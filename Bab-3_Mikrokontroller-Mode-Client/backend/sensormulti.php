<?php
// --- LOAD ENV (Agar password database aman) ---
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

// --- KONEKSI DATABASE ---
$servername = $_ENV['DB_HOST'] ?? 'localhost';
$username   = $_ENV['DB_USERNAME'] ?? 'root';
$password   = $_ENV['DB_PASSWORD'] ?? '';
$dbname     = $_ENV['DB_DATABASE'] ?? 'eslolin';
$tableName  = $_ENV['DB_TABLE_MULTI'] ?? 'sensor';

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// --- QUERY DATA (Sesuai Buku Hal. 58) ---
// Mengambil semua data dari tabel sensor dan diurutkan berdasarkan idSensor
$sql = "SELECT * FROM $tableName ORDER BY idSensor ASC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Output data: menggabungkan status tiap baris dengan pemisah koma
    while($row = mysqli_fetch_assoc($result)) {
        // TARA FIX: Di buku tulisannya 'statussensor', tapi biasanya di DB itu 'statusSensor'
        // Cek tabel MySQL lu ya, kalau kecil semua tinggal ganti jadi statussensor
        echo $row["statusSensor"] . ","; 
    }
} else {
    echo "0 results";
}

mysqli_close($conn);
?>