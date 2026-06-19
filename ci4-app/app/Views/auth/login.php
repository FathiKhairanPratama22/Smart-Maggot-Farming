<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Maggot</title>
    <!-- Tailwind CSS -->
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
                        glassBg: 'rgba(17, 25, 40, 0.75)',
                        glassBorder: 'rgba(255, 255, 255, 0.125)'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0b0f19;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(188, 19, 254, 0.15), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(0, 243, 255, 0.15), transparent 25%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            margin: 0;
            overflow: hidden;
        }

        .glass-card {
            background: rgba(17, 25, 40, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5), 
                        inset 0 0 0 1px rgba(255, 255, 255, 0.05);
            border-radius: 20px;
        }

        .neon-text-blue {
            color: #fff;
            text-shadow: 0 0 5px #00f3ff, 0 0 10px #00f3ff, 0 0 20px #00f3ff;
        }

        .neon-text-purple {
            color: #fff;
            text-shadow: 0 0 5px #bc13fe, 0 0 10px #bc13fe, 0 0 20px #bc13fe;
        }

        .input-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }

        .input-glass:focus {
            outline: none;
            border-color: #00f3ff;
            box-shadow: 0 0 15px rgba(0, 243, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
        }

        .btn-neon {
            background: linear-gradient(45deg, #00f3ff, #bc13fe);
            border: none;
            color: white;
            position: relative;
            z-index: 1;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-neon::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(45deg, #bc13fe, #00f3ff);
            z-index: -1;
            transition: opacity 0.3s ease;
            opacity: 0;
        }

        .btn-neon:hover::before {
            opacity: 1;
        }

        .btn-neon:hover {
            box-shadow: 0 0 20px rgba(188, 19, 254, 0.6), 0 0 40px rgba(0, 243, 255, 0.6);
            transform: translateY(-2px);
        }

        /* Animated background blobs */
        .blob {
            position: absolute;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.6;
            animation: float 10s infinite alternate;
        }

        .blob-1 {
            width: 300px; height: 300px;
            background: #bc13fe;
            top: 10%; left: 10%;
        }

        .blob-2 {
            width: 400px; height: 400px;
            background: #00f3ff;
            bottom: 10%; right: 10%;
            animation-delay: -5s;
        }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(50px, 50px) scale(1.2); }
        }
    </style>
</head>
<body>

    <!-- Ambient Background -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="glass-card w-full max-w-md p-8 relative overflow-hidden">
        
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold tracking-wider mb-2">
                <span class="neon-text-blue">SMART</span> 
                <span class="neon-text-purple">MAGGOT</span>
            </h1>
            <p class="text-gray-400 text-sm">Monitoring System</p>
        </div>

        <div id="alertBox" class="hidden mb-4 p-3 rounded bg-red-500/20 border border-red-500/50 text-red-200 text-sm text-center"></div>

        <form id="loginForm" class="space-y-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2" for="email">Email Address</label>
                <input type="email" id="email" class="input-glass w-full rounded-lg px-4 py-3 focus:ring-0" placeholder="admin@example.com" required>
            </div>
            
            <div>
                <label class="block text-gray-300 text-sm mb-2" for="password">Password</label>
                <input type="password" id="password" class="input-glass w-full rounded-lg px-4 py-3 focus:ring-0" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-neon w-full rounded-lg px-4 py-3 font-semibold tracking-wide flex justify-center items-center">
                <span id="btnText">ACCESS SYSTEM</span>
                <svg id="btnSpinner" class="animate-spin ml-2 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-gray-500 text-xs">Secure Connection Established • V2.0</p>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const alertBox = document.getElementById('alertBox');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');

            // Reset UI
            alertBox.classList.add('hidden');
            btnText.textContent = 'AUTHENTICATING...';
            btnSpinner.classList.remove('hidden');

            try {
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ email, password })
                });

                const result = await response.json();

                if (response.ok) {
                    btnText.textContent = 'ACCESS GRANTED';
                    btnSpinner.classList.add('hidden');
                    
                    // Redirect based on role
                    setTimeout(() => {
                        if (result.data.role === 'admin') {
                            window.location.href = '/admin/dashboard';
                        } else {
                            window.location.href = '/user/dashboard';
                        }
                    }, 800);
                } else {
                    throw new Error(result.messages?.error || result.message || 'Authentication Failed');
                }
            } catch (error) {
                btnText.textContent = 'ACCESS SYSTEM';
                btnSpinner.classList.add('hidden');
                
                alertBox.textContent = error.message;
                alertBox.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
