<?php
session_start();

$index = $_GET['index'] ?? null;
if ($index !== null && isset($_SESSION['keranjang'][$index])) {
    unset($_SESSION['keranjang'][$index]);
    $_SESSION['keranjang'] = array_values($_SESSION['keranjang']); // reindex agar tidak loncat
}

header("Location: keranjang.php");
exit;
