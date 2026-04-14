<?php
// Load environment variables secara manual (simpel)
$env = parse_ini_file('.env');

// Tangkap 4 parameter dari URL
$dataHumidity = $_GET['humidity'] ?? 0;
$dataSuhu = $_GET['suhu'] ?? 0;
$dataLumen = $_GET['lumen'] ?? 0;
$dataStatus = $_GET['status'] ?? '';

// Koneksi Database
$conn = mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME']);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Gunakan Prepared Statements ke tabel bab4_datasensor
$sql = "INSERT INTO bab4_datasensor (tglData, suhu, kelembapan, lumen, status_cahaya) VALUES (NOW(), ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

// d = double (float), i = integer, s = string
// Karena ada suhu(d), humid(d), lumen(i), status(s) -> jadinya "ddis"
mysqli_stmt_bind_param($stmt, "ddis", $dataSuhu, $dataHumidity, $dataLumen, $dataStatus);

if (mysqli_stmt_execute($stmt)) {
    echo "✅ Data masuk: Suhu $dataSuhu, Hum $dataHumidity, Lumen $dataLumen ($dataStatus)";
} else {
    echo "❌ Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>