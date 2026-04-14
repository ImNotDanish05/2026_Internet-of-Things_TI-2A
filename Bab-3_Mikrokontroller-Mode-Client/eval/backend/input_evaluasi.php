<?php
// 1. Fungsi untuk membaca .env (dimasukkan langsung ke sini agar aman)
function loadEnv($path) {
    if (!file_exists($path)) return false;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Panggil fungsi untuk meload variabel .env
loadEnv(__DIR__ . '/.env');

$host = $_ENV['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';
$dbname = $_ENV['DB_DATABASE'] ?? 'eslolin';

// 2. Lakukan Koneksi ke Database
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Cek jika koneksi gagal
if (!$conn) {
    die("❌ Koneksi Database Gagal!");
}

// 3. Menangkap data dari URL (GET Request) dari ESP32
$suhu = $_GET['suhu'] ?? null;
$ldr = $_GET['ldr'] ?? null;
$status = $_GET['status'] ?? null;

// 4. Proses Insert Data
if ($suhu !== null && $ldr !== null) {
    // Insert ke tabel suhu
    $sql_suhu = "INSERT INTO bab3_tabel_suhu (nilai_suhu) VALUES ('$suhu')";
    mysqli_query($conn, $sql_suhu);

    // Insert ke tabel cahaya
    $sql_cahaya = "INSERT INTO bab3_tabel_cahaya (nilai_ldr, status_cahaya) VALUES ('$ldr', '$status')";
    mysqli_query($conn, $sql_cahaya);

    echo "✅ Data Berhasil Disimpan!";
} else {
    echo "❌ Data Tidak Lengkap!";
}

// Tutup koneksi
mysqli_close($conn);
?>