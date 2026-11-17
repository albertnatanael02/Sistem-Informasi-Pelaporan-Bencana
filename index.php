<?php
session_start();
// require_once __DIR__ . '/config/database.php'; // Aktifkan jika ingin mengambil data dinamis dari Neon
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPB - Sistem Informasi Pelaporan dan Pengelolaan Bencana</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Leaflet CSS untuk Peta Bencana -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <!-- 1. TOP BAR / AKSES PETUGAS & INFO DARURAT -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center gap-4">
                <span><i class="fa-solid fa-phone-volume text-red-400 mr-1"></i> Call Center Darurat: <strong class="text-white">112 / (022) 123-4567</strong></span>
                <span class="hidden sm:inline text-slate-500">|</span>
                <span class="hidden sm:inline"><i class="fa-solid fa-shield-halved text-emerald-400 mr-1"></i> Layanan Pelaporan Resmi BPBD & Dinas Terkait</span>
            </div>
            <div>
                <?php if (isset($_SESSION['role'])): ?>
                    <span class="text-emerald-400 mr-3">Halo, <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong></span>
                    <a href="pages/<?= $_SESSION['role'] ?>/dashboard.php" class="text-white hover:underline font-medium mr-3">Dashboard</a>
                    <a href="proses/proses-logout.php" class="text-red-400 hover:underline">Keluar</a>
                <?php else: ?>
                    <a href="pages/auth/login.php" class="text-slate-200 hover:text-white transition flex items-center gap-1.5 font-medium">
                        <i class="fa-solid fa-right-to-bracket text-amber-400"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 2. NAVBAR UTAMA -->
    <header class="bg-white shadow-sm sticky top-0 z-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Logo & Judul -->
            <a href="index.php" class="flex items-center gap-3">
                <div class="w-11 h-11 bg-red-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-red-200">
                    <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                </div>
                <div>
                    <span class="block text-xl font-extrabold tracking-tight text-slate-900 leading-none">SIPB</span>
                    <span class="block text-xs text-slate-500 mt-1">Sistem Informasi Pelaporan Bencana</span>
                </div>
            </a>

            <!-- Menu Navigasi -->
            <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-slate-600">
                <a href="index.php" class="text-red-600 border-b-2 border-red-600 py-1">Beranda</a>
                <a href="#peta-bencana" class="hover:text-red-600 transition">Peta Bencana</a>
                <a href="#pengumuman" class="hover:text-red-600 transition">Pengumuman</a>
                <a href="#laporan-terkini" class="hover:text-red-600 transition">Riwayat Kasus</a>
            </nav>

            <!-- Tombol Aksi Utama -->
            <div class="flex items-center gap-3">
                <a href="pages/masyarakat/cek-status.php" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-sm font-semibold transition">
                    <i class="fa-solid fa-magnifying-glass"></i> Cek Status
                </a>
                <a href="pages/masyarakat/buat-laporan.php" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-bold shadow-md shadow-red-200 transition">
                    <i class="fa-solid fa-bullhorn"></i> Lapor Bencana
                </a>
            </div>
        </div>
    </header>

    <!-- 3. HERO SECTION & STATISTIK CEPAT -->
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-red-950 text-white py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7 space-y-5">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-500/20 border border-red-400/30 text-red-200 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-red-400 animate-ping"></span>
                        Tanggap Darurat 24 Jam — Tanpa Wajib Akun
                    </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight">
                        Laporkan Kejadian Darurat & Bencana di Sekitar Anda Secara Cepat.
                    </h1>
                    <p class="text-slate-300 text-base sm:text-lg max-w-2xl leading-relaxed">
                        Platform resmi pelaporan bencana alam (banjir, longsor, kebakaran) maupun kejadian luar biasa seperti <strong class="text-white">Keracunan Makan Bergizi Gratis (MBG)</strong> di lingkungan sekolah.
                    </p>
                    <div class="flex flex-wrap gap-3 pt-2">
                        <a href="pages/masyarakat/buat-laporan.php" class="px-6 py-3.5 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-sm sm:text-base shadow-lg transition flex items-center gap-2">
                            <i class="fa-solid fa-pen-to-square"></i> Buat Laporan Sekarang
                        </a>
                        <a href="pages/masyarakat/cek-status.php" class="px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-sm sm:text-base transition flex items-center gap-2">
                            <i class="fa-solid fa-barcode"></i> Lacak Kode Unik Laporan
                        </a>
                    </div>
                </div>

                <!-- Ringkasan Statistik Publik -->
                <div class="lg:col-span-5 grid grid-cols-2 gap-4">
                    <div class="bg-white/10 backdrop-blur-md border border-white/15 p-5 rounded-2xl">
                        <div class="text-amber-400 text-2xl mb-2"><i class="fa-solid fa-truck-medical"></i></div>
                        <div class="text-3xl font-extrabold">4</div>
                        <div class="text-xs text-slate-300 mt-1">Kasus Sedang Ditangani</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md border border-white/15 p-5 rounded-2xl">
                        <div class="text-emerald-400 text-2xl mb-2"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="text-3xl font-extrabold">28</div>
                        <div class="text-xs text-slate-300 mt-1">Kasus Selesai Tertangani</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md border border-white/15 p-5 rounded-2xl">
                        <div class="text-red-400 text-2xl mb-2"><i class="fa-solid fa-bell"></i></div>
                        <div class="text-3xl font-extrabold">2</div>
                        <div class="text-xs text-slate-300 mt-1">Peringatan Darurat Aktif</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md border border-white/15 p-5 rounded-2xl">
                        <div class="text-sky-400 text-2xl mb-2"><i class="fa-solid fa-school"></i></div>
                        <div class="text-3xl font-extrabold">5</div>
                        <div class="text-xs text-slate-300 mt-1">Sekolah Terdaftar (MBG)</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. PETA SITUASI BENCANA TERKINI -->
    <section id="peta-bencana" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
        <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-map-location-dot text-red-600"></i> Peta Sebaran Bencana Terverifikasi
                    </h2>
                    <p class="text-xs text-slate-500">Menampilkan titik kejadian resmi yang telah diverifikasi oleh petugas wilayah.</p>
                </div>
                <div class="flex gap-2">
                    <input type="text" placeholder="Cari wilayah atau jenis bencana..." class="px-3.5 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 w-full sm:w-64">
                    <button class="px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-700 transition">Cari</button>
                </div>
            </div>
            <!-- Kontainer Peta Leaflet -->
            <div id="map" class="w-full h-72 sm:h-80 rounded-xl border border-slate-200 z-0"></div>
        </div>
    </section>

    <!-- 5. KONTEN UTAMA: PENGUMUMAN DARURAT (KIRI) & LAPORAN TERKINI (KANAN) -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid lg:grid-cols-12 gap-8">

            <!-- SIDEBAR KIRI: PERINGATAN DARURAT & PENGUMUMAN RESMI -->
            <aside id="pengumuman" class="lg:col-span-5 space-y-4">
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 sm:p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-amber-950 flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600"></i> Peringatan & Pengumuman
                        </h2>
                        <span class="px-2.5 py-0.5 text-xs font-bold bg-amber-200 text-amber-900 rounded-full">Resmi</span>
                    </div>

                    <div class="space-y-3">
                        <!-- Item Pengumuman 1 -->
                        <div class="bg-white p-4 rounded-xl border border-amber-200/80 shadow-sm hover:border-amber-400 transition">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="px-2 py-0.5 font-bold bg-red-100 text-red-700 rounded">Keracunan MBG</span>
                                <span class="text-slate-400">18 Nov 2026 • 01:30 WIB</span>
                            </div>
                            <h3 class="font-bold text-slate-900 text-sm">Penanganan Darurat Siswa SDN Lengkong 01</h3>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Tim medis Puskesmas Bojongsoang dan BPBD telah berada di lokasi. Distribusi paket MBG batch pagi dihentikan sementara untuk uji sampel.
                            </p>
                            <div class="mt-2 text-[11px] font-medium text-slate-500">
                                <i class="fa-solid fa-location-dot text-red-500 mr-1"></i> Desa Lengkong, Kec. Bojongsoang
                            </div>
                        </div>

                        <!-- Item Pengumuman 2 -->
                        <div class="bg-white p-4 rounded-xl border border-amber-200/80 shadow-sm hover:border-amber-400 transition">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="px-2 py-0.5 font-bold bg-blue-100 text-blue-700 rounded">Banjir</span>
                                <span class="text-slate-400">17 Nov 2026 • 19:15 WIB</span>
                            </div>
                            <h3 class="font-bold text-slate-900 text-sm">Siaga Luapan Sungai Citarum (Dayeuhkolot)</h3>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                masyarakat di sekitar Desa Citeureup diimbau mengamankan dokumen penting dan kendaraan ke titik evakuasi Balai Desa.
                            </p>
                            <div class="mt-2 text-[11px] font-medium text-slate-500">
                                <i class="fa-solid fa-location-dot text-blue-500 mr-1"></i> Desa Citeureup, Kec. Dayeuhkolot
                            </div>
                        </div>
                    </div>

                    <a href="pages/masyarakat/laporan.php" class="mt-4 block w-full text-center py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition">
                        Lihat Semua Pengumuman
                    </a>
                </div>
            </aside>

            <!-- KONTEN KANAN: TABEL LAPORAN TERKINI masyarakat -->
            <section id="laporan-terkini" class="lg:col-span-7">
                <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-5">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                <i class="fa-solid fa-list-check text-red-600"></i> Laporan Terkini Terverifikasi
                            </h2>
                            <p class="text-xs text-slate-500">Identitas pelapor (NIK & Nama) disembunyikan demi perlindungan privasi.</p>
                        </div>
                        <a href="pages/masyarakat/laporan.php" class="text-xs font-bold text-red-600 hover:underline">
                            Riwayat Kasus Lengkap <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-500 text-xs uppercase bg-slate-50">
                                    <th class="py-3 px-3 rounded-tl-lg">Waktu Lapor</th>
                                    <th class="py-3 px-3">Jenis Bencana</th>
                                    <th class="py-3 px-3">Lokasi / Sekolah</th>
                                    <th class="py-3 px-3 rounded-tr-lg">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3.5 px-3 text-xs text-slate-500 whitespace-nowrap">18 Nov 2026<br>01:10 WIB</td>
                                    <td class="py-3.5 px-3 font-semibold text-slate-900">Keracunan MBG</td>
                                    <td class="py-3.5 px-3 text-xs text-slate-600">
                                        <strong class="text-slate-800">SDN Lengkong 01</strong><br>
                                        Desa Lengkong, Bojongsoang
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Sedang Ditangani
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3.5 px-3 text-xs text-slate-500 whitespace-nowrap">17 Nov 2026<br>18:45 WIB</td>
                                    <td class="py-3.5 px-3 font-semibold text-slate-900">Banjir</td>
                                    <td class="py-3.5 px-3 text-xs text-slate-600">
                                        Desa Citeureup, Kec. Dayeuhkolot,<br>Kab. Bandung
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Sedang Ditangani
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3.5 px-3 text-xs text-slate-500 whitespace-nowrap">16 Nov 2026<br>14:20 WIB</td>
                                    <td class="py-3.5 px-3 font-semibold text-slate-900">Pohon Tumbang</td>
                                    <td class="py-3.5 px-3 text-xs text-slate-600">
                                        Kelurahan Dago, Kec. Coblong,<br>Kota Bandung
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            <i class="fa-solid fa-check text-[10px]"></i> Selesai
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <!-- 6. FOOTER -->
    <footer class="bg-slate-900 text-slate-400 text-xs py-8 border-t border-slate-800 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <p class="text-white font-bold text-sm">SIPB — Sistem Informasi Pelaporan dan Pengelolaan Bencana</p>
                <p class="mt-1">Data yang ditampilkan pada halaman publik telah melalui verifikasi petugas berwenang.</p>
            </div>
            <div class="text-center sm:text-right">
                <p>&copy; 2026 Kelompok 4 - Rekayasa Perangkat Lunak.</p>
                <p class="mt-1 text-slate-500">Mendukung Pelaporan Bencana Umum & Kasus MBG Sekolah.</p>
            </div>
        </div>
    </footer>

    <!-- Script Inisialisasi Peta Leaflet (Berpusat di Bandung) -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([-6.9685, 107.6385], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Marker Dummy Bencana
        L.marker([-6.9745, 107.6392]).addTo(map)
            .bindPopup('<b>Keracunan MBG - SDN Lengkong 01</b><br>Status: Sedang Ditangani');

        L.marker([-6.9850, 107.6250]).addTo(map)
            .bindPopup('<b>Banjir - Desa Citeureup</b><br>Status: Sedang Ditangani');
    </script>
</body>
</html>