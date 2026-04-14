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

// 4. Buat Tabel EVALUASI Bab 4 (Ketambahan lumen & status)
$sqlCreateTable = "CREATE TABLE IF NOT EXISTS bab4_datasensor (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    tglData DATETIME DEFAULT CURRENT_TIMESTAMP,
    suhu FLOAT NOT NULL,
    kelembapan FLOAT NOT NULL,
    lumen INT NOT NULL,
    status_cahaya VARCHAR(20)
)";

if (mysqli_query($conn, $sqlCreateTable)) {
    echo "✅ Tabel 'bab4_datasensor' siap!<br>";
} else {
    echo "❌ Gagal buat tabel: " . mysqli_error($conn);
}

// 5. Seeder (Opsional, buat ngetes dashboard entar)
$cek_data = mysqli_query($conn, "SELECT COUNT(*) AS total FROM bab4_datasensor");
$row = mysqli_fetch_assoc($cek_data);
if ($row['total'] == 0) {
    $seed = "INSERT INTO bab4_datasensor (suhu, kelembapan, lumen, status_cahaya) VALUES 
        (28.5, 65.2, 1200, 'Cerah'), 
        (29.1, 64.0, 3500, 'Gelap'), 
        (30.0, 60.5, 400, 'Terang')";
    mysqli_query($conn, $seed);
    echo "🌱 Data dummy (Seeder) berhasil disuntikkan!<br>";
}

mysqli_close($conn);

echo "<br><b>Semua setup database Bab 4 selesai. Gas lanjut!</b>";
?>