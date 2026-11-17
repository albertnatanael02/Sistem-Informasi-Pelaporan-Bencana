<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/auth/login.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = "Username dan kata sandi wajib diisi.";
    header("Location: ../pages/auth/login.php");
    exit;
}

// 1. Cek Tabel Admin
$stmt = $pdo->prepare("SELECT id_admin AS id, nama_admin AS nama, password_hash FROM admin WHERE username = :u LIMIT 1");
$stmt->execute(['u' => $username]);
if ($admin = $stmt->fetch()) {
    if (password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['nama']    = $admin['nama'];
        $_SESSION['role']    = 'admin';
        header("Location: ../pages/admin/dashboard.php");
        exit;
    }
}

// 2. Cek Tabel Petugas (Validasi status_aktif)
$stmt = $pdo->prepare("SELECT id_petugas AS id, id_desa, nama_petugas AS nama, password_hash, status_aktif FROM petugas WHERE username = :u LIMIT 1");
$stmt->execute(['u' => $username]);
if ($petugas = $stmt->fetch()) {
    if (!$petugas['status_aktif']) {
        $_SESSION['login_error'] = "Akun petugas Anda sedang dinonaktifkan oleh Administrator.";
        header("Location: ../auth/login.php");
        exit;
    }
    if (password_verify($password, $petugas['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $petugas['id'];
        $_SESSION['id_desa'] = $petugas['id_desa'];
        $_SESSION['nama']    = $petugas['nama'];
        $_SESSION['role']    = 'petugas';
        header("Location: ../petugas/dashboard.php");
        exit;
    }
}

// 3. Cek Tabel Masyarakat (Diarahkan ke folder pages/warga/)
$stmt = $pdo->prepare("SELECT id_masyarakat AS id, id_desa, nama_masyarakat AS nama, password_hash FROM masyarakat WHERE username = :u LIMIT 1");
$stmt->execute(['u' => $username]);
if ($warga = $stmt->fetch()) {
    if (!empty($warga['password_hash']) && password_verify($password, $warga['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $warga['id'];
        $_SESSION['id_desa'] = $warga['id_desa'];
        $_SESSION['nama']    = $warga['nama'];
        $_SESSION['role']    = 'warga';
        header("Location: ../warga/dashboard.php");
        exit;
    }
}

$_SESSION['login_error'] = "Username atau kata sandi tidak sesuai.";
header("Location: ../pages/auth/login.php");
exit;