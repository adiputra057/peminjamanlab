<?php
include "../../config/config.php";
session_start();

// Cek login
if (!isset($_SESSION['id_pengguna'])) {
    echo "<script>alert('Anda belum login'); window.location.href='../login.php';</script>";
    exit;
}

$id = $_SESSION['id_pengguna'];

$username     = mysqli_real_escape_string($conn, trim($_POST['username']));
$nama_lengkap = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
$no_hp        = mysqli_real_escape_string($conn, trim($_POST['no_hp']));
$status       = mysqli_real_escape_string($conn, trim($_POST['status']));
$email        = mysqli_real_escape_string($conn, trim($_POST['email']));
$password     = trim($_POST['password']);
$password2    = trim($_POST['password2']);

// Validasi email STIKOM
if (!preg_match('/^[a-zA-Z0-9._%+-]+@stikom-bali\.ac\.id$/i', $email)) {
    $_SESSION['status'] = 'email_invalid';
    header("Location: ../dashboard/index.php?page=profil");
    exit;
}

// Cek email unik (kecuali email sendiri)
$cek_email = mysqli_query($conn, "SELECT id_pengguna FROM pengguna WHERE email='$email' AND id_pengguna != '$id'");
if (mysqli_num_rows($cek_email) > 0) {
    $_SESSION['status'] = 'email_terdaftar';
    header("Location: ../dashboard/index.php?page=profil");
    exit;
}

// Cek apakah salah satu field password diisi
if (!empty($password) || !empty($password2)) {
    // Jika hanya salah satu yang diisi atau tidak cocok
    if ($password !== $password2) {
        $_SESSION['status'] = 'password_tidak_cocok';
        header("Location: ../dashboard/index.php?page=profil");
        exit;
    }

    // Password cocok dan diisi, hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Update dengan password
    $query = "UPDATE pengguna 
              SET username='$username', no_hp='$no_hp', nama_lengkap='$nama_lengkap',
                  status='$status', email='$email', password='$hashed' 
              WHERE id_pengguna='$id'";
} else {
    // Update tanpa password
    $query = "UPDATE pengguna 
              SET username='$username', no_hp='$no_hp', nama_lengkap='$nama_lengkap',
                  status='$status', email='$email'
              WHERE id_pengguna='$id'";
}

// Eksekusi query
if (mysqli_query($conn, $query)) {
    $_SESSION['status'] = 'sukses_edit';
} else {
    $_SESSION['status'] = 'gagal_edit_db';
    $_SESSION['error'] = mysqli_error($conn);
}

header("Location: ../dashboard/index.php?page=profil");
exit();
?>
