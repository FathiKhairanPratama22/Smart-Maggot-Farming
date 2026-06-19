<?= $this->extend('layout/user') ?>

<?= $this->section('title') ?>Materi Edukasi<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold">Materi Edukasi</h1>
        <p class="text-sm text-gray-400">Pusat Pengetahuan Smart Maggot</p>
    </div>
</header>

<div class="glass-panel p-6 mb-6 flex gap-4 items-center">
    <div class="flex-1">
        <label class="text-xs text-gray-400 block mb-1">Cari Artikel</label>
        <input type="text" id="searchInput" placeholder="Ketik judul..." class="input-glass w-full rounded px-3 py-2 text-sm">
    </div>
    <div class="pt-5">
        <button onclick="loadArticles()" class="btn-neon px-6 py-2 rounded text-sm"><i class="fas fa-search mr-2"></i> Cari</button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="articleContainer">
    <!-- Articles injected here -->
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    async function loadArticles() {
        showLoading();
        try {
            const query = document.getElementById('searchInput').value;
            let url = '/api/user/article';
            if (query) url += `?search=${encodeURIComponent(query)}`;

            const res = await fetch(url);
            const json = await res.json();
            const container = document.getElementById('articleContainer');
            container.innerHTML = '';

            if (json.data && json.data.length > 0) {
                json.data.forEach(item => {
                    const card = `
                        <div class="glass-panel p-6 flex flex-col hover:border-neonPurple hover:shadow-[0_0_15px_rgba(188,19,254,0.3)] transition-all">
                            <span class="text-xs bg-neonPurple/20 text-neonPurple px-2 py-1 rounded-full w-max mb-3 border border-neonPurple/30">${item.nama_kategori}</span>
                            <h3 class="text-lg font-bold text-white mb-2 line-clamp-2">${item.judul}</h3>
                            <p class="text-gray-400 text-sm mb-4 line-clamp-4 flex-1">${item.konten}</p>
                            <div class="flex justify-between items-center border-t border-gray-700 pt-3">
                                <span class="text-xs text-gray-500"><i class="fas fa-calendar-alt mr-1"></i> ${item.created_at.split(' ')[0]}</span>
                                <button class="text-neonPurple hover:text-white text-sm"><i class="fas fa-book-open"></i> Baca</button>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', card);
                });
            } else {
                container.innerHTML = '<div class="col-span-full text-center text-gray-500 py-8">Artikel tidak ditemukan</div>';
            }
        } catch (e) {
            Toast.fire({ icon: 'error', title: 'Gagal memuat artikel' });
        }
        hideLoading();
    }

    loadArticles();
</script>
<?= $this->endSection() ?>
