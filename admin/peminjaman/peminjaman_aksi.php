<?php
include "../../config/config.php";
include "../../config/kirim_wa.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/* ============================================================
   SETUJUI PEMINJAMAN
============================================================ */
if (isset($_POST['setujui'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id_peminjaman']);
    mysqli_begin_transaction($conn);

    try {
        mysqli_query($conn, "UPDATE peminjaman SET status='Disetujui' WHERE id_peminjaman='$id'");

        $detail = mysqli_query($conn, "SELECT id_peralatan, jumlah_pinjam FROM detail_peminjaman WHERE id_peminjaman='$id'");
        while ($row = mysqli_fetch_assoc($detail)) {
            $id_peralatan = $row['id_peralatan'];
            $jumlah_pinjam = $row['jumlah_pinjam'];

            mysqli_query($conn, "UPDATE peralatan SET jumlah_baik = jumlah_baik - $jumlah_pinjam WHERE id_peralatan='$id_peralatan'");
            mysqli_query($conn, "
                UPDATE unit_peralatan SET keterangan='Dipinjam'
                WHERE id_peralatan='$id_peralatan' AND keterangan='Tersedia'
                LIMIT $jumlah_pinjam
            ");
        }

        mysqli_commit($conn);
        $_SESSION['status'] = 'disetujui';
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['status'] = 'gagal_setujui';
        error_log("Error setujui: ".$e->getMessage());
    }

    header("Location: ../dashboard/index.php?page=peminjaman");
    exit;
}

/* ============================================================
   TOLAK PEMINJAMAN
============================================================ */
if (isset($_POST['tolak'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id_peminjaman']);
    mysqli_begin_transaction($conn);

    try {
        mysqli_query($conn, "UPDATE peminjaman SET status='Ditolak' WHERE id_peminjaman='$id'");

        $detail = mysqli_query($conn, "SELECT id_peralatan, jumlah_pinjam FROM detail_peminjaman WHERE id_peminjaman='$id'");
        while ($row = mysqli_fetch_assoc($detail)) {
            $id_peralatan = $row['id_peralatan'];
            $jumlah_pinjam = $row['jumlah_pinjam'];

            mysqli_query($conn, "
                UPDATE unit_peralatan SET keterangan='Tersedia'
                WHERE id_peralatan='$id_peralatan' AND keterangan='Dipinjam'
                LIMIT $jumlah_pinjam
            ");
        }

        mysqli_commit($conn);
        $_SESSION['status'] = 'ditolak';
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['status'] = 'gagal_tolak';
        error_log("Error tolak: ".$e->getMessage());
    }

    header("Location: ../dashboard/index.php?page=peminjaman");
    exit;
}





