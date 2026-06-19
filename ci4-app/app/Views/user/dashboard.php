<?= $this->extend('layout/user') ?>

<?= $this->section('title') ?>Dashboard Monitoring<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold">Monitoring Kondisi Lingkungan</h1>
        <p class="text-sm text-gray-400">Tampilan Data Sensor (Read-Only)</p>
    </div>
</header>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="glass-panel p-6 relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 text-neonBlue opacity-10"><i class="fas fa-temperature-high text-8xl"></i></div>
        <p class="text-gray-400 text-sm font-medium">Suhu Kandang</p>
        <div class="mt-2 flex items-baseline gap-2">
            <h3 class="text-4xl font-bold text-neonBlue neon-text-blue" id="currentSuhu">--</h3>
            <span class="text-xl text-gray-500">°C</span>
        </div>
    </div>
    <div class="glass-panel p-6 relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 text-neonBlue opacity-10"><i class="fas fa-tint text-8xl"></i></div>
        <p class="text-gray-400 text-sm font-medium">Kelembaban Kandang</p>
        <div class="mt-2 flex items-baseline gap-2">
            <h3 class="text-4xl font-bold text-neonBlue neon-text-blue" id="currentHum">--</h3>
            <span class="text-xl text-gray-500">%</span>
        </div>
    </div>
</div>

<div class="glass-panel p-6 mb-8">
    <h3 class="text-lg font-semibold mb-4 text-gray-200">Grafik Pola Suhu & Kelembaban</h3>
    <div class="h-72 w-full">
        <canvas id="userChart"></canvas>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('userChart').getContext('2d');
    const userChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                { label: 'Suhu (°C)', data: [], borderColor: '#00f3ff', backgroundColor: 'rgba(0, 243, 255, 0.1)', fill: true, tension: 0.4 },
                { label: 'Kelembaban (%)', data: [], borderColor: '#bc13fe', backgroundColor: 'rgba(188, 19, 254, 0.1)', fill: true, tension: 0.4 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: { x: { ticks: { color: '#9ca3af'} }, y: { ticks: { color: '#9ca3af'} } },
            plugins: { legend: { labels: { color: '#fff' } } }
        }
    });

    async function loadData() {
        try {
            const res = await fetch('/api/user/dashboard');
            const json = await res.json();
            if (json.data && json.data.recent_sensors.length > 0) {
                document.getElementById('currentSuhu').textContent = json.data.recent_sensors[0].suhu;
                document.getElementById('currentHum').textContent = json.data.recent_sensors[0].kelembaban;

                const chartData = json.data.recent_sensors.slice().reverse();
                userChart.data.labels = chartData.map(d => new Date(d.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}));
                userChart.data.datasets[0].data = chartData.map(d => d.suhu);
                userChart.data.datasets[1].data = chartData.map(d => d.kelembaban);
                userChart.update('none');
            }
        } catch(e) {}
    }

    // Load initial data
    loadData();

    // Listen to real-time socket updates from layout
    if (typeof socket !== 'undefined') {
        socket.on('sensor_update', (data) => {
            console.log('Real-time sensor update:', data);
            
            // Update UI instantly
            document.getElementById('currentSuhu').textContent = data.suhu;
            document.getElementById('currentHum').textContent = data.kelembaban;

            // Animate Chart Update
            const date = new Date(data.created_at || new Date());
            const timeLabel = date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            
            userChart.data.labels.push(timeLabel);
            userChart.data.datasets[0].data.push(data.suhu);
            userChart.data.datasets[1].data.push(data.kelembaban);

            if (userChart.data.labels.length > 50) {
                userChart.data.labels.shift();
                userChart.data.datasets[0].data.shift();
                userChart.data.datasets[1].data.shift();
            }

            userChart.update('active');
        });
    }
</script>
<?= $this->endSection() ?>
