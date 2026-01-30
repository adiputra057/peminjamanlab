<?php
// Pastikan kita mengambil NILAI dari variable, bukan teks namanya saja
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');
$port = getenv('DB_PORT');

// SOLUSI ERROR TYPEERROR: Paksa port menjadi angka (integer)
$port_int = (int)$port; 

// Jika port kosong atau gagal jadi angka, gunakan default 3306
if ($port_int === 0) {
    $port_int = 3306;
}

// Gunakan variabel-variabel di atas
$conn = new mysqli($host, $user, $pass, $db, $port_int);

if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/peminjamanlab/');
}

?>
