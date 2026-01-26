<?php
include_once __DIR__ . '/../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil data user dari database jika login
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
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Website Peminjaman Alat</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="../assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="../assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  

  <!-- Main CSS File -->
  <link href="../assets/css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Updated: Nov 01 2024 with Bootstrap v5.3.3
  * Author: Adi Putra
  ======================================================== -->
</head>

<body class="index-page">
 <!--Navbar Section -->
  <header id="header" class="fixed-top">

    <nav class="navbar navbar-expand-lg navbar-light d-flex align-items-center fixed-top">
      <div class="container-fluid container-xl position-relative d-flex align-items-center">
          <button class="navbar-toggler me-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
          </button>
          <div class="logo d-flex align-items-center">
              <img src="<?= BASE_URL ?>assets/img/logo.png" alt="logo">
              <h1 class="sitename"></h1>
          </div>
          <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
              <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                  <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>index.php">Beranda</a>
                  </li>
                   <li class="nav-item">
                      <a class="nav-link" href="<?= BASE_URL ?>index.php#carapeminjaman">Cara Peminjaman</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" href="<?= BASE_URL ?>index.php#peralatan">Peralatan</a>
                  </li>
                  <li class="nav-item">
                  <a class="nav-link" href="<?= BASE_URL ?>index.php#kontak">Kontak</a>
                  </li>
              </ul>
            </div>
            <div class="profile">
            <?php if ($data): ?>
              <div class="name-user d-sm-none d-lg-inline-block me-2 fw-bold">
                <?= htmlspecialchars($data['username']) ?>
              </div>
            <?php endif; ?>
           <img src="<?= BASE_URL ?>assets/img/users.png" alt="Profile" id="profile-icon">
            <ul class="profile-link">
            <?php if (isset($_SESSION['id_pengguna'])): ?>
              <li>
              <a href="<?= BASE_URL ?>peminjaman_saya/status.php">
                <i class="bi-folder2-open me-3"></i>Peminjaman Saya
              </a>
            </li>
            <li>
              <a href="<?= BASE_URL ?>keranjang/keranjang.php">
                <i class="bi bi-cart me-3"></i>Keranjang
              </a>
            </li>
            <li>
              <a href="#" data-bs-toggle="modal" data-bs-target="#profilModal">
                <i class="bi bi-person-circle me-3"></i>Profil
              </a>
            </li>
              <?php endif; ?>
            <li>
              <?php if (!isset($_SESSION['id_pengguna'])): ?>
                <a href="<?= BASE_URL ?>auth/login.php"><i class="bi bi-box-arrow-left me-3"></i>Login</a>
              <?php else: ?>
                <a href="<?= BASE_URL ?>auth/logout.php"><i class="bi bi-box-arrow-right me-3"></i>Logout</a>
              <?php endif; ?>
            </li>
            </ul>
          </div>
      </div>
    </nav>
  </header>
  <!--/Navbar Section -->

<!-- Modal Detail Profil -->
<div class="modal fade" id="profilModal" tabindex="-1" aria-labelledby="profilModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3">
      <div class="modal-header">
        <h5 class="modal-title" id="profilModalLabel">Detail Profil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <form action="<?= BASE_URL ?>layout/profil_update.php" method="POST">
          <div class="row">
            <div class="col-12">
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= $data['username'] ?>">
              </div>
               <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" name="email" value="<?= $data['email'] ?>">
              </div>
              <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= $data['nama_lengkap'] ?>">
              </div>
               <div class="mb-3">
                <label for="no_hp" class="form-label">No HP</label>
                <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?= $data['no_hp'] ?>">
              </div>
              <?php $status = isset($data['status']) ? $data['status'] : ''; ?>
              <div class="mb-3">
                <label class="form-label">Status</label>
                <input type="text" class="form-control" name="status" value="<?= $status ?>" readonly>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password (kosongkan jika tidak diubah)</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" autocomplete="off">
              </div>
              <div class="mb-3">
                <label for="password2" class="form-label">Konfirmasi Password</label>
                <input type="password" class="form-control" id="password2" name="password2" placeholder="Konfirmasi Password" autocomplete="off">
              </div>
            </div>
          </div>
          <div class="text-end mt-3">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="" class="btn btn-secondary">Batal</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--Modal Detail Profil -->

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/aos/aos.js"></script>
  <script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="../assets/vendor/purecounter/purecounter_vanilla.js"></script>

  <!-- Main JS File -->
  <script src="../assets/js/main.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const navLinks = document.querySelectorAll(".nav-link");
    const navbarCollapse = document.getElementById("navbarNav");

    // Tutup navbar saat klik link (untuk mobile)
    navLinks.forEach(link => {
      link.addEventListener("click", function () {
        const collapse = bootstrap.Collapse.getInstance(navbarCollapse);
        if (window.innerWidth < 992 && collapse) {
          collapse.hide();
        }
      });
    });
  });
</script>

</body>
</html>