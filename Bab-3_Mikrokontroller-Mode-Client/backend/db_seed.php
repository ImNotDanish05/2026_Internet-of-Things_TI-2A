<?php
// (Load Env sama kayak di atas)
loadEnv(__DIR__ . '/.env');

$conn = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);

// Hapus data lama biar gak numpuk pas di-refresh
mysqli_query($conn, "TRUNCATE TABLE sensor");

// Insert Data buat Single (ID 1) dan Multi (ID 1-4)
$sql_insert = "INSERT INTO sensor (namaSensor, statusSensor, keterangan) VALUES 
    ('LED Teras', 0, 'Indikator Single'),
    ('LED Ruang Tamu', 1, 'Multi 2'),
    ('LED Kamar', 0, 'Multi 3'),
    ('LED Dapur', 1, 'Multi 4')";

if (mysqli_query($conn, $sql_insert)) {
    echo "🚀 Data berhasil di-push! Siap buat praktek Bab 3.";
}

mysqli_close($conn);
?>