<?php
require 'koneksi.php';

$username = 'admin';
$password_baru = 'admin123';
$hash = password_hash($password_baru, PASSWORD_DEFAULT);

// Pastikan kolom password_hash cukup panjang (255 karakter)
$conn->query("ALTER TABLE admin MODIFY password_hash VARCHAR(255) NOT NULL");

// Hapus data admin lama jika ada, lalu masukkan yang baru dengan hash asli PHP
$conn->query("DELETE FROM admin WHERE username = 'admin'");

$stmt = $conn->prepare("INSERT INTO admin (nama_admin, username, password_hash) VALUES ('Administrator SIPD', ?, ?)");
$stmt->bind_param("ss", $username, $hash);

if ($stmt->execute()) {
    echo "<h3>Berhasil!</h3>";
    echo "Username: <b>admin</b><br>";
    echo "Password: <b>admin123</b><br><br>";
    echo "<a href='login_admin.php'>Klik di sini untuk Login Sekarang</a>";
} else {
    echo "Gagal: " . $conn->error;
}
?>