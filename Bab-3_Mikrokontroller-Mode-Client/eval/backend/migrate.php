<?php

// Fungsi sederhana untuk membaca file .env
function loadEnv($path) {
    if (!file_exists($path)) {
        die("❌ File .env tidak ditemukan di: " . $path . "\n");
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Abaikan komentar
        if (strpos(trim($line), '#') === 0) continue;
        // Pisahkan key dan value
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// 1. Load konfigurasi dari .env
loadEnv(__DIR__ . '/.env');

$host = $_ENV['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';
$dbname = $_ENV['DB_DATABASE'] ?? 'eslolin';

// 2. Koneksi awal ke MySQL (tanpa memilih database khusus)
$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
    die("❌ Koneksi MySQL gagal: " . mysqli_connect_error() . "\n");
}
echo "✅ Koneksi ke MySQL berhasil.\n";

// 3. Buat Database jika belum ada
$sql_db = "CREATE DATABASE IF NOT EXISTS $dbname";
if (mysqli_query($conn, $sql_db)) {
    echo "✅ Database '$dbname' siap digunakan.\n";
} else {
    die("❌ Error membuat database: " . mysqli_error($conn) . "\n");
}

// Pilih database yang akan digunakan
mysqli_select_db($conn, $dbname);

// 4. Migrasi: Buat Tabel Suhu
$sql_tabel_suhu = "CREATE TABLE IF NOT EXISTS bab3_tabel_suhu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nilai_suhu FLOAT NOT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $sql_tabel_suhu)) {
    echo "✅ Tabel 'bab3_tabel_suhu' berhasil disiapkan.\n";
} else {
    echo "❌ Error membuat tabel suhu: " . mysqli_error($conn) . "\n";
}

// 5. Migrasi: Buat Tabel Cahaya
$sql_tabel_cahaya = "CREATE TABLE IF NOT EXISTS bab3_tabel_cahaya (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nilai_ldr INT NOT NULL,
    status_cahaya VARCHAR(20) NOT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $sql_tabel_cahaya)) {
    echo "✅ Tabel 'bab3_tabel_cahaya' berhasil disiapkan.\n";
} else {
    echo "❌ Error membuat tabel cahaya: " . mysqli_error($conn) . "\n";
}

// 6. Seeder: Masukkan data awal (dummy)
echo "----------------------------------------\n";
echo "Menjalankan Seeder Data...\n";

// Seeder Tabel Suhu
$cek_suhu = mysqli_query($conn, "SELECT COUNT(*) AS total FROM bab3_tabel_suhu");
$row_suhu = mysqli_fetch_assoc($cek_suhu);
if ($row_suhu['total'] == 0) {
    $seed_suhu = "INSERT INTO bab3_tabel_suhu (nilai_suhu) VALUES 
        (28.5), (29.1), (30.5), (29.8), (27.4)";
    mysqli_query($conn, $seed_suhu);
    echo "🌱 Seeder 'bab3_tabel_suhu' berhasil disuntikkan.\n";
} else {
    echo "⏩ Tabel 'bab3_tabel_suhu' sudah ada datanya, seeder di-skip.\n";
}

// Seeder Tabel Cahaya
$cek_cahaya = mysqli_query($conn, "SELECT COUNT(*) AS total FROM bab3_tabel_cahaya");
$row_cahaya = mysqli_fetch_assoc($cek_cahaya);
if ($row_cahaya['total'] == 0) {
    // Ingat logika ESP32 kamu: Nilai besar = Gelap, Nilai kecil = Terang
    $seed_cahaya = "INSERT INTO bab3_tabel_cahaya (nilai_ldr, status_cahaya) VALUES 
        (3800, 'Gelap'), 
        (2100, 'Redup'), 
        (1200, 'Cerah'), 
        (450, 'Terang')";
    mysqli_query($conn, $seed_cahaya);
    echo "🌱 Seeder 'bab3_tabel_cahaya' berhasil disuntikkan.\n";
} else {
    echo "⏩ Tabel 'bab3_tabel_cahaya' sudah ada datanya, seeder di-skip.\n";
}

echo "----------------------------------------\n";
echo "🚀 MANTAP! Database, Tabel, dan Data siap digunakan :D\n";

mysqli_close($conn);
?>