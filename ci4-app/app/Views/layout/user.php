<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - Smart Maggot</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        neonBlue: '#00f3ff',
                        neonPurple: '#bc13fe',
                        darkBg: '#0b0f19',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #0b0f19;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(0, 243, 255, 0.05), transparent 30%),
                radial-gradient(circle at 90% 80%, rgba(188, 19, 254, 0.05), transparent 30%);
            color: #fff;
            font-family: 'Inter', sans-serif;
        }
        .glass-panel {
            background: rgba(17, 25, 40, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            border-radius: 16px;
        }
        .neon-text-blue { text-shadow: 0 0 10px rgba(0, 243, 255, 0.5); }
        .neon-text-purple { text-shadow: 0 0 10px rgba(188, 19, 254, 0.5); }
        .sidebar-link {
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255, 255, 255, 0.05);
            border-left-color: #bc13fe;
            color: #bc13fe;
            text-shadow: 0 0 8px rgba(188, 19, 254, 0.4);
        }
        /* Custom Table Styling */
        table { border-collapse: separate; border-spacing: 0; width: 100%; }
        th { background: rgba(255, 255, 255, 0.05); padding: 12px; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        td { padding: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        tr:hover td { background: rgba(255, 255, 255, 0.02); }
    </style>
</head>
<body class="flex h-screen overflow-hidden">
    <!-- User Sidebar uses purple accent instead of blue -->
    <aside class="w-64 glass-panel m-4 flex flex-col hidden md:flex">
        <div class="p-6 text-center border-b border-gray-800">
            <h2 class="text-xl font-bold tracking-widest"><span class="text-neonBlue neon-text-blue">SMART</span> <span class="text-neonPurple neon-text-purple">MAGGOT</span></h2>
            <p class="text-xs text-gray-500 mt-1">User Panel</p>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="/user/dashboard" class="sidebar-link <?= (url_is('user/dashboard')) ? 'active' : '' ?> flex items-center px-4 py-3 text-gray-300 rounded-lg">
                <i class="fas fa-home w-6"></i> Dashboard
            </a>
            <a href="/user/production" class="sidebar-link <?= (url_is('user/production')) ? 'active' : '' ?> flex items-center px-4 py-3 text-gray-300 rounded-lg">
                <i class="fas fa-box w-6"></i> Riwayat Produksi
            </a>
            <a href="/user/article" class="sidebar-link <?= (url_is('user/article')) ? 'active' : '' ?> flex items-center px-4 py-3 text-gray-300 rounded-lg">
                <i class="fas fa-book w-6"></i> Materi Edukasi
            </a>
            <a href="/user/notification" class="sidebar-link <?= (url_is('user/notification')) ? 'active' : '' ?> flex items-center px-4 py-3 text-gray-300 rounded-lg">
                <i class="fas fa-bell w-6"></i> Info & Peringatan
            </a>
        </nav>
        <div class="p-4 border-t border-gray-800">
            <button onclick="logout()" class="w-full py-2 text-sm text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto p-4 md:p-8 relative">
        <div id="globalSpinner" class="hidden absolute inset-0 bg-black/50 z-50 flex items-center justify-center backdrop-blur-sm">
            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-neonPurple border-solid"></div>
        </div>
        
        <?= $this->renderSection('content') ?>
    </main>

    <script>
        async function logout() {
            await fetch('/api/auth/logout', { method: 'POST' });
            window.location.href = '/auth';
        }
        function showLoading() { document.getElementById('globalSpinner').classList.remove('hidden'); }
        function hideLoading() { document.getElementById('globalSpinner').classList.add('hidden'); }
    </script>
    <script src="http://localhost:3000/socket.io/socket.io.js"></script>
    <script>
        // Global Socket.io Connection
        const socket = io("http://localhost:3000");
        
        socket.on("connect", () => {
            console.log("Connected to WebSocket server");
        });

        // Global Alert Listener
        socket.on("notification_alert", (data) => {
            Swal.fire({
                title: 'Peringatan Sensor!',
                text: data.pesan,
                icon: 'warning',
                background: '#111928',
                color: '#fff',
                confirmButtonColor: '#bc13fe'
            });
            if(typeof loadNotifications === 'function') {
                loadNotifications();
            }
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
