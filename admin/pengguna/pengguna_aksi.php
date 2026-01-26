<?php
session_start();
include "../../config/config.php";

// Fungsi untuk validasi input
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// ================================
// PROSES TAMBAH PENGGUNA
// ================================
if (isset($_POST['simpan'])) {

    $username     = validate_input($_POST['username']);
    $nama_lengkap = validate_input($_POST['nama_lengkap']);
    $no_hp        = validate_input($_POST['no_hp']);
    $status       = validate_input($_POST['status']);
    $role         = validate_input($_POST['role']);
    $email        = validate_input($_POST['email']);
    $password     = $_POST['password'];

    // Simpan input jika gagal
    $_SESSION['old'] = [
        'username'     => $username,
        'nama_lengkap' => $nama_lengkap,
        'no_hp'        => $no_hp,
        'status'       => $status,
        'role'         => $role,
        'email'        => $email
    ];

    // ---------- Validasi panjang password ----------
    if (strlen($password) < 6) {
        $_SESSION['status'] = 'password_terlalu_pendek';
        header("Location: ../dashboard/index.php?page=tambah_pengguna");
        exit();
    }

    // ---------- Validasi email STIKOM ----------
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@stikom-bali\.ac\.id$/i', $email)) {
        $_SESSION['status'] = 'email_invalid';
        header("Location: ../dashboard/index.php?page=tambah_pengguna");
        exit();
    }

    // ---------- Cek username unik ----------
    $stmt_user = $conn->prepare("SELECT id_pengguna FROM pengguna WHERE username=?");
    $stmt_user->bind_param("s", $username);
    $stmt_user->execute();
    $stmt_user->store_result();

    if ($stmt_user->num_rows > 0) {
        $_SESSION['status'] = 'username_terdaftar';
        $stmt_user->close();
        header("Location: ../dashboard/index.php?page=tambah_pengguna");
        exit();
    }
    $stmt_user->close();

    // ---------- Cek email unik ----------
    $stmt_email = $conn->prepare("SELECT id_pengguna FROM pengguna WHERE email=?");
    $stmt_email->bind_param("s", $email);
    $stmt_email->execute();
    $stmt_email->store_result();

    if ($stmt_email->num_rows > 0) {
        $_SESSION['status'] = 'email_terdaftar';
        $stmt_email->close();
        header("Location: ../dashboard/index.php?page=tambah_pengguna");
        exit();
    }
    $stmt_email->close();

    // ---------- Enkripsi password ----------
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // ---------- Insert pengguna ----------
    $stmt_insert = $conn->prepare("
        INSERT INTO pengguna (username, nama_lengkap, status, no_hp, role, email, password) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt_insert->bind_param("sssssss", $username, $nama_lengkap, $status, $no_hp, $role, $email, $password_hash);

    if ($stmt_insert->execute()) {
        $_SESSION['status'] = 'sukses_tambah';
        unset($_SESSION['old']); // hapus data lama
    } else {
        $_SESSION['status'] = 'gagal_tambah';
        $_SESSION['error'] = $stmt_insert->error;
    }

    $stmt_insert->close();
    header("Location: ../dashboard/index.php?page=pengguna");
    exit();
}

// ================================
// PROSES HAPUS PENGGUNA
// ================================
if (isset($_POST['hapus'])) {

    $id = $_POST['id_pengguna'];

    $stmt_hapus = $conn->prepare("DELETE FROM pengguna WHERE id_pengguna=?");
    $stmt_hapus->bind_param("i", $id);

    if ($stmt_hapus->execute()) {
        $_SESSION['status'] = 'sukses_hapus';
    } else {
        $_SESSION['status'] = 'gagal_hapus_db';
        $_SESSION['error'] = $stmt_hapus->error;
    }

    $stmt_hapus->close();

    header("Location: ../dashboard/index.php?page=pengguna");
    exit();
}

// Redirect default
header("Location: ../dashboard/index.php?page=pengguna");
exit();
?>
