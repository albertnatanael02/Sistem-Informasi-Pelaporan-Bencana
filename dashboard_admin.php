<?php
require 'koneksi.php';
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit;
}

// Hitung ringkasan data dari database untuk ditampilkan di kartu dashboard
$total_petugas = $conn->query("SELECT COUNT(*) AS jml FROM petugas")->fetch_assoc()['jml'];
$total_user    = $conn->query("SELECT COUNT(*) AS jml FROM masyarakat")->fetch_assoc()['jml'];
$total_wilayah = $conn->query("SELECT COUNT(*) AS jml FROM desa_wilayah")->fetch_assoc()['jml'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIPD Bencana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #F4F7FA;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }
        /* Sidebar sesuai referensi gambar */
        .sidebar {
            width: 265px;
            min-height: 100vh;
            background-color: #112A4A;
            color: #FFFFFF;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .logo-circle {
            width: 40px;
            height: 40px;
            background-color: #E65100;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #FFFFFF;
            flex-shrink: 0;
        }
        .nav-menu {
            padding: 16px 12px;
            list-style: none;
            margin: 0;
        }
        .nav-menu li {
            margin-bottom: 6px;
        }
        .nav-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            color: #CBD5E1;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.92rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .nav-menu a:hover {
            background-color: rgba(255, 255, 255, 0.08);
            color: #FFFFFF;
        }
        .nav-menu a.active {
            background-color: #1565C0;
            color: #FFFFFF;
            font-weight: 600;
        }
        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }
        /* Konten Utama */
        .main-wrapper {
            margin-left: 265px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background-color: #FFFFFF;
            height: 70px;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            border-bottom: 1px solid #E2E8F0;
        }
        .content-area {
            padding: 28px 32px;
        }
        .stat-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 2px 8px rgba(17, 42, 74, 0.03);
            transition: transform 0.15s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div>
        <div class="sidebar-brand">
            <div class="logo-circle">
                <i class="bi bi-exclamation-triangle-fill text-white fs-5"></i>
            </div>
            <div>
                <div class="fw-bold fs-6 lh-1">SIPD Bencana</div>
                <div style="font-size: 0.68rem; color: #94A3B8; margin-top: 4px;">
                    Sistem Informasi Pelaporan & Penanganan Bencana
                </div>
            </div>
        </div>

        <ul class="nav-menu">
            <li>
                <a href="dashboard_admin.php" class="active">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="kelola_petugas.php">
                    <i class="bi bi-person-badge"></i> Data Petugas
                </a>
            </li>
            <li>
                <a href="kelola_masyarakat.php">
                    <i class="bi bi-people"></i> Data Masyarakat (NIK)
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-geo-alt"></i> Data Wilayah
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-clock-history"></i> Riwayat Laporan
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <div class="px-3 mb-3">
            <div style="font-size: 0.72rem; color: #94A3B8;">Hak Akses Sistem</div>
            <div class="small fw-semibold text-white"><i class="bi bi-shield-check me-1 text-info"></i> Administrator Pusat</div>
        </div>
        <ul class="nav-menu p-0">
            <li>
                <a href="logout.php" class="text-danger-emphasis">
                    <i class="bi bi-box-arrow-left"></i> Keluar
                </a>
            </li>
        </ul>
    </div>
</aside>

<!-- Main Content -->
<div class="main-wrapper">
    <!-- Topbar Profil (Kanan Atas seperti gambar) -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                <i class="bi bi-person-fill fs-5"></i>
            </div>
            <div class="lh-sm">
                <div class="fw-bold text-dark small"><?= htmlspecialchars($_SESSION['nama_admin']); ?></div>
                <div class="text-muted" style="font-size: 0.75rem;">Administrator Sistem</div>
            </div>
        </div>
    </header>

    <!-- Isi Halaman Dashboard -->
    <main class="content-area">
        <div class="mb-4">
            <h4 class="fw-bold text-dark mb-1">Dashboard Administrator</h4>
            <p class="text-muted small mb-0">Ringkasan data master akun petugas, masyarakat, dan cakupan wilayah tugas.</p>
        </div>

        <!-- Kartu Statistik -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold mb-1">Total Petugas Terdaftar</div>
                        <h3 class="fw-bold text-dark mb-0"><?= $total_petugas; ?></h3>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold mb-1">Total User Masyarakat</div>
                        <h3 class="fw-bold text-dark mb-0"><?= $total_user; ?></h3>
                    </div>
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold mb-1">Wilayah Desa/Kelurahan</div>
                        <h3 class="fw-bold text-dark mb-0"><?= $total_wilayah; ?></h3>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Akses Cepat Tugas Utama Admin -->
        <div class="stat-card">
            <h6 class="fw-bold text-dark mb-3">Akses Cepat Pengelolaan Akun</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-light bg-opacity-50">
                        <div>
                            <div class="fw-bold text-dark small">Pendaftaran Petugas Lapangan</div>
                            <div class="text-muted" style="font-size: 0.8rem;">Input nomor pegawai dan penempatan wilayah tugas.</div>
                        </div>
                        <a href="kelola_petugas.php" class="btn btn-primary btn-sm px-3">Kelola</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-light bg-opacity-50">
                        <div>
                            <div class="fw-bold text-dark small">Pendaftaran User Masyarakat (NIK)</div>
                            <div class="text-muted" style="font-size: 0.8rem;">Daftarkan akun warga beserta 16 digit NIK & domisili.</div>
                        </div>
                        <a href="kelola_masyarakat.php" class="btn btn-primary btn-sm px-3">Kelola</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>