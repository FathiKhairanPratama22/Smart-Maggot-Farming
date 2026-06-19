<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>User Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold">User Management</h1>
        <p class="text-sm text-gray-400">Kelola Akses Admin, Guru, dan Siswa</p>
    </div>
    <button onclick="openModal()" class="btn-neon px-5 py-2 rounded-lg font-semibold shadow-lg">
        <i class="fas fa-user-plus mr-2"></i> Tambah User
    </button>
</header>

<div class="glass-panel p-6 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody id="userTableBody"></tbody>
    </table>
</div>

<!-- Modal Form -->
<div id="userModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="glass-panel p-8 w-full max-w-md relative">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        <h2 class="text-xl font-bold mb-6 neon-text-blue" id="modalTitle">Tambah User</h2>
        
        <form id="userForm" onsubmit="saveData(event)">
            <input type="hidden" id="userId">
            <div class="mb-4">
                <label class="block text-sm text-gray-300 mb-1">Nama Lengkap</label>
                <input type="text" id="name" class="input-glass w-full rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-300 mb-1">Email</label>
                <input type="email" id="email" class="input-glass w-full rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-300 mb-1">Role</label>
                <select id="role" class="input-glass w-full rounded px-3 py-2" required>
                    <option value="admin" class="text-black">Admin</option>
                    <option value="guru" class="text-black">Guru</option>
                    <option value="siswa" class="text-black">Siswa</option>
                </select>
            </div>
            <div class="mb-4 flex items-center gap-2">
                <input type="checkbox" id="is_active" class="w-4 h-4 text-neonBlue bg-gray-700 border-gray-600 rounded">
                <label class="text-sm text-gray-300">Akun Aktif</label>
            </div>
            <div class="mb-6">
                <label class="block text-sm text-gray-300 mb-1">Password <span class="text-xs text-gray-500" id="pwNote"></span></label>
                <input type="password" id="password" class="input-glass w-full rounded px-3 py-2">
            </div>
            <button type="submit" class="btn-neon w-full py-2 rounded font-bold">Simpan User</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    async function loadData() {
        showLoading();
        try {
            const res = await fetch('/api/admin/users');
            const json = await res.json();
            const tbody = document.getElementById('userTableBody');
            tbody.innerHTML = '';

            json.data.forEach(item => {
                const statusBadge = item.is_active == 1 
                    ? '<span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs border border-green-500/50">Aktif</span>'
                    : '<span class="bg-red-500/20 text-red-400 px-2 py-1 rounded text-xs border border-red-500/50">Nonaktif</span>';
                
                const roleColors = { 'admin': 'text-neonPurple', 'guru': 'text-neonBlue', 'siswa': 'text-gray-300' };
                const roleClass = roleColors[item.role] || 'text-white';

                const row = `
                    <tr>
                        <td class="font-semibold">${item.name}</td>
                        <td class="text-gray-400">${item.email}</td>
                        <td class="font-bold capitalize ${roleClass}">${item.role}</td>
                        <td>${statusBadge}</td>
                        <td class="text-right">
                            <button onclick='editData(${JSON.stringify(item)})' class="text-blue-400 hover:text-blue-300 px-2"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteData(${item.id})" class="text-red-400 hover:text-red-300 px-2"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        } catch (e) {}
        hideLoading();
    }

    function openModal() {
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('is_active').checked = true;
        document.getElementById('modalTitle').textContent = 'Tambah User';
        document.getElementById('password').required = true;
        document.getElementById('pwNote').textContent = '';
        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeModal() { document.getElementById('userModal').classList.add('hidden'); }

    function editData(item) {
        document.getElementById('userId').value = item.id;
        document.getElementById('name').value = item.name;
        document.getElementById('email').value = item.email;
        document.getElementById('role').value = item.role;
        document.getElementById('is_active').checked = item.is_active == 1;
        document.getElementById('modalTitle').textContent = 'Edit User';
        document.getElementById('password').required = false;
        document.getElementById('password').value = '';
        document.getElementById('pwNote').textContent = '(Kosongkan jika tidak ingin mengubah password)';
        document.getElementById('userModal').classList.remove('hidden');
    }

    async function saveData(e) {
        e.preventDefault();
        const id = document.getElementById('userId').value;
        const data = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            role: document.getElementById('role').value,
            is_active: document.getElementById('is_active').checked ? 1 : 0
        };
        const pw = document.getElementById('password').value;
        if (pw) data.password = pw;

        const method = id ? 'PUT' : 'POST';
        const url = id ? `/api/admin/users/${id}` : '/api/admin/users';

        showLoading();
        try {
            const res = await fetch(url, { method, headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data) });
            const json = await res.json();
            if(res.ok) { Toast.fire({ icon: 'success', title: json.message }); closeModal(); loadData(); }
            else Toast.fire({ icon: 'error', title: 'Gagal: Email mungkin sudah ada' });
        } catch (e) {}
        hideLoading();
    }

    async function deleteData(id) {
        if(confirm('Hapus user ini?')) {
            await fetch(`/api/admin/users/${id}`, { method: 'DELETE' });
            Toast.fire({ icon: 'success', title: 'User terhapus' });
            loadData();
        }
    }

    loadData();
</script>
<?= $this->endSection() ?>
