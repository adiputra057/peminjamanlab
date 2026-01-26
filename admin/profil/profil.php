<?php
include "../../config/config.php";

// Cek apakah session id_pengguna ada
if (!isset($_SESSION['id_pengguna'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='../auth/login.php';</script>";
    exit;
}

$id = intval($_SESSION['id_pengguna']); // amankan nilai ID
// Ambil data pengguna dari database
$query = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_pengguna = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data pengguna tidak ditemukan.");
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />
    <link rel="stylesheet" href="../assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css" />
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>

    <!-- Main CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css" />
      
  </head>
  <body>
 <?php if (isset($_SESSION['status'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
      <?php
      switch ($_SESSION['status']) {
          case 'sukses_edit':
              echo "Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Profil berhasil diperbarui.', showConfirmButton: false, timer: 2000 });";
              break;
          case 'update_gagal':
              echo "Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat memperbarui profil.', showConfirmButton: false, timer: 2000 });";
              break;
          case 'password_tidak_cocok':
              echo "Swal.fire({ icon: 'warning', title: 'Password tidak cocok!', text: 'Silakan periksa kembali password Anda.', showConfirmButton: false, timer: 2000 });";
              break;
          case 'email_invalid':
              echo "Swal.fire({ icon: 'error', title: 'Email Tidak Valid!', text: 'Harap gunakan email STIKOM (@stikom-bali.ac.id).', showConfirmButton: false, timer: 2000 });";
              break;
          case 'email_terdaftar':
              echo "Swal.fire({ icon: 'error', title: 'Email Sudah Terdaftar!', text: 'Gunakan email lain.', showConfirmButton: false, timer: 2000 });";
              break;
      }
      unset($_SESSION['status']);
      ?>
  });
</script>
<?php endif; ?>


    <div class="container-scroller">
     <!-- Navbar -->
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <div class="container-fluid page-body-wrapper">
    <!-- Sidebar -->
    <?php include __DIR__ . '/../layout/sidebar.php'; ?>

        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="row">
              <div class="col-md-12 grid-margin">
                <div class="row">
                  <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h6 class="font-weight-normal mb-0">
                      Dashboard / <span class="text-gray">Profil</span>
                    </h6>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
                  <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="card-title">Profil</h4>
                       <form action="../profil/profil_update.php" method="POST" enctype="multipart/form-data">
                          <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="username">Username <code>*</code></label>
                                <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($data['username']) ?>" required>
                              </div>

                              <div class="form-group">
                                <label for="email">Email <code>*</code></label>
                                <input type="text" class="form-control" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>
                              </div>

                              <div class="form-group">
                                <label for="nama_lengkap">Nama Lengkap <code>*</code></label>
                                <input type="text" class="form-control" name="nama_lengkap" value="<?= htmlspecialchars($data['nama_lengkap']) ?>" required>
                              </div>

                              <div class="form-group">
                                <label for="no_hp">No HP <code>*</code></label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Contoh: +6281234567890" value="<?= htmlspecialchars($data['no_hp']) ?>" required>
                              </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="status">Status <code>*</code></label>
                                <select class="form-control" disabled>
                                  <option value="Mahasiswa" <?= $data['status'] == 'Mahasiswa' ? 'selected' : '' ?>>Mahasiswa</option>
                                  <option value="Pegawai" <?= $data['status'] == 'Pegawai' ? 'selected' : '' ?>>Pegawai</option>
                                  <option value="Ormawa" <?= $data['status'] == 'Ormawa' ? 'selected' : '' ?>>Ormawa</option>
                                </select>
                                <input type="hidden" name="status" value="<?= htmlspecialchars($data['status']) ?>">
                              </div>

                              <div class="form-group">
                                <label for="password">Password (kosongkan jika tidak ingin mengubah)</label>
                                <input type="password" class="form-control" name="password" id="password" autocomplete="new-password">
                              </div>

                              <div class="form-group">
                                <label for="password2">Konfirmasi Password</label>
                                <input type="password" class="form-control" name="password2" id="password2" autocomplete="new-password">
                              </div>
                            </div>
                          </div>

                          <div class="form-group mt-3">
                            <button type="submit" name="update" class="btn btn-primary mr-2">Simpan</button>
                            <a href="../dashboard/index.php?page=home" class="btn btn-secondary btn-rounded btn-fw">Batal</a>
                          </div>
                        </form>

                      </div>
                    </div>
                  </div>
            </div>
          </div>

         <footer class="footer">
                <div class="d-sm-flex justify-content-center justify-content-sm-between">
                    <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
                        Copyright &copy; 2025. <a href="#">Adi Putra</a> Lab Seni & Budaya
                    </span>
                </div>
            </footer>
        </div>
    </div>
  </div>

    <!-- Scripts -->
  <?php include(__DIR__ . '/../assets/includes/scripts.php');?>
  
  </body>
</html>
