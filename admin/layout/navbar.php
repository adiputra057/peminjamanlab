<?php
include "../../config/config.php";


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

$data = null;
if (isset($_SESSION['id_pengguna'])) {
    $id_pengguna = $_SESSION['id_pengguna'];
    $result = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_pengguna = '$id_pengguna'");
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Peminjaman Peralatan | Dashboard</title>

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="../assets/vendors/feather/feather.css" />
    <link rel="stylesheet" href="../assets/vendors/ti-icons/css/themify-icons.css" />
    <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />
    <link rel="stylesheet" href="../assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css" />
    <link rel="stylesheet" href="../assets/js/select.dataTables.min.css" />
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="../assets/css/style.css" />
  </head>
  <body>

<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="navbar-brand-wrapper d-flex align-items-center justify-content-center">
          <a class="navbar-brand brand-logo mr-5" href="dashboard_admin.php"><img src="../../assets/img/logo.png" class="mr-2" alt="logo" /></a>
          <a class="navbar-brand brand-logo-mini" href="dashboard_admin.php"><img src="../../assets/img/logo.png" alt="logo" /></a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
          <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item nav-profile dropdown d-flex align-items-center gap-2">  
            <?php if ($data): ?>
                <div class="d-none d-lg-block font-weight-bold mr-3">
                  <?= htmlspecialchars($data['username']) ?>
                </div>
              <?php endif; ?>
              
              <a class="nav-link dropdown-toggle p-0" href="#" data-toggle="dropdown" id="profileDropdown">
                <img src="../../assets/img/users.png" alt="profile" class="rounded-circle" style="width: 35px; height: 35px;" />
              </a>

              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                <a class="dropdown-item active">Administrator</a>
                <a href="../dashboard/index.php?page=profil" class="dropdown-item">
                  <i class='bx bxs-user-pin text-primary'></i> Profil
                </a>
                <a href="../../auth/logout.php" class="dropdown-item">
                  <i class="ti-power-off text-primary"></i> Logout
                </a>
              </div>
            </li>
          </ul>

          <button class="navbar-toggler navbar-toggler-right d-lg-none" type="button" data-toggle="offcanvas">
            <span class="icon-menu"></span>
          </button>
        </div>
      </nav>
    <!-- Scripts -->
  <?php include(__DIR__ . '/../assets/includes/scripts.php');?>
  </body>
</html>