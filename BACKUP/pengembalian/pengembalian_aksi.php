<?php
include '../../config/config.php';
session_start();

if (isset($_POST['hapus'])) {
    $id_pengembalian = mysqli_real_escape_string($conn, $_POST['id_pengembalian']);

    // Hapus data dari tabel pengembalian
    $delete_query = "DELETE FROM pengembalian WHERE id_pengembalian = '$id_pengembalian'";
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
?>
