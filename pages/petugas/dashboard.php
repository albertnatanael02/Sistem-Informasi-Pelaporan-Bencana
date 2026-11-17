<?php
session_start();

// Cegah akses langsung jika belum login atau bukan role yang sesuai (contoh: admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - SIPB</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 p-8 font-sans">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?></h1>
            <p class="text-xs text-slate-500 mt-1">Role aktif: <span class="font-bold uppercase text-red-600"><?= htmlspecialchars($_SESSION['role']) ?></span></p>
        </div>
        <div class="flex gap-3">
            <a href="../../index.php" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg">Beranda</a>
            <a href="../../proses/proses-logout.php" class="px-4 py-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-lg">Logout</a>
        </div>
    </div>
</body>
</html>