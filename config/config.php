<?php
// Sesuaikan dengan nama variabel di Dashboard Railway kamu (DB_HOST, DB_NAME, dll)
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: 3306;

// Lakukan koneksi
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/peminjamanlab/');
}

?>
