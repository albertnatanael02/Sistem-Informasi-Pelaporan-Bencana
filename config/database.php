<?php
$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
    die("Error: File .env tidak ditemukan di " . realpath(__DIR__ . '/..'));
}

$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
        continue;
    }
    list($name, $value) = explode('=', $line, 2);
    $name = trim($name);
    // Menghapus tanda kutip tunggal/ganda dan spasi di awal/akhir
    $value = trim($value, " \t\n\r\0\x0B\"'");
    $_ENV[$name] = $value;
}

$host           = $_ENV['PGHOST'] ?? '';
$port           = $_ENV['PGPORT'] ?? '5432';
$db             = $_ENV['PGDATABASE'] ?? 'neondb';
$user           = $_ENV['PGUSER'] ?? '';
$pass           = $_ENV['PGPASSWORD'] ?? '';
$sslmode        = $_ENV['PGSSLMODE'] ?? 'require';
$channelBinding = $_ENV['PGCHANNELBINDING'] ?? 'require';

if (empty($host)) {
    die("Error: PGHOST di dalam file .env tidak terbaca.");
}

$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=$sslmode;channel_binding=$channelBinding";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}