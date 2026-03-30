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

// Koneksi Database
$conn = mysqli_connect(
    $_ENV['DB_HOST'], 
    $_ENV['DB_USERNAME'], 
    $_ENV['DB_PASSWORD'], 
    $_ENV['DB_DATABASE']
);

if (!$conn) {
    die("❌ Koneksi Gagal: " . mysqli_connect_error());
}

// Hapus data lama biar idSensor balik lagi dari 1 (Reset)
mysqli_query($conn, "TRUNCATE TABLE " . $_ENV['DB_TABLE']);

// Insert Data Dummy (Pastikan nama tabel di .env bener ya, misal: sensor)
$tableName = $_ENV['DB_TABLE'];
$sql_insert = "INSERT INTO $tableName (namaSensor, statusSensor, keterangan) VALUES 
    ('LED Teras', 0, 'Indikator Single'),
    ('LED Ruang Tamu', 1, 'Multi 2'),
    ('LED Kamar', 0, 'Multi 3'),
    ('LED Dapur', 1, 'Multi 4')";

if (mysqli_query($conn, $sql_insert)) {
    echo "🚀 Data berhasil di-push! Siap buat praktek Bab 3.\n";
} else {
    echo "❌ Gagal push data: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
?>