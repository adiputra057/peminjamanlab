<?php
include "../../config/config.php";

$kondisi = ''; // untuk menghindari undefined variable
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $query = mysqli_query($conn, "SELECT * FROM peralatan WHERE id = $id");
    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $kondisi = $data['kondisi'];
        // dan variabel lain seperti $nama, $jumlah, dst jika dibutuhkan
    }
}

// Hitung Total Unit Peralatan
$query_total = mysqli_query($conn, "SELECT COUNT(*) AS total FROM unit_peralatan");
$total_unit = mysqli_fetch_assoc($query_total)['total'];

// Hitung Total Kondisi Baik
$query_baik = mysqli_query($conn, "SELECT COUNT(*) AS total_baik FROM unit_peralatan WHERE kondisi = 'Baik'");
$total_baik = mysqli_fetch_assoc($query_baik)['total_baik'];

// Hitung Total Kondisi Rusak
$query_rusak = mysqli_query($conn, "SELECT COUNT(*) AS total_rusak FROM unit_peralatan WHERE kondisi = 'Rusak'");
$total_rusak = mysqli_fetch_assoc($query_rusak)['total_rusak'];

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
                <h6 class="font-weight-normal mb-0">Dashboard / <span class="text-muted">Daftar Peralatan</span></h6>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistik Box -->
      <div class="row">
        <div class="col-md-12 grid-margin transparent">
          <div class="row">
            <!-- Total Peralatan -->
            <div class="col-xl-4 col-md-6 mb-4 transparent">
              <div class="card rounded">
                <div class="card-body">
                  <div class="mb-3 d-flex justify-content-sm-between">
                    <p class="font-weight-800">Total Peralatan</p>
                    <i class="bx bx-book icon-data"></i>
                  </div>
                  <p class="fs-30 mb-3 text-primary font-weight-medium"><?= $total_unit ?></p>
                  <p class="text-muted">Total Peralatan Keseluruhan</p>
                </div>
              </div>
            </div>
            <!-- Peralatan Baik -->
            <div class="col-xl-4 col-md-6 mb-4 transparent">
              <div class="card rounded">
                <div class="card-body">
                  <div class="mb-3 d-flex justify-content-sm-between">
                    <p class="font-weight-800">Peralatan Baik</p>
                    <i class="bx bxs-check-circle icon-data-tr"></i> 
                  </div>
                  <p class="fs-30 mb-3 text-primary font-weight-medium"><?= $total_baik ?></p>
                  <p class="text-muted">Total Peralatan Kondisi Baik</p>
                </div>
              </div>
            </div>
            <!-- Peralatan Rusak -->
            <div class="col-xl-4 col-md-6 mb-4 transparent">
              <div class="card rounded">
                <div class="card-body">
                  <div class="mb-3 d-flex justify-content-sm-between">
                    <p class="font-weight-800">Peralatan Rusak</p>
                    <i class="bx bxs-x-circle icon-data-ts"></i>
                  </div>
                  <p class="fs-30 mb-3 text-primary font-weight-medium"><?= $total_rusak ?></p>
                  <p class="text-muted">Total Peralatan Kondisi Rusak</p>
                </div>
              </div>
            </div>
            
            <!-- Tabel Peralatan -->
            <div class="col-lg-12 grid-margin">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title m-0">Daftar Peralatan</h4>
                    <div class="d-flex gap-2 align-items-center">
                    <a href="index.php?page=tambah_unit" class="btn btn-primary">Tambah Unit Peralatan</a>
                    </div>
                  </div>
                  <div class="table-responsive">
                     <table class="display expandable-table text-center"style="width: 100%;">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Nama Peralatan</th>
                            <th>Kode Unit</th>
                            <th>Kondisi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $no = 1;
                          $query = "SELECT u.*, p.nama_peralatan 
                            FROM unit_peralatan u
                            JOIN peralatan p ON u.id_peralatan = p.id_peralatan
                            ORDER BY 
                              SUBSTRING_INDEX(u.kode_unit, '-', 1) ASC, 
                              CAST(SUBSTRING_INDEX(u.kode_unit, '-', -1) AS UNSIGNED) ASC";
                          $tampil = mysqli_query($conn, $query);
                          while ($data = mysqli_fetch_assoc($tampil)):
                          ?>
                            <tr>
                              <td><?= $no++ ?></td>
                              <td><?= htmlspecialchars($data['nama_peralatan']) ?></td>
                              <td><?= htmlspecialchars($data['kode_unit']) ?></td>
                              <td><?= htmlspecialchars($data['kondisi']) ?></td>
                              <td>
                                  <?php
                                  $keterangan = htmlspecialchars($data['keterangan']);
                                  $warna = 'secondary'; // default
                                  if ($keterangan == 'Tersedia') {
                                      $warna = 'primary'; // biru
                                  } elseif ($keterangan == 'Dipinjam') {
                                      $warna = 'success'; // hijau
                                  } elseif ($keterangan == 'Perlu Perbaikan') {
                                      $warna = 'danger'; // merah
                                  }
                                  ?>
                                  <span class="btn btn-<?= $warna ?> btn-sm py-2 text-white"><?= $keterangan ?></span>
                              </td>
                              <td>
                                <a href="index.php?page=edit_unit&id=<?= $data['id_unit'] ?>" class="btn btn-warning py-2">Edit</a>
                                <a href="#" class="btn btn-danger py-2" data-toggle="modal" data-target="#hapusUnit<?= $data['id_unit'] ?>">Hapus</a>
                              </td>
                            </tr>


                        <!-- Modal Hapus -->
                        <div class="modal fade" id="hapusUnit<?= $data['id_unit'] ?>" tabindex="-1" role="dialog">
                          <div class="modal-dialog">
                            <div class="modal-content">
                            <form action="../daftar_peralatan/unit_aksi.php" method="post">
                               <input type="hidden" name="id_unit" value="<?= $data['id_unit'] ?>">   
                                <div class="modal-header">
                                  <h5 class="modal-title" id="hapusUnitLabel<?= $data['id_unit'] ?>">Konfirmasi Hapus Data</h5>
                                  <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <div class="modal-body">
                                  <input type="hidden" name="id_unit" value="<?= $data['id_unit'] ?>">
                                  <p class="text-center">Apakah Anda yakin ingin menghapus data <strong><?= htmlspecialchars($data['kode_unit']) ?></strong>?</p>
                                </div>
                                <div class="modal-footer">
                                  <button type="submit" name="hapus_unit" class="btn btn-danger">Hapus</button>
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <?php endwhile; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
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
          Copyright © 2025. <a href="https://www.bootstrapdash.com/" target="_blank">Adi Putra</a> Lab Seni & Budaya
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
      sukses_tambah: "Data berhasil ditambahkan",
      sukses_hapus: "Data per-unit peralatan berhasil dihapus",
      sukses_edit: "Data berhasil diupdate",
    };

    // Status yang dianggap sukses
    let successStatuses = ['sukses_tambah', 'sukses_hapus', 'sukses_edit'];

    if (messages[status]) {
      let icon = successStatuses.includes(status) ? 'success' : 'error';
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

