<?php
// 1. Ambil nilai dari environment variables yang sudah kamu set di dashboard
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');
$port = getenv('DB_PORT');

// 2. Paksa port jadi angka
$port_int = (int)$port;

// 3. Masukkan VARIABELNYA ke sini (Tanpa tanda kutip di nama variabelnya)
$conn = new mysqli($host, $user, $pass, $db, $port_int);

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/peminjamanlab/');
}

?>
