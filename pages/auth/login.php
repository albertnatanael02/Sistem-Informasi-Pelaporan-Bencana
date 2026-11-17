<?php
session_start();

// Jika sudah login, arahkan langsung ke dashboard sesuai role
if (isset($_SESSION['role'])) {
    header("Location: ../pages/" . $_SESSION['role'] . "/dashboard.php");
    exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem - SIPB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4 font-sans">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
        <!-- Header Form -->
        <div class="bg-slate-900 p-6 text-center text-white">
            <a href="../../index.php" class="inline-flex items-center justify-center w-12 h-12 bg-red-600 rounded-xl mb-3 shadow-md">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </a>
            <h1 class="text-xl font-bold tracking-tight">Masuk ke Sistem SIPB</h1>
            <p class="text-xs text-slate-400 mt-1">Portal Autentikasi Admin, Petugas Wilayah & Warga</p>
        </div>

        <div class="p-6 sm:p-8">
            <!-- Notifikasi Error Login -->
            <?php if (!empty($error)): ?>
                <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-sm mt-0.5"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <!-- Form Login -->
            <form action="../../proses/proses-login.php" method="POST" class="space-y-4">
                <div>
                    <label for="username" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" id="username" name="username" required placeholder="Masukkan username Anda"
                            class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" required placeholder="••••••••"
                            class="w-full pl-10 pr-10 py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                            <i id="eyeIcon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm shadow-md shadow-red-200 transition">
                    Masuk Sekarang <i class="fa-solid fa-arrow-right ml-1.5"></i>
                </button>
            </form>

            <!-- Panel Bantu Uji Coba Akun Dummy (Hapus saat rilis produksi) -->
            <div class="mt-6 pt-5 border-t border-slate-200">
                <p class="text-[11px] font-bold text-slate-500 uppercase mb-2 text-center">Klik untuk Uji Coba Akun Dummy (Pass: password123)</p>
                <div class="grid grid-cols-3 gap-2 text-xs">
                    <button type="button" onclick="fillDummy('admin1')" class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition">
                        Admin
                    </button>
                    <button type="button" onclick="fillDummy('petugas_rina')" class="py-1.5 px-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-lg transition">
                        Petugas
                    </button>
                    <button type="button" onclick="fillDummy('warga_siti')" class="py-1.5 px-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold rounded-lg transition">
                        Warga
                    </button>
                </div>
            </div>

            <!-- Kembali ke Beranda -->
            <div class="mt-6 text-center">
                <a href="../../index.php" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Halaman Beranda Publik
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passInput.type = 'password';
                eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        function fillDummy(user) {
            document.getElementById('username').value = user;
            document.getElementById('password').value = 'password123';
        }
    </script>
</body>
</html>