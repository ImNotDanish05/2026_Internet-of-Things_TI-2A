<?php
$env = parse_ini_file('.env');
$conn = mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME']);

if (!$conn) { die("Koneksi gagal: " . mysqli_connect_error()); }

// Ambil data khusus Sensor ID = 1 (Seperti contoh di modul)
$sql = "SELECT idSensor, namaSensor, statusSensor FROM bab5_sensor WHERE idSensor = 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $objData = new stdClass;
    while($row = mysqli_fetch_assoc($result)) {
        $objData->idSensor = $row["idSensor"];
        $objData->namaSensor = $row["namaSensor"];
        $objData->statusSensor = $row["statusSensor"];
    }
    
    // Data tambahan sesuai contoh di modul [suhu, lumen]
    $objData->add = ["36", "40"]; 
    
    // Print data dalam format JSON
    echo json_encode($objData, JSON_FORCE_OBJECT);
} else {
    echo "0 results";
}

mysqli_close($conn);
?>