<?= $this->extend('layout/user') ?>

<?= $this->section('title') ?>Info & Peringatan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold">Info & Peringatan</h1>
        <p class="text-sm text-gray-400">Log Peringatan Suhu & Kelembaban Kandang</p>
    </div>
</header>

<div class="glass-panel p-6 max-w-4xl mx-auto">
    <div id="notifContainer" class="space-y-4 h-[600px] overflow-y-auto pr-2">
        <!-- Notifications injected here -->
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    async function loadNotifications() {
        showLoading();
        try {
            const res = await fetch('/api/user/notification');
            const json = await res.json();
            const container = document.getElementById('notifContainer');
            container.innerHTML = '';
            
            if (json.data && json.data.length > 0) {
                json.data.forEach(item => {
                    const isAbnormal = item.jenis === 'warning' || item.jenis === 'danger';
                    const style = isAbnormal ? 'border-red-500/50 bg-red-500/10 shadow-[0_0_10px_rgba(239,68,68,0.2)]' : 'border-gray-700 bg-gray-800/30';
                    const iconColor = isAbnormal ? 'text-red-400' : 'text-neonBlue';
                    
                    const notif = `
                        <div class="p-5 rounded-lg border ${style} flex items-start gap-4 transition-all hover:bg-gray-800/50">
                            <div class="mt-1 text-2xl ${iconColor}"><i class="fas fa-exclamation-circle"></i></div>
                            <div>
                                <p class="text-base font-semibold text-white mb-1">${item.pesan}</p>
                                <span class="text-xs text-gray-500"><i class="fas fa-clock mr-1"></i> ${item.created_at}</span>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', notif);
                });
            } else {
                container.innerHTML = '<div class="text-center text-gray-500 py-8">Tidak ada notifikasi sistem.</div>';
            }
        } catch(e) {}
        hideLoading();
    }

    loadNotifications();
</script>
<?= $this->endSection() ?>
