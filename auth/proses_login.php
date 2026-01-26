<?php
session_start();
include "../config/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM pengguna WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $data = mysqli_fetch_assoc($result);

        if (password_verify($password, $data['password'])) {
            session_unset();
            session_destroy();
            session_start(); // mulai ulang
            session_regenerate_id(true);

            $_SESSION['id_pengguna'] = $data['id_pengguna'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = strtolower($data['role']);

            // Redirect berdasarkan role
            if ($_SESSION['role'] === 'admin') {
                header("Location: ../admin/dashboard/index.php?page=home");
            } else {
                header("Location: ../index.php");
            }
            exit;
        }
    }

    // Gagal login
    $_SESSION['status'] = 'login_gagal';
    header("Location: login.php");
    exit;
}
?>
