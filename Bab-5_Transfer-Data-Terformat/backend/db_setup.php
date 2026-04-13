<?php
// db_setup.php

// 1. Baca konfigurasi dari file .env
// Pastikan file .env berada di folder yang sama dengan file ini
$env = parse_ini_file('.env');
$host = $env['DB_HOST'];
$user = $env['DB_USER'];
$pass = $env['DB_PASS'];
$dbname = $env['DB_NAME'];

// 2. Buat koneksi ke MySQL server
$conn = new mysqli($host, $user, $pass);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// 3. Buat Database (IoTBab3) jika belum ada
$sql_db = "CREATE DATABASE IF NOT EXISTS `$dbname`";
if ($conn->query($sql_db) === TRUE) {
    echo "[OK] Database '$dbname' siap.<br>";
} else {
    die("Error membuat database: " . $conn->error);
}

// Pilih database yang akan digunakan
$conn->select_db($dbname);

// 4. Buat Tabel bab5_sensor
$sql_sensor = "CREATE TABLE IF NOT EXISTS `bab5_sensor` (
  `idsensor` int(11) NOT NULL PRIMARY KEY,
  `namasensor` varchar(32) NOT NULL,
  `statussensor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

if ($conn->query($sql_sensor) === TRUE) {
    echo "[OK] Tabel 'bab5_sensor' berhasil dibuat.<br>";
}

// 5. Buat Tabel bab5_data
$sql_data = "CREATE TABLE IF NOT EXISTS `bab5_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `idSensor` int(11) NOT NULL,
  `tglData` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nilaiData` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

if ($conn->query($sql_data) === TRUE) {
    echo "[OK] Tabel 'bab5_data' berhasil dibuat.<br>";
}

// 6. Masukkan Data Awal (Dummy Data)
// Gunakan IGNORE agar jika file dijalankan 2x, tidak error karena Primary Key ganda
$sql_insert = "INSERT IGNORE INTO `bab5_sensor` (`idsensor`, `namasensor`, `statussensor`) VALUES 
(1, 'Sensor Suhu DHT22', 1),
(2, 'Sensor Kelembaban', 0),
(3, 'Sensor Cahaya LDR', 1);";

if ($conn->query($sql_insert) === TRUE) {
    echo "[OK] Data awal berhasil dimasukkan ke tabel 'bab5_sensor'.<br>";
} else {
    echo "[ERROR] Gagal memasukkan data: " . $conn->error . "<br>";
}

echo "<br><strong>Setup Database Selesai! Kamu siap lanjut ke API! :D</strong>";

// Tutup koneksi
$conn->close();
?>