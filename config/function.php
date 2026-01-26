<?php
// config/function.php

function generateKodePeminjaman($conn) {
    $prefix = 'LOAN-';
    do {
        $kodeBaru = rand(1, 9999); // Angka acak dari 1 sampai 9999
        $id_peminjaman = $prefix . str_pad($kodeBaru, 4, '0', STR_PAD_LEFT);

        $check = $conn->prepare("SELECT 1 FROM peminjaman WHERE id_peminjaman = ?");
        $check->bind_param("s", $id_peminjaman);
        $check->execute();
        $result = $check->get_result();
    } while ($result->num_rows > 0); // Ulangi jika sudah ada

    return $id_peminjaman;
}
