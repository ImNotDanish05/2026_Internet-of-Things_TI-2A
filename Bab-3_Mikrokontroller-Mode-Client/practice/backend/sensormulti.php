<?php
// --- LOAD ENV ---
function loadEnv($path) {
    if (!file_exists($path)) return false;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
}
loadEnv(__DIR__ . '/.env');

// --- KONEKSI DATABASE ---
$servername = $_ENV['DB_HOST'] ?? 'localhost';
$username   = $_ENV['DB_USERNAME'] ?? 'root';
$password   = $_ENV['DB_PASSWORD'] ?? '';
$dbname     = $_ENV['DB_DATABASE'] ?? 'eslolin';
$tableName  = $_ENV['DB_TABLE'] ?? 'sensor';

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// --- QUERY DATA ---
$sql = "SELECT statusSensor FROM $tableName ORDER BY idSensor ASC LIMIT 4";
$result = mysqli_query($conn, $sql);

$statusArray = []; // Wadah buat nampung angka status

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $statusArray[] = $row["statusSensor"]; // Masukin ke array
    }
    echo implode(",", $statusArray);
} else {
    echo "0 results";
}

mysqli_close($conn);
?>