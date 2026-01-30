<?php
// Mengambil data dari Environment Variables Railway
$host = $_ENV['MYSQLHOST'] ?? 'localhost';
$user = $_ENV['MYSQLUSER'] ?? 'root';
$pass = $_ENV['MYSQLPASSWORD'] ?? '';
$db   = $_ENV['MYSQLDATABASE'] ?? '';
$port = $_ENV['MYSQLPORT'] ?? 3306;

// Eksekusi koneksi
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/peminjamanlab/');
}

?>
