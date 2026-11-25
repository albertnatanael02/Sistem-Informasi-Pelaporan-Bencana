<?php
require 'koneksi.php';
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit;
}

$pesan = "";
if (isset($_GET['status']) && $_GET['status'] === 'sukses') {
    $pesan = "<div class='alert alert-success py-2 small'><i class='bi bi-check-circle-fill me-2'></i>User masyarakat berhasil didaftarkan!</div>";
}

// Proses Simpan Masyarakat Baru (Tanpa input username & password manual)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik             = trim($_POST['nik']);
    $nama_masyarakat = trim($_POST['nama_masyarakat']);
    $id_desa         = (int) $_POST['id_desa'];

    // Otomatis gunakan NIK sebagai username & password default agar sesuai struktur tabel database
    $auto_username = $nik;
    $auto_password = password_hash($nik, PASSWORD_DEFAULT);

    if (empty($nik) or empty($nama_masyarakat) or $id_desa <= 0) {
        $pesan = "<div class='alert alert-danger py-2 small'><i class='bi bi-exclamation-circle-fill me-2'></i>Harap lengkapi semua data dan pilih wilayah domisili hingga tingkat Desa/Kelurahan!</div>";
    } elseif (!preg_match('/^[0-9]{16}$/', $nik)) {
        $pesan = "<div class='alert alert-danger py-2 small'><i class='bi bi-exclamation-circle-fill me-2'></i>Format NIK tidak valid! NIK wajib terdiri dari 16 digit angka.</div>";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO masyarakat (id_desa, nama_masyarakat, username, password_hash, nik) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $id_desa, $nama_masyarakat, $auto_username, $auto_password, $nik);
            $stmt->execute();

            header("Location: kelola_masyarakat.php?status=sukses");
            exit;
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $pesan = "<div class='alert alert-danger py-2 small'><i class='bi bi-exclamation-circle-fill me-2'></i>Gagal: NIK <b>" . htmlspecialchars($nik) . "</b> sudah terdaftar pada akun lain!</div>";
            } else {
                $pesan = "<div class='alert alert-danger py-2 small'>Terjadi kesalahan: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}

// Ambil seluruh data wilayah berjenjang untuk dropdown bertingkat
$q_prov = $conn->query("SELECT * FROM provinsi ORDER BY nama_provinsi ASC");
$provinsi_list = $q_prov->fetch_all(MYSQLI_ASSOC);

$q_kab = $conn->query("SELECT * FROM kabupaten_kota ORDER BY nama_kabupaten_kota ASC");
$kabupaten_list = $q_kab->fetch_all(MYSQLI_ASSOC);

$q_kec = $conn->query("SELECT * FROM kecamatan ORDER BY nama_kecamatan ASC");
$kecamatan_list = $q_kec->fetch_all(MYSQLI_ASSOC);

$q_desa = $conn->query("SELECT * FROM desa_wilayah ORDER BY nama_desa ASC");
$desa_list = $q_desa->fetch_all(MYSQLI_ASSOC);

// Logika Pencarian Masyarakat (NIK, Nama, Desa, Kecamatan, Kabupaten/Kota, Provinsi)
$cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';
if ($cari !== '') {
    $like = "%" . $cari . "%";
    $stmt_cari = $conn->prepare("
        SELECT m.*, d.nama_desa, kec.nama_kecamatan, kab.nama_kabupaten_kota, kab.jenis_wilayah, prov.nama_provinsi
        FROM masyarakat m
        JOIN desa_wilayah d ON m.id_desa = d.id_desa
        JOIN kecamatan kec ON d.id_kecamatan = kec.id_kecamatan
        JOIN kabupaten_kota kab ON kec.id_kabupaten_kota = kab.id_kabupaten_kota
        JOIN provinsi prov ON kab.id_provinsi = prov.id_provinsi
        WHERE m.nama_masyarakat LIKE ? 
           OR m.nik LIKE ?
           OR d.nama_desa LIKE ?
           OR kec.nama_kecamatan LIKE ?
           OR kab.nama_kabupaten_kota LIKE ?
           OR prov.nama_provinsi LIKE ?
        ORDER BY m.id_masyarakat DESC
    ");
    $stmt_cari->bind_param("ssssss", $like, $like, $like, $like, $like, $like);
    $stmt_cari->execute();
    $daftar_user = $stmt_cari->get_result();
} else {
    $daftar_user = $conn->query("
        SELECT m.*, d.nama_desa, kec.nama_kecamatan, kab.nama_kabupaten_kota, kab.jenis_wilayah, prov.nama_provinsi
        FROM masyarakat m
        JOIN desa_wilayah d ON m.id_desa = d.id_desa
        JOIN kecamatan kec ON d.id_kecamatan = kec.id_kecamatan
        JOIN kabupaten_kota kab ON kec.id_kabupaten_kota = kab.id_kabupaten_kota
        JOIN provinsi prov ON kab.id_provinsi = prov.id_provinsi
        ORDER BY m.id_masyarakat DESC
    ");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Masyarakat - SIPD Bencana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #F4F7FA;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }
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
        .nav-menu li { margin-bottom: 6px; }
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
        .content-area { padding: 28px 32px; }
        .card-sipd {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(17, 42, 74, 0.03);
        }
        .btn-sipd {
            background-color: #1565C0;
            border-color: #1565C0;
            color: #FFFFFF;
            font-weight: 600;
        }
        .btn-sipd:hover {
            background-color: #0D47A1;
            border-color: #0D47A1;
            color: #FFFFFF;
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
            <li><a href="dashboard_admin.php"><i class="bi bi-house-door"></i> Dashboard</a></li>
            <li><a href="kelola_petugas.php"><i class="bi bi-person-badge"></i> Data Petugas</a></li>
            <li><a href="kelola_masyarakat.php" class="active"><i class="bi bi-people"></i> Data Masyarakat (NIK)</a></li>
            <li><a href="#"><i class="bi bi-geo-alt"></i> Data Wilayah</a></li>
            <li><a href="#"><i class="bi bi-clock-history"></i> Riwayat Laporan</a></li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <div class="px-3 mb-3">
            <div style="font-size: 0.72rem; color: #94A3B8;">Hak Akses Sistem</div>
            <div class="small fw-semibold text-white"><i class="bi bi-shield-check me-1 text-info"></i> Administrator Pusat</div>
        </div>
        <ul class="nav-menu p-0">
            <li><a href="logout.php" class="text-danger-emphasis"><i class="bi bi-box-arrow-left"></i> Keluar</a></li>
        </ul>
    </div>
</aside>

<!-- Main Content -->
<div class="main-wrapper">
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

    <main class="content-area">
        <div class="mb-4">
            <h4 class="fw-bold text-dark mb-1">Pengelolaan Data Masyarakat (NIK)</h4>
            <p class="text-muted small mb-0">Daftarkan akun warga menggunakan 16 digit Nomor Induk Kependudukan dan wilayah domisili.</p>
        </div>

        <!-- Form Pendaftaran User Masyarakat -->
        <div class="card-sipd mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-vcard-fill text-primary me-2"></i>Daftarkan User Masyarakat Baru</h6>
            <?= $pesan; ?>

            <form method="POST">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-secondary">Nomor Induk Kependudukan (NIK - 16 Digit) <span class="text-danger">*</span></label>
                        <input type="text" name="nik" class="form-control" maxlength="16" pattern="[0-9]{16}" placeholder="Contoh: 3273021504990001" required>
                        <div class="form-text" style="font-size: 0.75rem;">Wajib 16 digit angka sesuai KTP.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-secondary">Nama Lengkap Sesuai KTP <span class="text-danger">*</span></label>
                        <input type="text" name="nama_masyarakat" class="form-control" placeholder="Masukkan nama lengkap warga" required>
                    </div>
                </div>

                <!-- Jabaran Wilayah Domisili Berjenjang -->
                <div class="p-3 bg-light rounded-3 border mb-3">
                    <div class="small fw-bold text-dark mb-2"><i class="bi bi-geo-alt-fill text-primary me-1"></i> Wilayah Domisili Tempat Tinggal</div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small text-secondary mb-1">1. Provinsi <span class="text-danger">*</span></label>
                            <select id="select_provinsi" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Provinsi --</option>
                                <?php foreach ($provinsi_list as $prov): ?>
                                    <option value="<?= $prov['id_provinsi']; ?>"><?= htmlspecialchars($prov['nama_provinsi']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small text-secondary mb-1">2. Kabupaten / Kota <span class="text-danger">*</span></label>
                            <select id="select_kabupaten" class="form-select form-select-sm" disabled required>
                                <option value="">-- Pilih Kab/Kota --</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small text-secondary mb-1">3. Kecamatan <span class="text-danger">*</span></label>
                            <select id="select_kecamatan" class="form-select form-select-sm" disabled required>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small text-secondary mb-1">4. Desa / Kelurahan <span class="text-danger">*</span></label>
                            <select name="id_desa" id="select_desa" class="form-select form-select-sm" disabled required>
                                <option value="">-- Pilih Desa/Kelurahan --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-sipd px-4">
                        <i class="bi bi-save me-2"></i>Simpan User Masyarakat
                    </button>
                </div>
            </form>
        </div>

        <!-- Daftar & Pencarian Masyarakat -->
        <div class="card-sipd">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-table text-primary me-2"></i>Daftar User Masyarakat Terdaftar</h6>

                <!-- Form Pencarian -->
                <form method="GET" class="d-flex gap-2" style="max-width: 420px; width: 100%;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="cari" class="form-control" placeholder="Cari NIK, nama, kecamatan, kota, wilayah..." value="<?= htmlspecialchars($cari); ?>">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        <?php if ($cari !== ''): ?>
                            <a href="kelola_masyarakat.php" class="btn btn-outline-secondary">Reset</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <?php if ($cari !== ''): ?>
                <div class="small text-muted mb-2">
                    Menampilkan hasil pencarian untuk: <b>"<?= htmlspecialchars($cari); ?>"</b> (<?= $daftar_user->num_rows; ?> data ditemukan)
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>NIK</th>
                            <th>Nama Lengkap</th>
                            <th>Desa / Kelurahan</th>
                            <th>Kecamatan</th>
                            <th>Kabupaten / Kota</th>
                            <th>Provinsi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($daftar_user->num_rows > 0): ?>
                            <?php while ($u = $daftar_user->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-semibold text-primary"><?= htmlspecialchars($u['nik']); ?></td>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($u['nama_masyarakat']); ?></td>
                                    <td><?= htmlspecialchars($u['nama_desa']); ?></td>
                                    <td>Kec. <?= htmlspecialchars($u['nama_kecamatan']); ?></td>
                                    <td><?= htmlspecialchars($u['jenis_wilayah'] . " " . $u['nama_kabupaten_kota']); ?></td>
                                    <td><?= htmlspecialchars($u['nama_provinsi']); ?></td>
                                    <td>
                                        <?php if ($u['status_aktif']): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Data user masyarakat tidak ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Script Dropdown Berjenjang (Provinsi -> Kab/Kota -> Kecamatan -> Desa) -->
<script>
    const dataKabupaten = <?= json_encode($kabupaten_list); ?>;
    const dataKecamatan = <?= json_encode($kecamatan_list); ?>;
    const dataDesa      = <?= json_encode($desa_list); ?>;

    const elProv = document.getElementById('select_provinsi');
    const elKab  = document.getElementById('select_kabupaten');
    const elKec  = document.getElementById('select_kecamatan');
    const elDesa = document.getElementById('select_desa');

    elProv.addEventListener('change', function() {
        const idProv = this.value;
        elKab.innerHTML  = '<option value="">-- Pilih Kab/Kota --</option>';
        elKec.innerHTML  = '<option value="">-- Pilih Kecamatan --</option>';
        elDesa.innerHTML = '<option value="">-- Pilih Desa/Kelurahan --</option>';
        elKec.disabled   = true;
        elDesa.disabled  = true;

        if (!idProv) {
            elKab.disabled = true;
            return;
        }

        const filteredKab = dataKabupaten.filter(k => k.id_provinsi == idProv);
        filteredKab.forEach(k => {
            const opt = document.createElement('option');
            opt.value = k.id_kabupaten_kota;
            opt.textContent = `${k.jenis_wilayah} ${k.nama_kabupaten_kota}`;
            elKab.appendChild(opt);
        });
        elKab.disabled = false;
    });

    elKab.addEventListener('change', function() {
        const idKab = this.value;
        elKec.innerHTML  = '<option value="">-- Pilih Kecamatan --</option>';
        elDesa.innerHTML = '<option value="">-- Pilih Desa/Kelurahan --</option>';
        elDesa.disabled  = true;

        if (!idKab) {
            elKec.disabled = true;
            return;
        }

        const filteredKec = dataKecamatan.filter(kc => kc.id_kabupaten_kota == idKab);
        filteredKec.forEach(kc => {
            const opt = document.createElement('option');
            opt.value = kc.id_kecamatan;
            opt.textContent = kc.nama_kecamatan;
            elKec.appendChild(opt);
        });
        elKec.disabled = false;
    });

    elKec.addEventListener('change', function() {
        const idKec = this.value;
        elDesa.innerHTML = '<option value="">-- Pilih Desa/Kelurahan --</option>';

        if (!idKec) {
            elDesa.disabled = true;
            return;
        }

        const filteredDesa = dataDesa.filter(d => d.id_kecamatan == idKec);
        filteredDesa.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.id_desa;
            opt.textContent = d.nama_desa;
            elDesa.appendChild(opt);
        });
        elDesa.disabled = false;
    });
</script>

</body>
</html>