<?php
include '../../config/config.php';
session_start();

if (isset($_POST['hapus'])) {
    // Gunakan id_peminjaman sesuai input form
    $id_peminjaman = mysqli_real_escape_string($conn, $_POST['id_peminjaman']);

    // Hapus data dari tabel pengembalian berdasarkan id_peminjaman
    $delete_query = "DELETE FROM pengembalian WHERE id_peminjaman = '$id_peminjaman'";
    $result = mysqli_query($conn, $delete_query);

    if ($result) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data pengembalian berhasil dihapus.'
        ];
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Gagal!',
            'text' => 'Gagal menghapus data: ' . mysqli_error($conn)
        ];
    }

    header("Location: ../dashboard/index.php?page=pengembalian");
    exit;
}

/* ============================================================
   VALIDASI PENGEMBALIAN
============================================================ */
if (isset($_POST['validasi'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id_peminjaman']);
    mysqli_begin_transaction($conn);

    try {
        // Ambil semua unit yang dipinjam beserta kondisi
        $unit = mysqli_query($conn, "
            SELECT up.id_unit, up.id_peralatan, up.kondisi
            FROM unit_peralatan up
            JOIN detail_peminjaman dp ON dp.id_peralatan = up.id_peralatan
            WHERE dp.id_peminjaman='$id'
        ");

        if (!$unit) throw new Exception("Gagal mengambil unit: " . mysqli_error($conn));

        while ($row = mysqli_fetch_assoc($unit)) {
            $id_unit = $row['id_unit'];
            $id_peralatan = $row['id_peralatan'];
            $kondisi = $row['kondisi'];

            if ($kondisi === 'Baik') {
                mysqli_query($conn, "UPDATE unit_peralatan SET keterangan='Tersedia' WHERE id_unit='$id_unit'");
                mysqli_query($conn, "UPDATE peralatan SET jumlah_baik = jumlah_baik + 1 WHERE id_peralatan='$id_peralatan'");
            } else { 
                mysqli_query($conn, "UPDATE unit_peralatan SET keterangan='Perlu Perbaikan' WHERE id_unit='$id_unit'");
                mysqli_query($conn, "UPDATE peralatan SET jumlah_rusak = jumlah_rusak + 1, jumlah_baik = jumlah_baik - 1 WHERE id_peralatan='$id_peralatan'");
            }
        }

        // Update status peminjaman
        $q = mysqli_query($conn, "UPDATE peminjaman SET status='Selesai' WHERE id_peminjaman='$id'");
        if (!$q) throw new Exception("Gagal update status peminjaman: " . mysqli_error($conn));

        

        mysqli_commit($conn);

        $_SESSION['status'] = 'validasi_berhasil';
        header("Location: ../dashboard/index.php?page=pengembalian");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($conn);

        $_SESSION['status'] = 'validasi_gagal';
        $_SESSION['msg'] = $e->getMessage();

        header("Location: ../dashboard/index.php?page=pengembalian");
        exit;
    }
}
?>
