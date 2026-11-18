<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

// 1. Proteksi Halaman: Wajib Login sebagai Petugas
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: /pages/auth/login.php");
    exit;
}

$id_petugas   = $_SESSION['user_id'];
$id_desa      = $_SESSION['id_desa'] ?? 1;
$nama_petugas = $_SESSION['nama'] ?? 'Petugas Wilayah';

// 2. Ambil Nama Wilayah Tugas Petugas dari Database Neon
$nama_wilayah = 'Wilayah Tugas';
try {
    $stmtWilayah = $pdo->prepare("
        SELECT d.nama_desa, kec.nama_kecamatan, kab.nama_kabupaten_kota, kab.jenis_wilayah
        FROM desa_wilayah d
        JOIN kecamatan kec ON d.id_kecamatan = kec.id_kecamatan
        JOIN kabupaten_kota kab ON kec.id_kabupaten_kota = kab.id_kabupaten_kota
        WHERE d.id_desa = :id_desa
        LIMIT 1
    ");
    $stmtWilayah->execute(['id_desa' => $id_desa]);
    if ($rowWilayah = $stmtWilayah->fetch()) {
        $nama_wilayah = $rowWilayah['nama_desa'] . ', Kec. ' . $rowWilayah['nama_kecamatan'] . ' (' . $rowWilayah['jenis_wilayah'] . ' ' . $rowWilayah['nama_kabupaten_kota'] . ')';
    }
} catch (PDOException $e) {
    // Fallback jika tabel wilayah belum lengkap
    $nama_wilayah = 'Desa ID #' . htmlspecialchars($id_desa);
}

// 3. Data Dummy Laporan Masuk untuk Simulasi UI (Ganti dengan query tabel `laporan` jika sudah dibuat)
$daftar_laporan = [
    [
        'id_laporan'   => 101,
        'kode_unik'    => 'LAP-2026-0891',
        'waktu_lapor'  => '18 Nov 2026, 08:15 WIB',
        'jenis'        => 'Keracunan MBG',
        'lokasi'       => 'SDN Lengkong 01 (Desa Lengkong)',
        'pelapor'      => 'Siti Aminah',
        'keparahan'    => 'Tinggi / Darurat',
        'status'       => 'Menunggu Verifikasi',
        'is_mbg'       => true
    ],
    [
        'id_laporan'   => 102,
        'kode_unik'    => 'LAP-2026-0887',
        'waktu_lapor'  => '17 Nov 2026, 21:40 WIB',
        'jenis'        => 'Banjir',
        'lokasi'       => 'RW 04 Kampung Cijagra, Desa Lengkong',
        'pelapor'      => 'Joko Susilo',
        'keparahan'    => 'Sedang',
        'status'       => 'Sedang Ditangani',
        'is_mbg'       => false
    ],
    [
        'id_laporan'   => 103,
        'kode_unik'    => 'LAP-2026-0854',
        'waktu_lapor'  => '15 Nov 2026, 14:10 WIB',
        'jenis'        => 'Pohon Tumbang',
        'lokasi'       => 'Jl. Raya Bojongsoang KM 3, Desa Lengkong',
        'pelapor'      => 'Budi Raharjo',
        'keparahan'    => 'Rendah',
        'status'       => 'Selesai',
        'is_mbg'       => false
    ],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - SIPB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased">

    <div class="min-h-screen flex flex-col lg:flex-row">
        
        <!-- SIDEBAR NAVIGASI PETUGAS -->
        <aside class="w-full lg:w-64 bg-slate-900 text-slate-300 flex flex-col justify-between shrink-0">
            <div>
                <!-- Brand Logo -->
                <div class="p-6 border-b border-slate-800 flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center text-white shadow-md">
                        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    </div>
                    <div>
                        <span class="block text-lg font-extrabold text-white leading-none">SIPB</span>
                        <span class="block text-[11px] text-amber-400 font-semibold mt-1">Panel Petugas Wilayah</span>
                    </div>
                </div>

                <!-- Menu Sidebar -->
                <nav class="p-4 space-y-1.5 text-sm font-medium">
                    <a href="/pages/petugas/dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-red-600 text-white font-semibold shadow-sm">
                        <i class="fa-solid fa-gauge-high w-5 text-center"></i> Dashboard Utama
                    </a>
                    <a href="#daftar-laporan" class="flex items-center justify-between px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition">
                        <span class="flex items-center gap-3"><i class="fa-solid fa-clipboard-check w-5 text-center"></i> Verifikasi Laporan</span>
                        <span class="px-2 py-0.5 text-xs font-bold bg-amber-500 text-slate-950 rounded-full">1</span>
                    </a>
                    <a href="#penanganan-aktif" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition">
                        <i class="fa-solid fa-truck-medical w-5 text-center"></i> Status Penanganan
                    </a>
                    <a href="#kelola-pengumuman" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition">
                        <i class="fa-solid fa-bullhorn w-5 text-center"></i> Peringatan Darurat
                    </a>
                    <div class="pt-4 mt-4 border-t border-slate-800">
                        <a href="/index.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition text-xs">
                            <i class="fa-solid fa-globe w-5 text-center"></i> Lihat Beranda Publik
                        </a>
                    </div>
                </nav>
            </div>

            <!-- Profil Singkat & Tombol Logout -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/50">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-sm">
                        <?= strtoupper(substr($nama_petugas, 0, 1)) ?>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-white truncate"><?= htmlspecialchars($nama_petugas) ?></p>
                        <p class="text-[11px] text-emerald-400 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Petugas Aktif
                        </p>
                    </div>
                </div>
                <a href="/proses/proses-logout.php" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white border border-red-600/30 text-xs font-bold transition">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar (Logout)
                </a>
            </div>
        </aside>

        <!-- KONTEN UTAMA -->
        <div class="flex-1 flex flex-col">
            
            <!-- TOP HEADER -->
            <header class="bg-white border-b border-slate-200 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Dashboard Operasional Petugas</h1>
                    <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot text-red-600"></i>
                        <span>Wilayah Tugas: <strong class="text-slate-700"><?= htmlspecialchars($nama_wilayah) ?></strong></span>
                    </p>
                </div>
                <div class="flex items-center gap-2.5">
                    <button onclick="bukaModalPengumuman()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold shadow-sm transition">
                        <i class="fa-solid fa-plus"></i> Terbitkan Peringatan Darurat
                    </button>
                </div>
            </header>

            <!-- BODY DASHBOARD -->
            <main class="p-6 max-w-7xl w-full mx-auto space-y-6">

                <!-- 1. KARTU RINGKASAN STATISTIK WILAYAH -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase">Menunggu Verifikasi</p>
                            <p class="text-3xl font-extrabold text-amber-600 mt-1">1</p>
                            <p class="text-[11px] text-slate-400 mt-1">Perlu tindakan segera</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase">Sedang Ditangani</p>
                            <p class="text-3xl font-extrabold text-blue-600 mt-1">1</p>
                            <p class="text-[11px] text-slate-400 mt-1">Tim lapangan bertugas</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-person-digging"></i>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase">Kasus Selesai</p>
                            <p class="text-3xl font-extrabold text-emerald-600 mt-1">12</p>
                            <p class="text-[11px] text-slate-400 mt-1">Bulan ini di wilayah Anda</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase">Peringatan Aktif</p>
                            <p class="text-3xl font-extrabold text-red-600 mt-1">1</p>
                            <p class="text-[11px] text-slate-400 mt-1">Tampil di beranda publik</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-tower-broadcast"></i>
                        </div>
                    </div>
                </div>

                <!-- 2. TABEL DAFTAR LAPORAN WILAYAH (VERIFIKASI & PENANGANAN) -->
                <section id="daftar-laporan" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Daftar Laporan Masuk di Wilayah Anda</h2>
                            <p class="text-xs text-slate-500">Verifikasi laporan baru atau perbarui progres penanganan dan koordinasi relawan/PMI.</p>
                        </div>

                        <!-- Filter & Pencarian -->
                        <div class="flex flex-wrap items-center gap-2">
                            <input type="text" id="searchInput" onkeyup="filterTabel()" placeholder="Cari kode / lokasi..." class="px-3 py-2 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                            <select id="statusFilter" onchange="filterTabel()" class="px-3 py-2 text-xs border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="">Semua Status</option>
                                <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                                <option value="Sedang Ditangani">Sedang Ditangani</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="tabelLaporan" class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase text-slate-500 font-bold">
                                    <th class="py-3.5 px-4">Kode & Waktu</th>
                                    <th class="py-3.5 px-4">Jenis Bencana</th>
                                    <th class="py-3.5 px-4">Lokasi / Sekolah</th>
                                    <th class="py-3.5 px-4">Prioritas</th>
                                    <th class="py-3.5 px-4">Status</th>
                                    <th class="py-3.5 px-4 text-right">Aksi Petugas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                <?php foreach ($daftar_laporan as $lap): ?>
                                    <tr class="hover:bg-slate-50 transition baris-laporan" data-status="<?= htmlspecialchars($lap['status']) ?>">
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="font-mono font-bold text-slate-900 block"><?= htmlspecialchars($lap['kode_unik']) ?></span>
                                            <span class="text-[11px] text-slate-400"><?= htmlspecialchars($lap['waktu_lapor']) ?></span>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="font-bold text-slate-800"><?= htmlspecialchars($lap['jenis']) ?></span>
                                            <?php if ($lap['is_mbg']): ?>
                                                <span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold bg-purple-100 text-purple-700 rounded">Khusus MBG</span>
                                            <?php endif; ?>
                                            <span class="block text-[11px] text-slate-500 mt-0.5">Pelapor: <?= htmlspecialchars($lap['pelapor']) ?></span>
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-700 max-w-xs">
                                            <?= htmlspecialchars($lap['lokasi']) ?>
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <?php if (str_contains($lap['keparahan'], 'Tinggi')): ?>
                                                <span class="px-2 py-0.5 rounded bg-red-100 text-red-700 font-bold text-[11px]"><?= htmlspecialchars($lap['keparahan']) ?></span>
                                            <?php elseif ($lap['keparahan'] === 'Sedang'): ?>
                                                <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold text-[11px]"><?= htmlspecialchars($lap['keparahan']) ?></span>
                                            <?php else: ?>
                                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold text-[11px]"><?= htmlspecialchars($lap['keparahan']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <?php if ($lap['status'] === 'Menunggu Verifikasi'): ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-bold bg-amber-100 text-amber-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-pulse"></span> Menunggu Verifikasi
                                                </span>
                                            <?php elseif ($lap['status'] === 'Sedang Ditangani'): ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-bold bg-blue-100 text-blue-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Sedang Ditangani
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-bold bg-emerald-100 text-emerald-800">
                                                    <i class="fa-solid fa-check text-[10px]"></i> Selesai
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap space-x-1.5">
                                            <?php if ($lap['status'] === 'Menunggu Verifikasi'): ?>
                                                <button onclick="bukaModalVerifikasi('<?= $lap['kode_unik'] ?>', '<?= htmlspecialchars($lap['jenis']) ?>')" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition">
                                                    Verifikasi
                                                </button>
                                            <?php else: ?>
                                                <button onclick="bukaModalPenanganan('<?= $lap['kode_unik'] ?>', '<?= htmlspecialchars($lap['status']) ?>')" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-semibold transition">
                                                    Update Penanganan
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- 3. PANEL PENGELOLAAN PENGUMUMAN & PERINGATAN DARURAT -->
                <section id="kelola-pengumuman" class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Peringatan Darurat & Pengumuman Wilayah Aktif</h2>
                            <p class="text-xs text-slate-500">Pengumuman aktif akan langsung tampil pada halaman beranda masyarakat.</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 text-xs">
                        <div class="p-4 rounded-xl border border-amber-200 bg-amber-50/60 flex flex-col justify-between gap-3">
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="px-2 py-0.5 font-bold bg-red-100 text-red-700 rounded">Keracunan MBG</span>
                                    <span class="px-2 py-0.5 font-bold bg-emerald-100 text-emerald-800 rounded">Status: Aktif</span>
                                </div>
                                <h3 class="font-bold text-slate-900 text-sm">Penanganan Darurat Siswa SDN Lengkong 01</h3>
                                <p class="text-slate-600 mt-1">Tim medis Puskesmas Bojongsoang dan BPBD telah berada di lokasi. Distribusi paket MBG dihentikan sementara.</p>
                            </div>
                            <div class="flex justify-end gap-2 pt-2 border-t border-amber-200/60">
                                <button class="px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">Edit</button>
                                <button class="px-3 py-1.5 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700">Nonaktifkan</button>
                            </div>
                        </div>
                    </div>
                </section>

            </main>
        </div>
    </div>

    <!-- MODAL VERIFIKASI LAPORAN (TERIMA / TOLAK DENGAN ALASAN) -->
    <div id="modalVerifikasi" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-slate-900">Verifikasi Laporan <span id="verifKode" class="font-mono text-red-600"></span></h3>
                <button onclick="tutupModal('modalVerifikasi')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="/proses/proses-verifikasi.php" method="POST" class="space-y-4 text-xs">
                <input type="hidden" name="kode_unik" id="inputVerifKode">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Keputusan Verifikasi</label>
                    <select name="aksi_verifikasi" id="aksiVerifikasi" onchange="toggleAlasanTolak()" class="w-full p-2.5 border border-slate-300 rounded-xl bg-white">
                        <option value="terima">Terima & Terbitkan sebagai Laporan Resmi</option>
                        <option value="tolak">Tolak Laporan (Tidak Valid / Ganda)</option>
                    </select>
                </div>
                <div id="grupKeparahan">
                    <label class="block font-bold text-slate-700 mb-1">Tetapkan Tingkat Keparahan / Prioritas</label>
                    <select name="tingkat_keparahan" class="w-full p-2.5 border border-slate-300 rounded-xl bg-white">
                        <option value="Tinggi">Tinggi / Darurat Segera</option>
                        <option value="Sedang">Sedang</option>
                        <option value="Rendah">Rendah</option>
                    </select>
                </div>
                <div id="grupAlasanTolak" class="hidden">
                    <label class="block font-bold text-red-700 mb-1">Alasan Penolakan (Wajib Diisi)</label>
                    <textarea name="alasan_penolakan" rows="3" placeholder="Contoh: Laporan ganda dengan kasus LAP-2026-0890 atau bukti tidak sesuai..." class="w-full p-2.5 border border-red-300 rounded-xl"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="tutupModal('modalVerifikasi')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700">Simpan Keputusan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL UPDATE PENANGANAN & KOORDINASI EKSTERNAL -->
    <div id="modalPenanganan" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-slate-900">Update Penanganan <span id="penangananKode" class="font-mono text-blue-600"></span></h3>
                <button onclick="tutupModal('modalPenanganan')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="/proses/proses-penanganan.php" method="POST" class="space-y-4 text-xs">
                <input type="hidden" name="kode_unik" id="inputPenangananKode">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Penanganan Terkini</label>
                    <select name="status_penanganan" class="w-full p-2.5 border border-slate-300 rounded-xl bg-white">
                        <option value="Sedang Ditangani">Sedang Ditangani</option>
                        <option value="Selesai">Selesai (Tutup Kasus)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Koordinasi Pihak Eksternal (Opsional)</label>
                    <input type="text" name="koordinasi_eksternal" placeholder="Contoh: PMI Kab. Bandung, Puskesmas Bojongsoang, Relawan..." class="w-full p-2.5 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Catatan Tindakan Penanganan</label>
                    <textarea name="catatan_tindakan" rows="3" required placeholder="Tuliskan progres evakuasi atau tindakan lapangan..." class="w-full p-2.5 border border-slate-300 rounded-xl"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="tutupModal('modalPenanganan')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700">Perbarui Status</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function filterTabel() {
            const keyword = document.getElementById('searchInput').value.toLowerCase();
            const status  = document.getElementById('statusFilter').value;
            const rows    = document.querySelectorAll('.baris-laporan');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const rowStatus = row.getAttribute('data-status');
                const matchKeyword = text.includes(keyword);
                const matchStatus = status === '' || rowStatus === status;
                row.style.display = (matchKeyword && matchStatus) ? '' : 'none';
            });
        }

        function bukaModalVerifikasi(kode, jenis) {
            document.getElementById('verifKode').innerText = kode;
            document.getElementById('inputVerifKode').value = kode;
            document.getElementById('modalVerifikasi').classList.remove('hidden');
        }

        function bukaModalPenanganan(kode, status) {
            document.getElementById('penangananKode').innerText = kode;
            document.getElementById('inputPenangananKode').value = kode;
            document.getElementById('modalPenanganan').classList.remove('hidden');
        }

        function tutupModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function toggleAlasanTolak() {
            const aksi = document.getElementById('aksiVerifikasi').value;
            document.getElementById('grupAlasanTolak').classList.toggle('hidden', aksi !== 'tolak');
            document.getElementById('grupKeparahan').classList.toggle('hidden', aksi === 'tolak');
        }

        function bukaModalPengumuman() {
            alert('Form penerbitan pengumuman darurat siap dihubungkan ke /proses/proses-pengumuman.php');
        }
    </script>
</body>
</html>