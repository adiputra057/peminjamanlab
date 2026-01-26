<?php
include "../../config/config.php";

// Total semua pengguna
$query_total = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pengguna");
$total_pengguna = mysqli_fetch_assoc($query_total)['total'];

// Total Mahasiswa
$query_mahasiswa = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pengguna WHERE status = 'Mahasiswa'");
$total_mahasiswa = mysqli_fetch_assoc($query_mahasiswa)['total'];

// Total Pegawai
$query_pegawai = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pengguna WHERE status = 'Pegawai'");
$total_pegawai = mysqli_fetch_assoc($query_pegawai)['total'];

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
    <div class="container-scroller">
     <!-- Navbar -->
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <div class="container-fluid page-body-wrapper">
    <!-- Sidebar -->
    <?php include __DIR__ . '/../layout/sidebar.php'; ?>

  <!-- Main Panel -->
  <div class="main-panel">
    <div class="content-wrapper">
      <!-- Breadcrumb -->
       <div class="row">
        <div class="col-md-12 grid-margin">
          <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
              <h6 class="font-weight-normal mb-0">
                Dashboard / <span class="text-gray">Data Pengguna</span>
              </h6>
            </div>
          </div>
        </div>
      </div>

      <!-- Cards -->
      <div class="row">
        <div class="col-xl-4 col-md-6 mb-4 transparent">
          <div class="card rounded">
            <div class="card-body">
              <div class="mb-3 d-flex justify-content-between">
                <p class="font-weight-800">Total Pengguna</p>
                <i class="bx bx-group icon-data-ts"></i>
              </div>
              <p class="fs-30 mb-3 text-primary font-weight-medium"><?php echo $total_pengguna; ?></p>
              <p class="text-muted">Total Pengguna Terdaftar</p>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4 transparent">
          <div class="card rounded">
            <div class="card-body">
              <div class="mb-3 d-flex justify-content-between">
                <p class="font-weight-800">Mahasiswa</p>
                <i class="bx bx-user icon-data-ts"></i>
              </div>
              <p class="fs-30 mb-3 text-primary font-weight-medium"><?php echo $total_mahasiswa; ?></p>
              <p class="text-muted">Total Mahasiswa Terdaftar</p>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4 transparent">
          <div class="card rounded">
            <div class="card-body">
              <div class="mb-3 d-flex justify-content-between">
                <p class="font-weight-800">Pegawai</p>
                <i class="bx bx-group icon-data-tr"></i>
              </div>
              <p class="fs-30 mb-3 text-primary font-weight-medium"><?php echo $total_pegawai; ?></p>
              <p class="text-muted">Total Pegawai Terdaftar</p>
            </div>
          </div>
        </div>
      </div>
  

      <!-- Data Table -->
      <div class="data">
        <div class="col-md-12 grid-margin">
          <div class="card">
            <div class="card-body">
               <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title m-0">Data Pengguna</h4>
                    <div class="d-flex gap-2 align-items-center">
                    <a href="index.php?page=tambah_pengguna" class="btn btn-primary">Tambah Pengguna</a>
                    </div>
                </div>
              <div class="table-responsive">
                <table class="display expandable-table" style="width: 100%;" >
                  <thead>
                    <tr class="text-center">
                      <th>No</th>
                      <th>Username</th>
                      <th>Email</th>
                      <th>Nama Lengkap</th>
                      <th>No Telepon</th>
                      <th>Status</th>
                      <th>Role</th>
                      <th class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    $tampil_pengguna = mysqli_query($conn, "SELECT * FROM pengguna ORDER BY id_pengguna ASC");
                    while ($data = mysqli_fetch_array($tampil_pengguna)):
                    ?>
                    <tr>
                      <td><?= $no ?></td>
                      <td><?= htmlspecialchars($data['username']) ?></td>
                        <td><?= htmlspecialchars($data['email']) ?></td>
                      <td><?= htmlspecialchars($data['nama_lengkap']) ?></td>
                      <td><?= htmlspecialchars($data['no_hp']) ?></td>
                      <td><?= htmlspecialchars($data['status']) ?></td>
                      <td><?= htmlspecialchars($data['role']) ?></td>
                      <td class="text-center">
                        <a href="#" class="btn btn-primary py-2" data-toggle="modal" data-target="#modalDetail<?= $data['id_pengguna'] ?>">
                          Detail
                        </a>
                        <a href="#" class="btn btn-danger py-2" data-toggle="modal" data-target="#modalHapus<?= $data['id_pengguna'] ?>">
                         Hapus
                        </a>
                      </td>    
                    </tr>

                      <!-- Modal Detail -->
                     <div class="modal fade" id="modalDetail<?= $data['id_pengguna'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalHapusLabel" aria-hidden="true">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Detail Pengguna</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['username']) ?>" readonly>
                                  </div>
                                  <div class="form-group">
                                    <label>Email</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" readonly>
                                  </div>
                                  <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama_lengkap']) ?>" readonly>
                                  </div>
                                  <div class="form-group">
                                    <label>Status</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['status'] ?? '-') ?>" readonly>
                                  </div>
                                  <div class="form-group">
                                    <label>No HP</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['no_hp']) ?>" readonly>
                                  </div>
                                  <div class="form-group">
                                    <label>Tanggal Daftar</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['tgl_daftar']) ?>" readonly>
                                  </div>
                                  <div class="form-group">
                                    <label>Role</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['role']) ?>" readonly>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                    
                      <!-- Modal Hapus Pengguna -->
                      <div class="modal fade" id="modalHapus<?= $data['id_pengguna'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalHapusLabel" aria-hidden="true">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="modalHapusLabel">Konfirmasi Hapus Data</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <form action="../pengguna/pengguna_aksi.php" method="post">
                              <input type="hidden" name="id_pengguna" value="<?= $data['id_pengguna'] ?>">   
                              <div class="modal-body">
                                <h5 class="text-center">
                                  Apakah anda yakin ingin menghapus data <?=$data['username']?> ?
                                </h5>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" name="hapus" class="btn btn-danger">Hapus</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    <?php 
                    $no++;
                    endwhile; 
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
      <div class="d-sm-flex justify-content-center justify-content-sm-between">
        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
          Copyright © 2025. <a href="#">Adi Putra</a> Lab Seni & Budaya
        </span>
      </div>
    </footer>
  </div>
</div>
</div>


<?php if (isset($_SESSION['status'])): ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    let status = "<?php echo $_SESSION['status']; ?>";

    let messages = {
      sukses_tambah: "Data pengguna berhasil ditambahkan",
      sukses_hapus: "Data pengguna berhasil dihapus",
    };

    if (messages[status]) {
      let icon = status.includes('sukses') ? 'success' : 'error';
      Swal.fire({
        icon: icon,
        title: messages[status],
        timer: 2000,
        showConfirmButton: false
      });
    }
  </script>
  <?php unset($_SESSION['status']); ?>
<?php endif; ?>



   <!-- Scripts -->
  <?php include(__DIR__ . '/../assets/includes/scripts.php');?>

<script>
$(document).ready(function() {
    $('.display').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });
});
</script>

</body>
</html>