<?php
include_once __DIR__ . '/../config/config.php';
session_start();

// Cek login
if (!isset($_SESSION['id_pengguna'])) {
    echo "<script>alert('Anda belum login'); window.location.href='../login.php';</script>";
    exit;
}

$id = $_SESSION['id_pengguna'];
$username = mysqli_real_escape_string($conn, trim($_POST['username']));
$nama_lengkap = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
$no_hp = mysqli_real_escape_string($conn, trim($_POST['no_hp']));
$status = mysqli_real_escape_string($conn, trim($_POST['status']));
$email = mysqli_real_escape_string($conn, trim($_POST['email']));
$password = trim($_POST['password']);
$password2 = trim($_POST['password2']);

// =============================
// VALIDASI EMAIL
// =============================

// Format email harus stikom
if (!preg_match('/^[a-zA-Z0-9._%+-]+@stikom-bali\.ac\.id$/', $email)) {
    $_SESSION['status'] = 'email_invalid';
    header("Location: ../index.php");
    exit;
}

// Cek email sudah digunakan user lain
$cek_email = $conn->prepare("SELECT id_pengguna FROM pengguna WHERE email=? AND id_pengguna!=?");
$cek_email->bind_param("si", $email, $id);
$cek_email->execute();
$cek_email->store_result();

if ($cek_email->num_rows > 0) {
    $_SESSION['status'] = 'email_terdaftar';
    header("Location: ../index.php");
    exit;
}

$cek_email->close();

// =============================
// VALIDASI PASSWORD
// =============================
if (!empty($password) && !empty($password2)) {

    // Minimal 6 karakter
    if (strlen($password) < 6) {
        $_SESSION['status'] = 'password_terlalu_pendek';
        header("Location: ../index.php");
        exit;
    }

    // Cocok atau tidak
    if ($password !== $password2) {
        $_SESSION['status'] = 'password_tidak_cocok';
        header("Location: ../index.php");
        exit;
    }

    // Hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $query = "UPDATE pengguna 
              SET username='$username', no_hp='$no_hp', email='$email',
                  status='$status', nama_lengkap='$nama_lengkap', password='$hashed'
              WHERE id_pengguna='$id'";

} else {

    // Update tanpa password
    $query = "UPDATE pengguna 
              SET username='$username', no_hp='$no_hp', email='$email',
                  status='$status', nama_lengkap='$nama_lengkap'
              WHERE id_pengguna='$id'";
}

// =============================
// EKSEKUSI QUERY
// =============================
if (mysqli_query($conn, $query)) {
    $_SESSION['status'] = 'sukses_edit';
} else {
    $_SESSION['status'] = 'gagal_edit_db';
    $_SESSION['error'] = mysqli_error($conn);
}

header("Location: ../index.php");
exit;
?>
