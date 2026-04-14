<?php
$env = parse_ini_file('.env');
$conn = mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME']);

$query = mysqli_query($conn, "SELECT * FROM bab4_datasensor ORDER BY id DESC LIMIT 15");

$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}
$data = array_reverse($data); // Balik urutan dari kiri (lama) ke kanan (baru)

$response = [
    'labels' => [],
    'suhu' => [],
    'kelembapan' => [],
    'lumen' => [],
    'status_terakhir' => '-'
];

foreach ($data as $row) {
    $response['labels'][] = date('H:i:s', strtotime($row['tglData']));
    $response['suhu'][] = $row['suhu'];
    $response['kelembapan'][] = $row['kelembapan'];
    $response['lumen'][] = $row['lumen'];
    $response['status_terakhir'] = $row['status_cahaya'];
}

echo json_encode($response);
mysqli_close($conn);
?>