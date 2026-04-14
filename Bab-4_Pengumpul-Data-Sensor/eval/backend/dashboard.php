<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>IoT Dashboard - Bab 4</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #1e1e1e;
            width: 85%;
            margin: auto;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        }
        h2 { color: #ffffff; font-weight: 500; }
        .status-box {
            display: inline-block;
            padding: 10px 20px;
            background-color: #333;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 1.1em;
        }
        canvas { max-height: 500px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Dashboard Sensor: Suhu, Kelembapan & Kecerahan</h2>
        <div class="status-box" id="status-sekarang">Memuat status...</div>
        <canvas id="iotChart"></canvas>
    </div>

    <script>
        const ctx = document.getElementById('iotChart').getContext('2d');
        // Set tema teks Chart.js jadi terang
        Chart.defaults.color = '#e0e0e0';

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Suhu (°C)',
                        borderColor: '#ff6b6b',
                        backgroundColor: 'rgba(255, 107, 107, 0.1)',
                        data: [],
                        yAxisID: 'y'
                    },
                    {
                        label: 'Kelembapan (%)',
                        borderColor: '#4ecdc4',
                        backgroundColor: 'rgba(78, 205, 196, 0.1)',
                        data: [],
                        yAxisID: 'y'
                    },
                    {
                        label: 'Lumen (ADC)',
                        borderColor: '#feca57',
                        backgroundColor: 'rgba(254, 202, 87, 0.1)',
                        data: [],
                        yAxisID: 'y1' // Lumen pakai sumbu Y sebelah kanan karena angkanya ribuan
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { type: 'linear', display: true, position: 'left', min: 0, max: 100 },
                    y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false } }
                }
            }
        });

        function fetchData() {
            $.ajax({
                url: 'get_data.php',
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    chart.data.labels = res.labels;
                    chart.data.datasets[0].data = res.suhu;
                    chart.data.datasets[1].data = res.kelembapan;
                    chart.data.datasets[2].data = res.lumen;
                    chart.update();
                    $('#status-sekarang').html("Status Cahaya: <b>" + res.status_terakhir + "</b>");
                }
            });
        }

        setInterval(fetchData, 2000); // Auto-load tiap 2 detik
    </script>
</body>
</html>