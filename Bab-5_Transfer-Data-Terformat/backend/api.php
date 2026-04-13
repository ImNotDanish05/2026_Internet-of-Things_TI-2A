<?php
// Baca konfigurasi dari file .env
$env = parse_ini_file('.env');
$host = $env['DB_HOST'];
$user = $env['DB_USER'];
$pass = $env['DB_PASS'];
$dbname = $env['DB_NAME'];

// Koneksi ke database
$conn = new mysqli($host, $user, $pass, $dbname);

// JIKA ESP32 MINTA DATA (BACA JSON)
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Sesuaikan nama tabel: bab5_sensor
    $sql = "SELECT * FROM bab5_sensor WHERE idsensor = 1";
    $result = $conn->query($sql);
    $data = $result->fetch_assoc();
    
    echo json_encode($data); 
}

// JIKA ESP32 KIRIM DATA SENSOR (SIMPAN KE DATABASE)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idSensor = $_POST['idSensor'];
    $nilai = $_POST['nilaiData'];
    
    // Sesuaikan nama tabel: bab5_data
    $sql = "INSERT INTO bab5_data (idSensor, nilaiData) VALUES ('$idSensor', '$nilai')";
    if($conn->query($sql) === TRUE) {
        echo "Data berhasil disimpan";
    }
}
$conn->close();
?>