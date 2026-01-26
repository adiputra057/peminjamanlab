<?php
include "../../config/config.php";

if (isset($_GET['id_peralatan'])) {
    $id = intval($_GET['id_peralatan']);

    // Ambil nama peralatan
    $alat = mysqli_query($conn, "SELECT nama_peralatan FROM peralatan WHERE id_peralatan = $id");
    $row = mysqli_fetch_assoc($alat);
    $nama = $row['nama_peralatan'];

    // Hapus spasi dan karakter khusus (optional)
    $nama_slug = preg_replace('/[^a-zA-Z0-9]/', '', ucwords($nama));  // Gong Besar → GongBesar

    // Hitung jumlah unit yang sudah ada
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM unit_peralatan WHERE id_peralatan = $id");
    $data = mysqli_fetch_assoc($result);
    $jumlah = $data['total'] + 1;

    // Buat kode unit
    $kode = $nama_slug . '-' . str_pad($jumlah, 2, '0', STR_PAD_LEFT);
    echo $kode;
}
