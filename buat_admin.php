<?php
require 'koneksi.php';

$nama = "Administrator Utama";
$username = "admin";
$password = password_hash("admin123", PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admin (nama_admin, username, password_hash) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nama, $username, $password);

if ($stmt->execute()) {
    echo "Akun admin berhasil dibuat! Username: <b>admin</b> | Password: <b>admin123</b>";
} else {
    echo "Gagal atau admin sudah ada: " . $conn->error;
}
?>