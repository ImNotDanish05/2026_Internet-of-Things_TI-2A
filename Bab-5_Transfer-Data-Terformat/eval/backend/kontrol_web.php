<?php
// --- BAGIAN BACKEND: Menangani Request Update dari Tombol ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
    $env = parse_ini_file('.env');
    $conn = mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME']);
    
    $id = (int)$_POST['id'];
    $status = (int)$_POST['status'];
    
    // Update status di database
    $sql = "UPDATE bab5_sensor SET statusSensor = $status WHERE idSensor = $id";
    mysqli_query($conn, $sql);
    mysqli_close($conn);
    exit; // Stop eksekusi PHP di sini khusus untuk request POST AJAX
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Smart Home Control - Bab 5</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #121212; color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-align: center; padding: 40px; margin: 0;
        }
        .container {
            background-color: #1e1e1e; width: 60%; margin: auto;
            padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }
        .control-panel {
            display: flex; justify-content: space-around; margin-top: 30px;
        }
        .card {
            background-color: #2a2a2a; padding: 20px; border-radius: 10px; width: 40%;
        }
        button {
            padding: 15px 30px; font-size: 1.2em; font-weight: bold; border: none;
            border-radius: 8px; cursor: pointer; transition: 0.3s; margin-top: 15px;
            color: white; width: 100%;
        }
        /* Warna Tombol Dinamis */
        .btn-on { background-color: #4CAF50; } /* Hijau */
        .btn-on:hover { background-color: #45a049; }
        .btn-off { background-color: #f44336; } /* Merah */
        .btn-off:hover { background-color: #da190b; }
        
        .sensor-data {
            margin-top: 30px; padding-top: 20px; border-top: 1px solid #444;
            display: flex; justify-content: space-around; font-size: 1.2em;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🏠 Smart Home Dashboard</h2>
    
    <div class="control-panel">
        <div class="card">
            <h3>💡 Lampu (LED 1)</h3>
            <div id="status-lampu" style="font-size:1.5em; margin-bottom:10px;">-</div>
            <button id="btn-lampu" onclick="toggleAlat(1, statusLampuSekarang)">Memuat...</button>
        </div>
        
        <div class="card">
            <h3>💧 Pompa (LED 2)</h3>
            <div id="status-pompa" style="font-size:1.5em; margin-bottom:10px;">-</div>
            <button id="btn-pompa" onclick="toggleAlat(2, statusPompaSekarang)">Memuat...</button>
        </div>
    </div>

    <div class="sensor-data">
        <div>🌡️ Suhu: <b id="val-suhu">0</b> °C</div>
        <div>💧 Humid: <b id="val-humid">0</b> %</div>
        <div>☀️ Kecerahan: <b id="val-cahaya">0</b></div>
    </div>
</div>

<script>
    // Variabel penyimpan status saat ini
    let statusLampuSekarang = 0;
    let statusPompaSekarang = 0;

    // Fungsi untuk narik data JSON dari API kamu
    function loadData() {
        $.ajax({
            url: 'api.php', // Tembak ke API yang barusan kamu buat
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Update Variabel
                statusLampuSekarang = parseInt(data.Lampu.statusSensor);
                statusPompaSekarang = parseInt(data.Pompa.statusSensor);

                // Update UI Lampu
                if(statusLampuSekarang === 1) {
                    $('#status-lampu').text('MENYALA').css('color', '#4CAF50');
                    $('#btn-lampu').text('MATIKAN').removeClass('btn-on').addClass('btn-off');
                } else {
                    $('#status-lampu').text('MATI').css('color', '#f44336');
                    $('#btn-lampu').text('NYALAKAN').removeClass('btn-off').addClass('btn-on');
                }

                // Update UI Pompa
                if(statusPompaSekarang === 1) {
                    $('#status-pompa').text('MENYALA').css('color', '#4CAF50');
                    $('#btn-pompa').text('BERHENTI').removeClass('btn-on').addClass('btn-off');
                } else {
                    $('#status-pompa').text('BERHENTI').css('color', '#f44336');
                    $('#btn-pompa').text('NYALAKAN').removeClass('btn-off').addClass('btn-on');
                }

                // Update Nilai Sensor Bawah
                $('#val-suhu').text(data.NilaiSensor.suhu);
                $('#val-humid').text(data.NilaiSensor.kelembapan);
                $('#val-cahaya').text(data.NilaiSensor.cahaya);
            }
        });
    }

    // Fungsi untuk mengubah status di Database via AJAX
    function toggleAlat(idSensor, currentStatus) {
        // Balikkan status (Kalau 1 jadi 0, kalau 0 jadi 1)
        let newStatus = currentStatus === 1 ? 0 : 1; 

        $.ajax({
            url: 'kontrol_web.php', // Nembak ke dirinya sendiri (bagian PHP atas)
            type: 'POST',
            data: { id: idSensor, status: newStatus },
            success: function() {
                loadData(); // Langsung reload data biar UI terupdate instan
            }
        });
    }

    // Load pertama kali, dan auto-load setiap 2 detik
    $(document).ready(function() {
        loadData();
        setInterval(loadData, 2000);
    });
</script>

</body>
</html>