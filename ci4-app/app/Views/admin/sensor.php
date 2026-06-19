<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Data Sensor<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold">Data Sensor</h1>
        <p class="text-sm text-gray-400">Riwayat Pembacaan Suhu & Kelembaban</p>
    </div>
</header>

<div class="glass-panel p-6 mb-6 flex gap-4 items-center">
    <div>
        <label class="text-xs text-gray-400 block mb-1">Filter Tanggal</label>
        <input type="date" id="filterDate" class="input-glass rounded px-3 py-2 text-sm">
    </div>
    <div class="pt-5">
        <button onclick="loadSensors()" class="btn-neon px-4 py-2 rounded text-sm"><i class="fas fa-filter"></i> Filter</button>
        <button onclick="resetFilter()" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded text-sm transition-colors"><i class="fas fa-sync"></i> Reset</button>
    </div>
</div>

<div class="glass-panel p-6 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr>
                <th>Waktu (WIB)</th>
                <th>Suhu (°C)</th>
                <th>Kelembaban (%)</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody id="sensorTableBody">
            <!-- Data injected via JS -->
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    async function loadSensors() {
        showLoading();
        try {
            const date = document.getElementById('filterDate').value;
            let url = '/api/admin/sensor';
            if (date) url += `?tanggal=${date}`;

            const res = await fetch(url);
            const json = await res.json();
            const tbody = document.getElementById('sensorTableBody');
            tbody.innerHTML = '';

            if (json.data && json.data.length > 0) {
                json.data.forEach(item => {
                    const row = `
                        <tr>
                            <td>${item.created_at}</td>
                            <td class="text-neonBlue">${item.suhu}</td>
                            <td class="text-neonPurple">${item.kelembaban}</td>
                            <td class="text-right">
                                <button onclick="deleteSensor(${item.id})" class="text-red-400 hover:text-red-300 px-2"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-gray-500 py-4">Data tidak ditemukan</td></tr>';
            }
        } catch (e) {
            Toast.fire({ icon: 'error', title: 'Gagal mengambil data' });
        }
        hideLoading();
    }

    function resetFilter() {
        document.getElementById('filterDate').value = '';
        loadSensors();
    }

    async function deleteSensor(id) {
        const confirm = await Swal.fire({
            title: 'Hapus data?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            background: '#111928',
            color: '#fff',
            confirmButtonColor: '#bc13fe',
            cancelButtonColor: '#d33'
        });

        if (confirm.isConfirmed) {
            showLoading();
            try {
                const res = await fetch(`/api/admin/sensor/${id}`, { method: 'DELETE' });
                const json = await res.json();
                if (res.ok) {
                    Toast.fire({ icon: 'success', title: json.message });
                    loadSensors();
                } else {
                    throw new Error(json.message);
                }
            } catch (e) {
                Toast.fire({ icon: 'error', title: 'Gagal menghapus' });
            }
            hideLoading();
        }
    }

    // Init
    loadSensors();
</script>
<?= $this->endSection() ?>
