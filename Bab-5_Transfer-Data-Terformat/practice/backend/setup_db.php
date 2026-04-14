<?php
$env = parse_ini_file('.env');

// Koneksi tanpa pilih DB dulu
$conn = mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS']);
if (!$conn) { die("❌ Koneksi gagal: " . mysqli_connect_error()); }

$dbName = $env['DB_NAME'];
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $dbName");
mysqli_select_db($conn, $dbName);

// 1. Buat Tabel Sensor (Primary Key)
$sqlSensor = "CREATE TABLE IF NOT EXISTS bab5_sensor (
    idSensor INT PRIMARY KEY,
    namaSensor VARCHAR(32) NOT NULL,
    statusSensor INT NOT NULL
)";
mysqli_query($conn, $sqlSensor);

// 2. Buat Tabel Data (Foreign Key ke Tabel Sensor)
$sqlData = "CREATE TABLE IF NOT EXISTS bab5_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idSensor INT NOT NULL,
    tglData DATETIME DEFAULT CURRENT_TIMESTAMP,
    nilaiData FLOAT NOT NULL,
    FOREIGN KEY (idSensor) REFERENCES bab5_sensor(idSensor) ON DELETE CASCADE
)";
mysqli_query($conn, $sqlData);

// 3. Seeder (Suntik Data Dummy)
$cek = mysqli_query($conn, "SELECT COUNT(*) AS total FROM bab5_sensor");
$row = mysqli_fetch_assoc($cek);

if ($row['total'] == 0) {
    // Insert ke tabel sensor
    mysqli_query($conn, "INSERT INTO bab5_sensor (idSensor, namaSensor, statusSensor) VALUES 
        (1, 'LED 1', 1), 
        (2, 'LED 2', 0), 
        (5, 'LUMEN', 1), 
        (6, 'SUHU', 1)");
    
    // Insert ke tabel data (Sebagai sampel data history)
    mysqli_query($conn, "INSERT INTO bab5_data (idSensor, nilaiData) VALUES 
        (5, 120), (6, 36.5), (5, 150), (6, 37.0)");
        
    echo "✅ Mantap! Database, Tabel bab5_sensor, bab5_data, dan Seeder berhasil dibuat!";
} else {
    echo "⏩ Tabel sudah ada dan terisi data, seeder di-skip biar aman.";
}

mysqli_close($conn);
?>