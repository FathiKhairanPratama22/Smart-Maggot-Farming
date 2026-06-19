<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Produksi Maggot<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold">Produksi Maggot</h1>
        <p class="text-sm text-gray-400">Manajemen Panen & Pakan Harian</p>
    </div>
    <button onclick="openModal()" class="btn-neon px-5 py-2 rounded-lg font-semibold shadow-lg">
        <i class="fas fa-plus mr-2"></i> Tambah Produksi
    </button>
</header>

<div class="glass-panel p-6 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Berat Pakan (Kg)</th>
                <th>Hasil Panen (Kg)</th>
                <th>FCR (Efisiensi)</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody id="prodTableBody"></tbody>
    </table>
</div>

<!-- Modal Form -->
<div id="prodModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="glass-panel p-8 w-full max-w-md relative">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        <h2 class="text-xl font-bold mb-6 neon-text-blue" id="modalTitle">Tambah Produksi</h2>
        
        <form id="prodForm" onsubmit="saveData(event)">
            <input type="hidden" id="prodId">
            <div class="mb-4">
                <label class="block text-sm text-gray-300 mb-1">Tanggal</label>
                <input type="date" id="tanggal" class="input-glass w-full rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-300 mb-1">Berat Pakan (Kg)</label>
                <input type="number" step="0.1" id="berat_pakan" class="input-glass w-full rounded px-3 py-2" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm text-gray-300 mb-1">Berat Maggot (Kg)</label>
                <input type="number" step="0.1" id="berat_maggot" class="input-glass w-full rounded px-3 py-2" required>
            </div>
            <button type="submit" class="btn-neon w-full py-2 rounded font-bold">Simpan Data</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    async function loadData() {
        showLoading();
        try {
            const res = await fetch('/api/admin/production');
            const json = await res.json();
            const tbody = document.getElementById('prodTableBody');
            tbody.innerHTML = '';

            json.data.forEach(item => {
                const fcr = (parseFloat(item.berat_pakan) / parseFloat(item.berat_maggot)).toFixed(2);
                let fcrHtml = `<span class="text-green-400">${fcr} (Sangat Baik)</span>`;
                if(fcr > 1.5) fcrHtml = `<span class="text-yellow-400">${fcr} (Normal)</span>`;
                if(fcr > 2.5) fcrHtml = `<span class="text-red-400">${fcr} (Buruk)</span>`;

                const row = `
                    <tr>
                        <td>${item.tanggal}</td>
                        <td>${item.berat_pakan}</td>
                        <td class="text-neonPurple font-bold">${item.berat_maggot}</td>
                        <td>${fcrHtml}</td>
                        <td class="text-right">
                            <button onclick='editData(${JSON.stringify(item)})' class="text-blue-400 hover:text-blue-300 px-2"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteData(${item.id})" class="text-red-400 hover:text-red-300 px-2"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        } catch (e) {
            Toast.fire({ icon: 'error', title: 'Gagal memuat data' });
        }
        hideLoading();
    }

    function openModal() {
        document.getElementById('prodForm').reset();
        document.getElementById('prodId').value = '';
        document.getElementById('modalTitle').textContent = 'Tambah Produksi';
        document.getElementById('prodModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('prodModal').classList.add('hidden');
    }

    function editData(item) {
        document.getElementById('prodId').value = item.id;
        document.getElementById('tanggal').value = item.tanggal;
        document.getElementById('berat_pakan').value = item.berat_pakan;
        document.getElementById('berat_maggot').value = item.berat_maggot;
        document.getElementById('modalTitle').textContent = 'Edit Produksi';
        document.getElementById('prodModal').classList.remove('hidden');
    }

    async function saveData(e) {
        e.preventDefault();
        const id = document.getElementById('prodId').value;
        const data = {
            tanggal: document.getElementById('tanggal').value,
            berat_pakan: document.getElementById('berat_pakan').value,
            berat_maggot: document.getElementById('berat_maggot').value
        };

        const method = id ? 'PUT' : 'POST';
        const url = id ? `/api/admin/production/${id}` : '/api/admin/production';

        showLoading();
        try {
            const res = await fetch(url, {
                method, headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data)
            });
            const json = await res.json();
            if(res.ok) {
                Toast.fire({ icon: 'success', title: json.message });
                closeModal();
                loadData();
            } else throw new Error();
        } catch (e) {
            Toast.fire({ icon: 'error', title: 'Gagal menyimpan data' });
        }
        hideLoading();
    }

    async function deleteData(id) {
        if(confirm('Hapus data ini?')) {
            await fetch(`/api/admin/production/${id}`, { method: 'DELETE' });
            Toast.fire({ icon: 'success', title: 'Data terhapus' });
            loadData();
        }
    }

    loadData();
</script>
<?= $this->endSection() ?>
