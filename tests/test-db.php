<?php
require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->query("SELECT version();");
$version = $stmt->fetchColumn();

echo "<h3>Koneksi ke Neon Berhasil!</h3>";
echo "Versi Database: " . htmlspecialchars($version);