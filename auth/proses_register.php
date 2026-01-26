<?php
session_start();
include "../config/config.php";

// Fungsi validasi sederhana
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username     = trim($_POST['username']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $no_hp        = trim($_POST['no_hp']);
    $password     = $_POST['password'];
    $status       = isset($_POST['status']) ? strtolower(validate_input($_POST['status'])) : '';
    $email        = trim($_POST['email']); // Ambil email

    // Simpan data lama ke session
    $_SESSION['old'] = [
        'username'     => $username,
        'nama_lengkap' => $nama_lengkap,
        'no_hp'        => $no_hp,
        'status'       => $status,
        'password'     => $password,
        'email'        => $email
    ];

    // Validasi input
    if (empty($username) || empty($nama_lengkap) || empty($no_hp) || empty($password) || empty($status) || empty($email)) {
        $_SESSION['status'] = 'register_kosong';
        header("Location: register.php");
        exit();
    }

    // Validasi email Outlook
   if (!preg_match('/^[a-zA-Z0-9._%+-]+@stikom-bali\.ac\.id$/i', $email)) {
    $_SESSION['status'] = 'email_invalid';
    header("Location: register.php");
    exit();
    }      

    if (strlen($password) < 6) {
        $_SESSION['status'] = 'password_too_short';
        header("Location: register.php");
        exit();
    }

    // Cek duplikat username
    $stmt_check_username = $conn->prepare("SELECT id_pengguna FROM pengguna WHERE username = ?");
    $stmt_check_username->bind_param("s", $username);
    $stmt_check_username->execute();
    $stmt_check_username->store_result();

    if ($stmt_check_username->num_rows > 0) {
        $_SESSION['status'] = 'register_duplikat';
        $stmt_check_username->close();
        header("Location: register.php");
        exit();
    }
    $stmt_check_username->close();

    // Cek duplikat nomor HP
    $stmt_check_hp = $conn->prepare("SELECT id_pengguna FROM pengguna WHERE no_hp = ?");
    $stmt_check_hp->bind_param("s", $no_hp);
    $stmt_check_hp->execute();
    $stmt_check_hp->store_result();

    if ($stmt_check_hp->num_rows > 0) {
        $_SESSION['status'] = 'no_hp_duplikat';
        $stmt_check_hp->close();
        header("Location: register.php");
        exit();
    }
    $stmt_check_hp->close();

    // Cek duplikat email
    $stmt_check_email = $conn->prepare("SELECT id_pengguna FROM pengguna WHERE email = ?");
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $stmt_check_email->store_result();

    if ($stmt_check_email->num_rows > 0) {
        $_SESSION['status'] = 'email_duplikat';
        $stmt_check_email->close();
        header("Location: register.php");
        exit();
    }
    $stmt_check_email->close();

    // Hash password
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert pengguna baru
    $stmt_insert = $conn->prepare("INSERT INTO pengguna (username, nama_lengkap, status, no_hp, email, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("ssssss", $username, $nama_lengkap, $status, $no_hp, $email, $password_hashed);

    if ($stmt_insert->execute()) {
        $_SESSION['status'] = 'register_sukses';
        unset($_SESSION['old']);
    } else {
        $_SESSION['status'] = 'register_gagal';
        $_SESSION['error'] = $stmt_insert->error;
    }

    $stmt_insert->close();
    header("Location: register.php");
    exit();
}
?>
