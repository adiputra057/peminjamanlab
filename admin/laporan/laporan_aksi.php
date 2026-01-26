<?php
session_start();
include "../../config/config.php";

if (isset($_POST['hapus'])) {
    $id = $_POST['id_peminjaman'];

    // Hapus detail peminjaman dulu karena relasi
    mysqli_query($conn, "DELETE FROM detail_peminjaman WHERE id_peminjaman = '$id'");
    mysqli_query($conn, "DELETE FROM pengembalian WHERE id_peminjaman = '$id'");
    
    // Hapus utama
    $hapus = mysqli_query($conn, "DELETE FROM peminjaman WHERE id_peminjaman = '$id'");

    $_SESSION['status'] = $hapus ? 'hapus_berhasil' : 'hapus_gagal';
    header("Location: dashboard/index.php?page=laporan");
    exit;
}
