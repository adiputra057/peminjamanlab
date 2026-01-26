<?php
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/dashboard/index.php?page=home");
    } else {
        header("Location: ../index.php");
    }
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome (untuk ikon) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Main CSS File -->
  <link href="../assets/css/style.css" rel="stylesheet">

</head>
<body class="bg-light">

<?php if (isset($_SESSION['status'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
<?php
switch ($_SESSION['status']) {
    case 'login_gagal':
        echo "
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: 'Username atau password salah!',
            showConfirmButton: false,
            timer:2000
        });
        ";
        break;
}
unset($_SESSION['status']);
?>
</script>
<?php endif; ?>

  <div class="container min-vh-100 d-flex justify-content-center align-items-center">
    <div class="row justify-content-center">
     <div class="col-md-5 login-box">
        <div class="card shadow-sm p-4">
          <h3 class="text-center mb-4">Login</h3>
          <form action="proses_login.php" method="POST">
            <div class="mb-3">
              <label class="form-label">Username</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" name="username" class="form-control" placeholder="Masukan Username"  required >
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" class="form-control" placeholder="Masukan Password" required >
              </div>
            </div>
            <div class="d-grid">
              <button type="submit" name="login" class="btn btn-primary fw-bold" >Login</button>
            </div>
          </form>
          <p class="mt-3 text-center text-bold">Belum punya akun? <a href="register.php" class="text-decoration-none fw-bold">Register</a></p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>