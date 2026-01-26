<?php
include "../../config/config.php";
session_start();

function updateJumlahPeralatan($conn, $id_peralatan) {
    // Hitung total unit
    $q1 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM unit_peralatan WHERE id_peralatan = '$id_peralatan'");
    $total = mysqli_fetch_assoc($q1)['total'];

    // Hitung unit dengan kondisi Baik
    $q2 = mysqli_query($conn, "SELECT COUNT(*) AS baik FROM unit_peralatan WHERE id_peralatan = '$id_peralatan' AND kondisi = 'Baik'");
    $baik = mysqli_fetch_assoc($q2)['baik'];

    // Hitung unit dengan kondisi Rusak
    $q3 = mysqli_query($conn, "SELECT COUNT(*) AS rusak FROM unit_peralatan WHERE id_peralatan = '$id_peralatan' AND kondisi = 'Rusak'");
    $rusak = mysqli_fetch_assoc($q3)['rusak'];

    // Update ke tabel peralatan
    mysqli_query($conn, "UPDATE peralatan SET jumlah = '$total', jumlah_baik = '$baik', jumlah_rusak = '$rusak' WHERE id_peralatan = '$id_peralatan'");
}

// ============= TAMBAH UNIT =============
if (isset($_POST['tambah_unit'])) {
    $id_peralatan = $_POST['id_peralatan'];
    $kode_unit = $_POST['kode_unit'];
    $kondisi = $_POST['kondisi'];
    $keterangan = $_POST['keterangan'];

    $query = "INSERT INTO unit_peralatan (id_peralatan, kode_unit, kondisi, keterangan) 
              VALUES ('$id_peralatan', '$kode_unit', '$kondisi', '$keterangan')";

    $result = mysqli_query($conn, $query);

    if ($result) {
        updateJumlahPeralatan($conn, $id_peralatan);
        $_SESSION['status'] = "sukses_tambah";
    } else {
        $_SESSION['status'] = "gagal_tambah";
    }

    header("Location: ../dashboard/index.php?page=daftar_peralatan");
    exit;
}

// ============= UPDATE UNIT =============
if (isset($_POST['update_unit'])) {
    $id_unit = $_POST['id_unit'];
    $id_peralatan = $_POST['id_peralatan'];
    $kode_unit = $_POST['kode_unit'];
    $kondisi = $_POST['kondisi'];
    $keterangan = $_POST['keterangan'];

    $query = "UPDATE unit_peralatan SET 
              id_peralatan = '$id_peralatan', 
              kode_unit = '$kode_unit', 
              kondisi = '$kondisi', 
              keterangan = '$keterangan' 
              WHERE id_unit = '$id_unit'";

    $result = mysqli_query($conn, $query);

    if ($result) {
        updateJumlahPeralatan($conn, $id_peralatan);
        $_SESSION['status'] = "sukses_edit";
    } else {
        $_SESSION['status'] = "gagal_edit";
    }

    header("Location: ../dashboard/index.php?page=daftar_peralatan");
    exit;
}

// ============= HAPUS UNIT =============
if (isset($_POST['hapus_unit'])) {
    $id_unit = intval($_POST['id_unit']);

    // Ambil id_peralatan sebelum hapus
    $res = mysqli_query($conn, "SELECT id_peralatan FROM unit_peralatan WHERE id_unit = '$id_unit'");
    $data = mysqli_fetch_assoc($res);
    $id_peralatan = $data['id_peralatan'];

    $query = "DELETE FROM unit_peralatan WHERE id_unit = '$id_unit'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        updateJumlahPeralatan($conn, $id_peralatan);
        $_SESSION['status'] = "sukses_hapus";
    } else {
        $_SESSION['status'] = "gagal_hapus";
    }

    header("Location: ../dashboard/index.php?page=daftar_peralatan");
    exit;
}
?>
