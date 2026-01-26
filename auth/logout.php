<?php
session_start();

// Simpan role sebelum session dihancurkan
$role = $_SESSION['role'] ?? null;

// Hapus session
session_unset();
session_destroy();

// Arahkan sesuai role
if ($role === 'admin') {
    header("Location: login.php"); // Admin balik ke login
} else {
    header("Location: ../index.php"); // User ke dashboard publik
}
exit;
