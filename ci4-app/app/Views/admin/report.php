<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Laporan Sistem<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold">Laporan Sistem</h1>
        <p class="text-sm text-gray-400">Generate Laporan Produksi & Monitoring</p>
    </div>
</header>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="glass-panel p-8 text-center flex flex-col items-center justify-center">
        <div class="w-16 h-16 rounded-full bg-red-500/20 text-red-500 flex items-center justify-center mb-4 text-2xl shadow-[0_0_15px_rgba(239,68,68,0.4)]">
            <i class="fas fa-file-pdf"></i>
        </div>
        <h3 class="text-xl font-bold mb-2">Export PDF</h3>
        <p class="text-sm text-gray-400 mb-6">Unduh laporan produksi dalam format dokumen PDF resmi yang siap dicetak.</p>
        <button onclick="downloadReport('pdf')" class="bg-red-500 hover:bg-red-600 px-6 py-3 rounded-lg font-bold shadow-lg transition-colors w-full md:w-auto">
            <i class="fas fa-download mr-2"></i> Download PDF
        </button>
    </div>

    <div class="glass-panel p-8 text-center flex flex-col items-center justify-center">
        <div class="w-16 h-16 rounded-full bg-green-500/20 text-green-500 flex items-center justify-center mb-4 text-2xl shadow-[0_0_15px_rgba(34,197,94,0.4)]">
            <i class="fas fa-file-excel"></i>
        </div>
        <h3 class="text-xl font-bold mb-2">Export Excel</h3>
        <p class="text-sm text-gray-400 mb-6">Unduh laporan produksi dalam format spreadsheet Excel (.xlsx) untuk analisis lanjutan.</p>
        <button onclick="downloadReport('excel')" class="bg-green-500 hover:bg-green-600 px-6 py-3 rounded-lg font-bold shadow-lg transition-colors w-full md:w-auto">
            <i class="fas fa-download mr-2"></i> Download Excel
        </button>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function downloadReport(type) {
        showLoading();
        // Delay slighty to show the loading animation, since window.location doesn't trigger await
        setTimeout(() => {
            window.location.href = `/api/admin/report/${type}`;
            // Hide loading after 3 seconds assuming download started
            setTimeout(() => { hideLoading(); }, 3000);
        }, 500);
    }
</script>
<?= $this->endSection() ?>
