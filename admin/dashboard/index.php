<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil parameter halaman dari URL, default ke 'home'
$page = isset($_GET['page']) ? basename($_GET['page']) : 'home';

// Tentukan base path relatif terhadap file ini (admin/dashboard/index.php)
$basePath = dirname(__DIR__); // Naik dari /admin/dashboard ke /admin

// Daftar file yang bisa diakses
$routes = [
    'peralatan'         => "$basePath/peralatan/data_peralatan.php",
    'tambah_peralatan'  => "$basePath/peralatan/tambah_peralatan.php",
    'edit_peralatan'    => "$basePath/peralatan/tambah_peralatan.php",
    'daftar_peralatan'  => "$basePath/daftar_peralatan/daftar_peralatan.php",
    'tambah_unit'       => "$basePath/daftar_peralatan/tambah_unit.php",
    'edit_unit'         => "$basePath/daftar_peralatan/tambah_unit.php",
    'pengguna'          => "$basePath/pengguna/data_pengguna.php",
    'tambah_pengguna'   => "$basePath/pengguna/tambah_pengguna.php",
    'peminjaman'        => "$basePath/peminjaman/data_peminjaman.php",
    'pengembalian'      => "$basePath/pengembalian/pengembalian.php",
    'laporan'           => "$basePath/laporan/laporan.php",
    'profil'            => "$basePath/profil/profil.php",
    'home'              => "$basePath/dashboard/admin.php"
];

// Cek apakah file tujuan ada
if (array_key_exists($page, $routes) && file_exists($routes[$page])) {
    include $routes[$page];
} else {
    echo "<div class='alert alert-danger m-4'>Halaman <strong>'$page'</strong> tidak ditemukan.</div>";
}
?>
