<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "lab";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/peminjamanlab/');
}

?>
