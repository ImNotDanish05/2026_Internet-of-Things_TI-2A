<?php
// Set header agar browser tahu ini adalah data JSON murni
header('Content-Type: application/json');

// Fungsi baca .env secara "silent" (tanpa echo)
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Panggil file .env
loadEnv(__DIR__ . '/.env');

$host = $_ENV['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';
$dbname = $_ENV['DB_DATABASE'] ?? 'eslolin';

$conn = mysqli_connect($host, $user, $pass, $dbname);

// Mengambil 10 data terakhir dari tabel
$querySuhu = mysqli_query($conn, "SELECT * FROM bab3_tabel_suhu ORDER BY id DESC LIMIT 10");
$queryCahaya = mysqli_query($conn, "SELECT * FROM bab3_tabel_cahaya ORDER BY id DESC LIMIT 10");

$labels = [];
$dataSuhu = [];
$dataLdr = [];
$lastStatus = "-";

$suhuRows = [];
while($row = mysqli_fetch_assoc($querySuhu)) { $suhuRows[] = $row; }
$suhuRows = array_reverse($suhuRows); // Dibalik agar urutan waktunya dari kiri ke kanan

$cahayaRows = [];
while($row = mysqli_fetch_assoc($queryCahaya)) { $cahayaRows[] = $row; }
$cahayaRows = array_reverse($cahayaRows);

for($i = 0; $i < count($suhuRows); $i++) {
    $labels[] = date('H:i:s', strtotime($suhuRows[$i]['waktu']));
    $dataSuhu[] = $suhuRows[$i]['nilai_suhu'];
    
    if(isset($cahayaRows[$i])) {
        $dataLdr[] = $cahayaRows[$i]['nilai_ldr'];
        $lastStatus = $cahayaRows[$i]['status_cahaya'];
    }
}

// Cetak hasil murni sebagai JSON
echo json_encode([
    'labels' => $labels,
    'suhu' => $dataSuhu,
    'ldr' => $dataLdr,
    'last_status' => $lastStatus
]);

mysqli_close($conn);
?>