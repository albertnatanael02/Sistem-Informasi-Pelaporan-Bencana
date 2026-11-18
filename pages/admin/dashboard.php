<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigasi -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>AdminPanel</h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="active"><a href="#">Dashboard</a></li>
                    <li><a href="#">Manajemen User</a></li>
                    <li><a href="#">User Groups / Role</a></li>
                    <li><a href="#">Data & Laporan</a></li>
                    <li><a href="#">Pengaturan</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../../proses/proses-logout.php" class="logout-btn">Log Out</a>
            </div>
        </aside>

        <!-- Konten Utama -->
        <div class="main-wrapper">
            <!-- Header / Topbar -->
            <header class="topbar">
                <div class="search-box">
                    <input type="text" placeholder="Cari data, pengguna, atau laporan...">
                </div>
                <div class="user-profile">
                    <span>Halo, <strong>Admin</strong></span>
                    <div class="avatar">A</div>
                </div>
            </header>

            <!-- Area Konten -->
            <main class="content">
                <div class="page-title">
                    <h1>Ringkasan Sistem</h1>
                    <p>Selamat datang kembali di panel kontrol administrator.</p>
                </div>

                <!-- Kartu Statistik (Summary Cards) -->
                <section class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Pengguna</h3>
                        <p class="stat-number">1,245</p>
                        <span class="stat-trend positive">+12% bulan ini</span>
                    </div>
                    <div class="stat-card">
                        <h3>Role Aktif</h3>
                        <p class="stat-number">4</p>
                        <span class="stat-trend neutral">Admin, Lecturer, Student, Staff</span>
                    </div>
                    <div class="stat-card">
                        <h3>Total Transaksi</h3>
                        <p class="stat-number">Rp 48.5 Jt</p>
                        <span class="stat-trend positive">+8.4% dari minggu lalu</span>
                    </div>
                    <div class="stat-card">
                        <h3>Laporan Pending</h3>
                        <p class="stat-number">7</p>
                        <span class="stat-trend negative">Perlu ditinjau</span>
                    </div>
                </section>

                <!-- Tabel Data Terbaru -->
                <section class="table-section">
                    <div class="section-header">
                        <h2>Pengguna Terbaru</h2>
                        <button class="btn-primary">+ Tambah Data</button>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Lengkap</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#USR-001</td>
                                    <td>Budi Santoso</td>
                                    <td>budisantoso</td>
                                    <td><span class="badge role-admin">Admin</span></td>
                                    <td><span class="status active">Aktif</span></td>
                                    <td>
                                        <button class="btn-edit">Edit</button>
                                        <button class="btn-delete">Hapus</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#USR-002</td>
                                    <td>Siti Aminah</td>
                                    <td>sitiaminah</td>
                                    <td><span class="badge role-lecturer">Lecturer</span></td>
                                    <td><span class="status active">Aktif</span></td>
                                    <td>
                                        <button class="btn-edit">Edit</button>
                                        <button class="btn-delete">Hapus</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#USR-003</td>
                                    <td>Andi Wijaya</td>
                                    <td>andiwijaya</td>
                                    <td><span class="badge role-student">Student</span></td>
                                    <td><span class="status inactive">Nonaktif</span></td>
                                    <td>
                                        <button class="btn-edit">Edit</button>
                                        <button class="btn-delete">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>
</body>
</html>