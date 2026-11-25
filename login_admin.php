<?php
require 'koneksi.php';

if (isset($_SESSION['id_admin'])) {
    header("Location: dashboard_admin.php");
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id_admin, nama_admin, password_hash FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['id_admin'] = $row['id_admin'];
            $_SESSION['nama_admin'] = $row['nama_admin'];
            header("Location: dashboard_admin.php");
            exit;
        }
    }
    $error = "Username atau password yang Anda masukkan salah!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - SIPD Bencana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #F4F7FA;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-wrapper {
            background: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(17, 42, 74, 0.08);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        .brand-panel {
            background: linear-gradient(160deg, #112A4A 0%, #0B1D36 100%);
            color: #FFFFFF;
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .logo-badge {
            width: 56px;
            height: 56px;
            background-color: #E65100;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid rgba(255, 255, 255, 0.25);
            margin-bottom: 20px;
        }
        .btn-sipd {
            background-color: #1565C0;
            border-color: #1565C0;
            color: #FFFFFF;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .btn-sipd:hover {
            background-color: #0D47A1;
            border-color: #0D47A1;
            color: #FFFFFF;
        }
        .form-control {
            padding: 11px 14px;
            border-radius: 8px;
            border: 1px solid #DCE4EC;
        }
        .form-control:focus {
            border-color: #1565C0;
            box-shadow: 0 0 0 0.2rem rgba(21, 101, 192, 0.15);
        }
    </style>
</head>
<body>

<div class="container px-3">
    <div class="login-wrapper mx-auto">
        <div class="row g-0">
            <!-- Panel Kiri: Branding Navy SIPD Bencana -->
            <div class="col-md-5 brand-panel">
                <div>
                    <div class="logo-badge">
                        <i class="bi bi-exclamation-triangle-fill fs-3 text-white"></i>
                    </div>
                    <h4 class="fw-bold mb-1">SIPD Bencana</h4>
                    <p class="small text-white-50 mb-4">Sistem Informasi Pelaporan dan Penanganan Bencana</p>
                    <hr class="border-secondary opacity-25">
                    <p class="small text-light opacity-75 mt-3">
                        Portal khusus Administrator untuk mengelola data master wilayah, akun petugas lapangan, serta validasi data kependudukan (NIK) masyarakat.
                    </p>
                </div>
                <div class="small text-white-50 pt-4">
                    <i class="bi bi-shield-lock me-1"></i> Akses Terbatas &bull; BPBD / Admin Sistem
                </div>
            </div>

            <!-- Panel Kanan: Form Login -->
            <div class="col-md-7 p-4 p-md-5 d-flex flex-column justify-content-center">
                <div class="mb-4">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-semibold">
                        Portal Administrator
                    </span>
                    <h3 class="fw-bold text-dark mb-1">Selamat Datang Kembali</h3>
                    <p class="text-muted small">Silakan masuk menggunakan kredensial admin Anda.</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger d-flex align-items-center py-2 small" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <div><?= htmlspecialchars($error); ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" name="username" class="form-control border-start-0" placeholder="Masukkan username admin" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" name="password" class="form-control border-start-0" placeholder="Masukkan password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sipd w-100">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Dashboard
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>