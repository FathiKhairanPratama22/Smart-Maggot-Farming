<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Manajemen Artikel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold">Manajemen Artikel</h1>
        <p class="text-sm text-gray-400">Pusat Informasi & Pengetahuan</p>
    </div>
    <div class="flex gap-2">
        <button onclick="openCategoryModal()" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg font-semibold shadow">Kategori</button>
        <button onclick="openArticleModal()" class="btn-neon px-5 py-2 rounded-lg font-semibold shadow-lg">
            <i class="fas fa-plus mr-2"></i> Tambah Artikel
        </button>
    </div>
</header>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="articleContainer">
    <!-- Articles injected here -->
</div>

<!-- Modal Artikel -->
<div id="articleModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="glass-panel p-8 w-full max-w-2xl relative">
        <button onclick="closeArticleModal()" class="absolute top-4 right-4 text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        <h2 class="text-xl font-bold mb-6 neon-text-blue" id="modalTitle">Tambah Artikel</h2>
        <form id="articleForm" onsubmit="saveArticle(event)">
            <input type="hidden" id="articleId">
            <div class="mb-4">
                <label class="block text-sm text-gray-300 mb-1">Kategori</label>
                <select id="kategori_id" class="input-glass w-full rounded px-3 py-2" required></select>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-300 mb-1">Judul Artikel</label>
                <input type="text" id="judul" class="input-glass w-full rounded px-3 py-2" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm text-gray-300 mb-1">Konten</label>
                <textarea id="konten" rows="6" class="input-glass w-full rounded px-3 py-2" required></textarea>
            </div>
            <button type="submit" class="btn-neon w-full py-2 rounded font-bold">Simpan Artikel</button>
        </form>
    </div>
</div>

<!-- Modal Kategori -->
<div id="categoryModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="glass-panel p-8 w-full max-w-sm relative">
        <button onclick="closeCategoryModal()" class="absolute top-4 right-4 text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        <h2 class="text-xl font-bold mb-6 text-neonPurple">Tambah Kategori Baru</h2>
        <form id="categoryForm" onsubmit="saveCategory(event)">
            <div class="mb-6">
                <input type="text" id="nama_kategori" placeholder="Nama Kategori" class="input-glass w-full rounded px-3 py-2" required>
            </div>
            <button type="submit" class="w-full bg-neonPurple hover:bg-purple-600 text-white py-2 rounded font-bold">Simpan</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    async function loadArticles() {
        showLoading();
        try {
            const res = await fetch('/api/admin/article');
            const json = await res.json();
            const container = document.getElementById('articleContainer');
            container.innerHTML = '';

            json.data.forEach(item => {
                const card = `
                    <div class="glass-panel p-6 flex flex-col">
                        <span class="text-xs bg-neonBlue/20 text-neonBlue px-2 py-1 rounded-full w-max mb-3">${item.nama_kategori}</span>
                        <h3 class="text-lg font-bold text-white mb-2 line-clamp-2">${item.judul}</h3>
                        <p class="text-gray-400 text-sm mb-4 line-clamp-3 flex-1">${item.konten}</p>
                        <div class="flex justify-between items-center border-t border-gray-700 pt-3">
                            <span class="text-xs text-gray-500">${item.created_at.split(' ')[0]}</span>
                            <div class="space-x-2">
                                <button onclick='editArticle(${JSON.stringify(item).replace(/'/g, "&#39;")})' class="text-blue-400 hover:text-blue-300"><i class="fas fa-edit"></i></button>
                                <button onclick="deleteArticle(${item.id})" class="text-red-400 hover:text-red-300"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', card);
            });
        } catch (e) {
            Toast.fire({ icon: 'error', title: 'Gagal memuat artikel' });
        }
        hideLoading();
    }

    async function loadCategories() {
        const res = await fetch('/api/admin/category');
        const json = await res.json();
        const select = document.getElementById('kategori_id');
        select.innerHTML = '<option value="">Pilih Kategori...</option>';
        json.data.forEach(c => {
            select.insertAdjacentHTML('beforeend', `<option value="${c.id}" class="text-black">${c.nama_kategori}</option>`);
        });
    }

    function openArticleModal() {
        document.getElementById('articleForm').reset();
        document.getElementById('articleId').value = '';
        document.getElementById('modalTitle').textContent = 'Tambah Artikel';
        document.getElementById('articleModal').classList.remove('hidden');
    }
    function closeArticleModal() { document.getElementById('articleModal').classList.add('hidden'); }
    
    function openCategoryModal() { document.getElementById('categoryModal').classList.remove('hidden'); }
    function closeCategoryModal() { document.getElementById('categoryModal').classList.add('hidden'); }

    function editArticle(item) {
        document.getElementById('articleId').value = item.id;
        document.getElementById('kategori_id').value = item.kategori_id;
        document.getElementById('judul').value = item.judul;
        document.getElementById('konten').value = item.konten;
        document.getElementById('modalTitle').textContent = 'Edit Artikel';
        document.getElementById('articleModal').classList.remove('hidden');
    }

    async function saveArticle(e) {
        e.preventDefault();
        const id = document.getElementById('articleId').value;
        const data = {
            kategori_id: document.getElementById('kategori_id').value,
            judul: document.getElementById('judul').value,
            konten: document.getElementById('konten').value
        };
        const method = id ? 'PUT' : 'POST';
        const url = id ? `/api/admin/article/${id}` : '/api/admin/article';

        showLoading();
        try {
            const res = await fetch(url, { method, headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data) });
            if(res.ok) {
                Toast.fire({ icon: 'success', title: 'Berhasil disimpan' });
                closeArticleModal(); loadArticles();
            }
        } catch (e) {}
        hideLoading();
    }

    async function saveCategory(e) {
        e.preventDefault();
        const data = { nama_kategori: document.getElementById('nama_kategori').value };
        showLoading();
        try {
            const res = await fetch('/api/admin/category', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data) });
            if(res.ok) {
                Toast.fire({ icon: 'success', title: 'Kategori disimpan' });
                closeCategoryModal(); loadCategories(); document.getElementById('categoryForm').reset();
            }
        } catch (e) {}
        hideLoading();
    }

    async function deleteArticle(id) {
        if(confirm('Hapus artikel ini?')) {
            await fetch(`/api/admin/article/${id}`, { method: 'DELETE' });
            Toast.fire({ icon: 'success', title: 'Artikel terhapus' });
            loadArticles();
        }
    }

    loadCategories();
    loadArticles();
</script>
<?= $this->endSection() ?>
