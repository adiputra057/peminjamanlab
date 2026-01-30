<?php
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

// Pastikan menyertakan $port agar tidak lari ke socket default
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/peminjamanlab/');
}

?>
