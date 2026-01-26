<?php 
include '../../config/config.php';

// Total semua peminjaman
$query_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman");
$total_peminjaman = mysqli_fetch_assoc($query_total)['total'];

// Peminjaman Disetujui
$query_disetujui = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'Disetujui'");
$total_disetujui = mysqli_fetch_assoc($query_disetujui)['total'];

// Peminjaman Ditolak
$query_ditolak = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'Ditolak'");
$total_ditolak = mysqli_fetch_assoc($query_ditolak)['total'];

// Peminjaman Selesai
$query_selesai = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'Selesai'");
$total_selesai = mysqli_fetch_assoc($query_selesai)['total'];
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
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

  <!-- Main CSS -->
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
     <div class="container-scroller">
     <!-- Navbar -->
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <div class="container-fluid page-body-wrapper">
    <!-- Sidebar -->
    <?php include __DIR__ . '/../layout/sidebar.php'; ?>

    <div class="main-panel">
      <div class="content-wrapper">
        <div class="row">
          <div class="col-md-12 grid-margin">
            <div class="row">
              <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <h6 class="font-weight-normal mb-0">Dashboard / <span class="text-gray">Data Peminjaman</span></h6>
              </div>
            </div>
          </div>
        </div>

        <!-- Cards -->
         <div class="row">
        <div class="col-xl-4 col-md-6 mb-4 transparent">
              <div class="card rounded">
                <div class="card-body">
                  <div class="d-flex justify-content-sm-between mb-3 font-weight-800">
                    <p class="font-weight-800">Peminjaman</p>
                    <i class="bx bx-calendar icon-data"></i>
                  </div>
                  <p class="fs-30 mb-2 text-primary font-weight-medium mb-3"><?php echo $total_peminjaman; ?></p>
                  <p class="text-muted">Total Peminjaman</p>
                </div>
              </div>
            </div>
            <!-- Card: Disetujui -->
            <div class="col-xl-4 col-md-6 mb-4 transparent">
              <div class="card rounded">
                <div class="card-body">
                  <div class="d-flex justify-content-sm-between mb-3 font-weight-800">
                    <p class="font-weight-800">Disetujui</p>
                    <i class="bx bx-check-circle icon-data-tr"></i>  
                  </div>
                  <p class="fs-30 mb-2 text-primary font-weight-medium mb-3"><?php echo $total_disetujui; ?></p>
                  <p class="text-muted">Total Peminjaman Disetujui</p>
                </div>
              </div>
            </div>
            <!-- Card: Ditolak -->
            <div class="col-xl-4 col-md-6 mb-4 transparent">
              <div class="card rounded">
                <div class="card-body">
                  <div class="d-flex justify-content-sm-between mb-3 font-weight-800">
                    <p class="font-weight-800">Ditolak</p>
                    <i class="bx bx-x-circle icon-data-ts"></i>
                  </div>
                  <p class="fs-30 mb-2 text-primary font-weight-medium mb-3"><?php echo $total_ditolak; ?></p>
                  <p class="text-muted">Total Peminjaman Ditolak</p>
                </div>
              </div>
            </div>
            <!-- Card: Dibatalkan -->
         </div>

        <!-- Tabel Peminjaman -->
        <div class="row">
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Data Peminjaman</h4>
                <div class="table-responsive">
                  <table class="table expandable-table" style="width: 100%;">
                    <thead>
                     <tr>
                        <th>No</th>
                        <th>ID Peminjaman</th>
                        <th>Nama Peminjam</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Kegiatan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $no = 1;
                      $query = mysqli_query($conn, "
                      SELECT 
                          p.id_peminjaman,
                          p.tanggal_pengajuan,
                          p.status,
                          p.kegiatan,
                          p.keterangan,
                          p.tanggal_kembali,
                          p.tanggal_pinjam,
                          u.username,
                          u.no_hp,
                          u.nama_lengkap
                      FROM peminjaman p 
                      JOIN pengguna u ON p.id_pengguna = u.id_pengguna 
                      WHERE p.status IN ('Menunggu', 'Disetujui')
                      ORDER BY FIELD(p.status, 'Menunggu', 'Disetujui', 'Ditolak'), p.tanggal_pengajuan DESC
                      ");
                      while ($row = mysqli_fetch_assoc($query)):
                      ?>
                        <tr class="text-center">
                          <td><?= $no++ ?></td>
                          <td><?= htmlspecialchars($row['id_peminjaman']) ?></td>
                          <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                          <td><?= date('d-m-Y', strtotime($row['tanggal_pengajuan'])) ?></td>
                          <td><?= htmlspecialchars($row['kegiatan']) ?></td>
                          <td class="text-center">
                            <span class="btn btn-<?= 
                              $row['status'] === 'Menunggu' ? 'primary' : 
                              ($row['status'] === 'Disetujui' ? 'success' : 'danger') 
                            ?> py-2"><?= $row['status'] ?></span>
                          </td>
                          <td class="text-center">
                            <?php if ($row['status'] === 'Menunggu'): ?>
                              <form method="POST" action="../peminjaman/peminjaman_aksi.php" class="d-flex flex-wrap justify-content-center">
                                <input type="hidden" name="id_peminjaman" value="<?= $row['id_peminjaman'] ?>">
                                <button type="submit" name="setujui" class="btn btn-success  py-2 mx-1">
                                  Setujui
                                </button>
                                <button type="submit" name="tolak" class="btn btn-danger py-2 mx-1">
                                  Tolak
                                </button>
                                <button type="button" class="btn btn-primary py-2 mx-1" data-toggle="modal" data-target="#detailModal<?= $row['id_peminjaman'] ?>">
                                  Detail
                                </button>
                              </form>
                            <?php else: ?>
                              <button type="button" class="btn btn-primary py-2" data-toggle="modal" data-target="#detailModal<?= $row['id_peminjaman'] ?>">Detail</button>
                            <?php endif; ?>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <footer class="footer">
        <div class="d-sm-flex justify-content-center justify-content-sm-between">
          <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright &copy; 2025. <a href="#">Adi Putra</a> Lab Seni & Budaya</span>
        </div>
      </footer>
    </div>
 </div>
</div>

      <!-- MODALS - Letakkan di sini, setelah tabel utama selesai -->
      <?php
      // Reset query untuk modal
      mysqli_data_seek($query, 0); // Reset pointer ke awal
      while ($row = mysqli_fetch_assoc($query)):
      ?>
      <!-- Modal Detail -->
      <div class="modal fade" id="detailModal<?= $row['id_peminjaman'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Detail Peminjaman - <?= $row['id_peminjaman'] ?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <!-- Info Peminjam -->
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label"><strong>Nama Peminjam</strong></label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['nama_lengkap']) ?>" readonly>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label"><strong>Username</strong></label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['username']) ?>" readonly>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label"><strong>No HP</strong></label>
                    <input type="text" class="form-control" value="<?= $row['no_hp'] ?>" readonly>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label"><strong>Kegiatan</strong></label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['kegiatan']) ?>" readonly>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label"><strong>Tanggal Pinjam</strong></label>
                    <input type="date" class="form-control" value="<?= $row['tanggal_pinjam'] ?>" readonly>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label"><strong>Tanggal Kembali</strong></label>
                    <input type="date" class="form-control" value="<?= $row['tanggal_kembali'] ?>" readonly>
                  </div>
                </div>
                <div class="col-12">
                  <div class="mb-3">
                    <label class="form-label"><strong>Catatan</strong></label>
                    <textarea class="form-control" rows="2" readonly><?= htmlspecialchars($row['keterangan']) ?></textarea>
                  </div>
                </div>

                <!-- TABEL PERALATAN - HANYA MUNCUL DI MODAL -->
                <div class="col-12">
                  <hr>
                  <h6><strong>Peralatan yang Dipinjam</strong></h6>
                  <div class="table-responsive">
                      <table class="table table-bordered table-striped table-hover align-middle shadow-sm rounded">
                      <thead class="table-primary">
                        <tr>
                          <th>No</th>
                          <th>Nama Peralatan</th>
                          <th>Kode Unit</th>
                          <th>Jumlah</th>
                        </tr>
                      </thead>
                      <tbody>
                       <?php
                        $id_modal = $row['id_peminjaman'];
                        $query_peralatan = "
                          SELECT 
                            pa.nama_peralatan,
                            GROUP_CONCAT(up.kode_unit SEPARATOR ', ') AS kode_unit,
                            COUNT(up.id_unit) AS jumlah_pinjam
                          FROM detail_peminjaman dp
                          JOIN peralatan pa ON dp.id_peralatan = pa.id_peralatan
                          JOIN unit_peralatan up ON dp.id_unit = up.id_unit
                          WHERE dp.id_peminjaman = '$id_modal' AND up.keterangan = 'Dipinjam'
                          GROUP BY dp.id_peralatan
                        ";

                        $result_peralatan = mysqli_query($conn, $query_peralatan);

                        if (mysqli_num_rows($result_peralatan) > 0) {
                          $no_peralatan = 1;
                          while ($data_peralatan = mysqli_fetch_assoc($result_peralatan)):
                        ?>
                        <tr>
                          <td><?= $no_peralatan++ ?></td>
                          <td ><?= htmlspecialchars($data_peralatan['nama_peralatan']) ?></td>
                          <td><?= $data_peralatan['kode_unit'] ?></td>
                          <td><?= $data_peralatan['jumlah_pinjam'] ?> Unit</td>
                        </tr>
                        <?php 
                            endwhile;
                          } else {
                            echo "<tr><td colspan='3' class='text-center text-muted'>Tidak ada data peralatan</td></tr>";
                          }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
          </div>
        </div>
      </div>
      <?php endwhile; ?>

<?php if (isset($_SESSION['status'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    let status = "<?= $_SESSION['status'] ?>";
    let config = {
      disetujui: { title: "Berhasil disetujui!", icon: "success" },
      ditolak: { title: "Peminjaman ditolak!", icon: "warning" },
      gagal_setujui: { title: "Gagal menyetujui!", icon: "error" },
      gagal_tolak: { title: "Gagal menolak!", icon: "error" }
    };
    if (config[status]) {
      Swal.fire({
        icon: config[status].icon,
        title: config[status].title,
        showConfirmButton: false,
        timer: 2000
      });
    }
  });
</script>
<?php unset($_SESSION['status']); endif; ?>

<?php include(__DIR__ . '/../assets/includes/scripts.php'); ?>

</body>
</html>