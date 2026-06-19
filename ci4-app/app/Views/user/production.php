<?= $this->extend('layout/user') ?>

<?= $this->section('title') ?>Riwayat Produksi<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold">Riwayat Produksi Maggot</h1>
        <p class="text-sm text-gray-400">Data Hasil Panen (Read Only)</p>
    </div>
</header>

<div class="glass-panel p-6 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Berat Pakan (Kg)</th>
                <th>Hasil Panen (Kg)</th>
                <th>FCR (Efisiensi)</th>
            </tr>
        </thead>
        <tbody id="prodTableBody"></tbody>
    </table>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    async function loadData() {
        showLoading();
        try {
            const res = await fetch('/api/user/production');
            const json = await res.json();
            const tbody = document.getElementById('prodTableBody');
            tbody.innerHTML = '';

            if (json.data && json.data.length > 0) {
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
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-gray-500 py-4">Data belum tersedia</td></tr>';
            }
        } catch (e) {
            Toast.fire({ icon: 'error', title: 'Gagal memuat data' });
        }
        hideLoading();
    }

    loadData();
</script>
<?= $this->endSection() ?>
