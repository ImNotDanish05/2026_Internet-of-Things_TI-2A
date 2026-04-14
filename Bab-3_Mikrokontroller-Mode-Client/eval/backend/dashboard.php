<!DOCTYPE html>
<html>
<head>
    <title>IoT Dashboard Bab 3</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: sans-serif; text-align: center; background: #f4f4f4; }
        .container { width: 80%; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        canvas { max-width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Monitoring Suhu & Cahaya (Real-time)</h2>
        <div id="status-sekarang">Memuat data...</div>
        <canvas id="iotChart"></canvas>
    </div>

    <script>
        var ctx = document.getElementById('iotChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Suhu (°C)',
                    borderColor: 'rgb(255, 99, 132)',
                    data: []
                }, {
                    label: 'LDR Value',
                    borderColor: 'rgb(54, 162, 235)',
                    data: []
                }]
            }
        });

        function updateData() {
            $.ajax({
                url: 'get_data.php', // Buat file PHP ini untuk mengambil 10 data terakhir dari DB
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    chart.data.labels = response.labels;
                    chart.data.datasets[0].data = response.suhu;
                    chart.data.datasets[1].data = response.ldr;
                    chart.update();
                    $('#status-sekarang').html("Status Cahaya Terakhir: <b>" + response.last_status + "</b>");
                }
            });
        }

        setInterval(updateData, 2000); // Update setiap 2 detik
    </script>
</body>
</html>