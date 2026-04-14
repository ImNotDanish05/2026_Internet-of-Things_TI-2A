<?php
$env = parse_ini_file('.env');
$conn = mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME']);

if (!$conn) { die("Koneksi gagal: " . mysqli_connect_error()); }

// 1. Ambil data Lampu (idSensor = 1)
$sqlLampu = mysqli_query($conn, "SELECT idSensor, namaSensor, statusSensor FROM bab5_sensor WHERE idSensor = 1");
$lampu = mysqli_fetch_assoc($sqlLampu);

// 2. Ambil data Pompa (idSensor = 2)
$sqlPompa = mysqli_query($conn, "SELECT idSensor, namaSensor, statusSensor FROM bab5_sensor WHERE idSensor = 2");
$pompa = mysqli_fetch_assoc($sqlPompa);

// 3. Ambil data NilaiSensor (Kita ambil 1 data paling baru dari database Evaluasi Bab 4 kamu!)
$sqlSensor = mysqli_query($conn, "SELECT suhu, kelembapan, lumen as cahaya FROM bab4_datasensor ORDER BY id DESC LIMIT 1");
$nilaiSensor = mysqli_fetch_assoc($sqlSensor);

// Kalau misal tabel bab4 kosong, kita kasih nilai default sesuai buku
if (!$nilaiSensor) {
    $nilaiSensor = [
        "suhu" => 36,
        "kelembapan" => 40,
        "cahaya" => 120
    ];
} else {
    // Pastikan tipe datanya angka (karena dari database kadang terbaca string)
    $nilaiSensor['suhu'] = (float)$nilaiSensor['suhu'];
    $nilaiSensor['kelembapan'] = (float)$nilaiSensor['kelembapan'];
    $nilaiSensor['cahaya'] = (int)$nilaiSensor['cahaya'];
}

// 4. Susun menjadi Nested JSON (JSON Bersarang)
$response = [
    "Lampu" => $lampu,
    "Pompa" => $pompa,
    "NilaiSensor" => $nilaiSensor
];

// Cetak ke layar
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT); // JSON_PRETTY_PRINT bikin tampilannya rapi ke bawah

mysqli_close($conn);
?>