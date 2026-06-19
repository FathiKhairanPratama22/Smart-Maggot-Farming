<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Admin Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold">Live Monitoring Overview</h1>
        <p class="text-sm text-gray-400">Real-time sensor data & production summary</p>
    </div>
    <div class="flex items-center space-x-4">
        <div id="connectionStatus" class="flex items-center text-sm text-green-400 bg-green-400/10 px-3 py-1 rounded-full border border-green-400/20">
            <span class="relative flex h-2 w-2 mr-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
            Live WebSocket Connected
        </div>
    </div>
</header>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="glass-panel p-6 relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 text-neonBlue opacity-10 group-hover:scale-110 transition-transform"><i class="fas fa-temperature-high text-8xl"></i></div>
        <p class="text-gray-400 text-sm font-medium">Suhu Saat Ini</p>
        <div class="mt-2 flex items-baseline gap-2">
            <h3 class="text-4xl font-bold text-neonBlue neon-text-blue" id="currentSuhu">--</h3>
            <span class="text-xl text-gray-500">°C</span>
        </div>
    </div>

    <div class="glass-panel p-6 relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 text-neonBlue opacity-10 group-hover:scale-110 transition-transform"><i class="fas fa-tint text-8xl"></i></div>
        <p class="text-gray-400 text-sm font-medium">Kelembaban Saat Ini</p>
        <div class="mt-2 flex items-baseline gap-2">
            <h3 class="text-4xl font-bold text-neonBlue neon-text-blue" id="currentHum">--</h3>
            <span class="text-xl text-gray-500">%</span>
        </div>
    </div>

    <div class="glass-panel p-6 relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 text-neonPurple opacity-10 group-hover:scale-110 transition-transform"><i class="fas fa-balance-scale text-8xl"></i></div>
        <p class="text-gray-400 text-sm font-medium">Panen Hari Ini</p>
        <div class="mt-2 flex items-baseline gap-2">
            <h3 class="text-4xl font-bold text-neonPurple neon-text-purple" id="prodHariIni">--</h3>
            <span class="text-xl text-gray-500">Kg</span>
        </div>
    </div>

    <div class="glass-panel p-6 relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 text-neonPurple opacity-10 group-hover:scale-110 transition-transform"><i class="fas fa-calendar-alt text-8xl"></i></div>
        <p class="text-gray-400 text-sm font-medium">Total Bulan Ini</p>
        <div class="mt-2 flex items-baseline gap-2">
            <h3 class="text-4xl font-bold text-neonPurple neon-text-purple" id="prodBulanIni">--</h3>
            <span class="text-xl text-gray-500">Kg</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 mb-8">
    <div class="glass-panel p-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-200">Grafik Suhu & Kelembaban (Real-time WebSocket)</h3>
        <div class="h-72 w-full">
            <canvas id="sensorChart"></canvas>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('sensorChart').getContext('2d');
    const sensorChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Suhu (°C)',
                    data: [],
                    borderColor: '#00f3ff',
                    backgroundColor: 'rgba(0, 243, 255, 0.1)',
                    borderWidth: 2, pointRadius: 0, tension: 0.4, fill: true
                },
                {
                    label: 'Kelembaban (%)',
                    data: [],
                    borderColor: '#bc13fe',
                    backgroundColor: 'rgba(188, 19, 254, 0.1)',
                    borderWidth: 2, pointRadius: 0, tension: 0.4, fill: true
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#9ca3af' } },
                y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#9ca3af' } }
            },
            plugins: { legend: { labels: { color: '#fff' } } }
        }
    });

    async function fetchDashboardData() {
        try {
            const response = await fetch('/api/admin/dashboard');
            const result = await response.json();

            if (result.status === 'success') {
                const data = result.data;
                
                if (data.recent_sensors.length > 0) {
                    document.getElementById('currentSuhu').textContent = data.recent_sensors[0].suhu;
                    document.getElementById('currentHum').textContent = data.recent_sensors[0].kelembaban;
                }
                
                document.getElementById('prodHariIni').textContent = data.summary_production.hari_ini || 0;
                document.getElementById('prodBulanIni').textContent = data.summary_production.bulan_ini || 0;

                const chartData = data.recent_sensors.slice().reverse();
                sensorChart.data.labels = chartData.map(d => {
                    const date = new Date(d.created_at);
                    return `${date.getHours()}:${date.getMinutes().toString().padStart(2, '0')}`;
                });
                sensorChart.data.datasets[0].data = chartData.map(d => d.suhu);
                sensorChart.data.datasets[1].data = chartData.map(d => d.kelembaban);
                sensorChart.update('none');
            }
        } catch (error) {}
    }

    // Load initial data
    fetchDashboardData();

    // Listen to real-time socket updates from layout
    if (typeof socket !== 'undefined') {
        socket.on('sensor_update', (data) => {
            console.log('Real-time sensor update:', data);
            
            // Update UI elements instantly
            document.getElementById('currentSuhu').textContent = data.suhu;
            document.getElementById('currentHum').textContent = data.kelembaban;

            // Animate Chart Update
            const date = new Date(data.created_at || new Date());
            const timeLabel = `${date.getHours()}:${date.getMinutes().toString().padStart(2, '0')}`;
            
            // Add new data points
            sensorChart.data.labels.push(timeLabel);
            sensorChart.data.datasets[0].data.push(data.suhu);
            sensorChart.data.datasets[1].data.push(data.kelembaban);

            // Maintain max 50 points (avoid memory leak)
            if (sensorChart.data.labels.length > 50) {
                sensorChart.data.labels.shift();
                sensorChart.data.datasets[0].data.shift();
                sensorChart.data.datasets[1].data.shift();
            }

            // Smooth update
            sensorChart.update('active');
        });
    }
</script>
<?= $this->endSection() ?>
