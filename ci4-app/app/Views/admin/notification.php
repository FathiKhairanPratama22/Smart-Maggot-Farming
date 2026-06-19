<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Notifikasi & Settings<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold">Notifikasi & Pengaturan</h1>
        <p class="text-sm text-gray-400">Log Peringatan dan Ambang Batas Sensor</p>
    </div>
</header>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Settings Form -->
    <div class="glass-panel p-6 h-fit">
        <h2 class="text-lg font-bold mb-4 neon-text-blue"><i class="fas fa-cog mr-2"></i> Ambang Batas Sensor</h2>
        <form id="settingsForm" onsubmit="saveSettings(event)">
            <div class="mb-4">
                <label class="block text-sm text-gray-300 mb-1">Suhu Minimum (°C)</label>
                <input type="number" step="0.1" id="suhu_min" class="input-glass w-full rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-300 mb-1">Suhu Maksimum (°C)</label>
                <input type="number" step="0.1" id="suhu_max" class="input-glass w-full rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-300 mb-1">Kelembaban Minimum (%)</label>
                <input type="number" step="0.1" id="kelembaban_min" class="input-glass w-full rounded px-3 py-2" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm text-gray-300 mb-1">Kelembaban Maksimum (%)</label>
                <input type="number" step="0.1" id="kelembaban_max" class="input-glass w-full rounded px-3 py-2" required>
            </div>
            <button type="submit" class="btn-neon w-full py-2 rounded font-bold">Simpan Pengaturan</button>
        </form>
    </div>

    <!-- Notification List -->
    <div class="lg:col-span-2 glass-panel p-6">
        <h2 class="text-lg font-bold mb-4 neon-text-purple"><i class="fas fa-bell mr-2"></i> Log Peringatan</h2>
        <div id="notifContainer" class="space-y-3 h-[500px] overflow-y-auto pr-2">
            <!-- Notifications injected here -->
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    async function loadSettings() {
        try {
            const res = await fetch('/api/admin/settings');
            const json = await res.json();
            if (json.data) {
                document.getElementById('suhu_min').value = json.data.suhu_min;
                document.getElementById('suhu_max').value = json.data.suhu_max;
                document.getElementById('kelembaban_min').value = json.data.kelembaban_min;
                document.getElementById('kelembaban_max').value = json.data.kelembaban_max;
            }
        } catch(e) {}
    }

    async function saveSettings(e) {
        e.preventDefault();
        const data = {
            suhu_min: document.getElementById('suhu_min').value,
            suhu_max: document.getElementById('suhu_max').value,
            kelembaban_min: document.getElementById('kelembaban_min').value,
            kelembaban_max: document.getElementById('kelembaban_max').value
        };
        showLoading();
        try {
            const res = await fetch('/api/admin/settings', {
                method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data)
            });
            if(res.ok) Toast.fire({ icon: 'success', title: 'Settings diperbarui' });
        } catch(e) {}
        hideLoading();
    }

    async function loadNotifications() {
        try {
            const res = await fetch('/api/admin/notification');
            const json = await res.json();
            const container = document.getElementById('notifContainer');
            container.innerHTML = '';
            
            json.data.forEach(item => {
                const isAbnormal = item.jenis === 'warning' || item.jenis === 'danger';
                const style = isAbnormal ? 'border-red-500/50 bg-red-500/10 shadow-[0_0_10px_rgba(239,68,68,0.2)]' : 'border-gray-700 bg-gray-800/30';
                const iconColor = isAbnormal ? 'text-red-400' : 'text-neonBlue';
                
                const notif = `
                    <div class="p-4 rounded-lg border ${style} flex items-start gap-4 transition-all hover:bg-gray-800/50">
                        <div class="mt-1 ${iconColor}"><i class="fas fa-exclamation-circle"></i></div>
                        <div>
                            <p class="text-sm font-semibold text-white mb-1">${item.pesan}</p>
                            <span class="text-xs text-gray-500">${item.created_at}</span>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', notif);
            });
        } catch(e) {}
    }

    loadSettings();
    loadNotifications();
</script>
<?= $this->endSection() ?>
