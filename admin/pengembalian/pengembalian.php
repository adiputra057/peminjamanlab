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

// Peminjaman Dibatalkan
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

  <!-- Main Panel -->
  <div class="main-panel">
    <div class="content-wrapper">
      <!-- Breadcrumb -->
      <div class="row">
        <div class="col-md-12 grid-margin">
          <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
              <h6 class="font-weight-normal mb-0">
                Dashboard / <span class="text-gray">Data Pengembalian</span>
              </h6>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistik Pengembalian -->
      <div class="row">
        <div class="col-md-12 grid-margin transparent">
          <div class="row">
            <!-- Card: Total -->
            <div class="col-xl-3 col-md-6 mb-4 transparent">
              <div class="card rounded">
                <div class="card-body">
                  <div class="d-flex justify-content-sm-between mb-3 font-weight-800">
                    <p class="font-weight-800">Pengembalian</p>
                    <i class="bx bx-calendar icon-data"></i>
                  </div>
                  <p class="fs-30 mb-2 text-primary font-weight-medium mb-3"><?php echo $total_peminjaman; ?></p>
                  <p class="text-muted">Total Pengembalian</p>
                </div>
              </div>
            </div>
            <!-- Card: Disetujui -->
            <div class="col-xl-3 col-md-6 mb-4 transparent">
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
            <div class="col-xl-3 col-md-6 mb-4 transparent">
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
            <div class="col-xl-3 col-md-6 mb-4 transparent">
              <div class="card rounded">
                <div class="card-body">
                  <div class="d-flex justify-content-sm-between mb-3 font-weight-800">
                    <p class="font-weight-800">Selesai</p>
                    <i class="bx bx-check icon-data"></i>
                  </div>
                   <p class="fs-30 mb-3 text-primary font-weight-medium"><?php echo $total_selesai; ?></p>
                  <p class="text-muted">Total Peminjaman Selesai</p>
                </div>
              </div>
            </div>

             <!-- Tabel Peminjaman -->
              <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <h4 class="card-title m-0">Data Pengembalian</h4>
                    </div>
                    <div class="table-responsive">
                     <?php
                    $no = 1;
                    $query = "
                      SELECT p.id_peminjaman, pg.id_peminjaman, u.nama_lengkap, p.kegiatan, 
                            SUM(dp.jumlah_pinjam) AS jumlah, pg.tanggal_pengembalian, 
                            pg.foto_alat, pg.catatan_pengembalian, p.status
                      FROM peminjaman p
                      JOIN pengguna u ON p.id_pengguna = u.id_pengguna
                      JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
                      JOIN pengembalian pg ON pg.id_peminjaman = p.id_peminjaman
                      WHERE p.status IN ('Dikembalikan','Selesai')
                      GROUP BY p.id_peminjaman, pg.id_peminjaman, u.nama_lengkap, p.kegiatan, 
                              pg.tanggal_pengembalian, pg.foto_alat, 
                              pg.catatan_pengembalian, p.status
                      ORDER BY FIELD(p.status, 'Dikembalikan', 'Selesai'), pg.tanggal_pengembalian DESC";

                    
                    $result = mysqli_query($conn, $query);
                    ?>
                    <table class="display expandable-table" style="width: 100%;">
                    <thead >
                        <tr class="text-center">
                        <th>No</th>
                        <th>ID Peminjaman</th>
                        <th>Nama Peminjam</th>
                        <th>Kegiatan</th>
                        <th>Tanggal Pengembalian</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['id_peminjaman']) ?></td>
                            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['kegiatan']) ?></td>
                            <td class="text-center"><?= htmlspecialchars(date('d-m-Y', strtotime($row['tanggal_pengembalian']))) ?></td>
                            <td class="text-center">
                              <span class="btn btn-<?= 
                                  $row['status'] === 'Dikembalikan' ? 'danger' : 
                                  ($row['status'] === 'Selesai' ? 'primary' : 'success') 
                                ?> py-2"><?= $row['status'] ?></span>
                            </td>
                            <td class="text-center">
                                <?php if($row['status'] === 'Dikembalikan'): ?>
                                <form method="POST" action="../pengembalian/pengembalian_aksi.php" class="d-inline">
                                    <input type="hidden" name="id_peminjaman" value="<?= $row['id_peminjaman'] ?>">
                                    <button type="submit" name="validasi" class="btn btn-success py-2 mx-1">Validasi</button>
                                </form>
                                <?php endif; ?>
                                <button type="button" class="btn btn-primary py-2 mx-1" data-toggle="modal" data-target="#modalDetail<?= $row['id_peminjaman'] ?>">Detail</button>
                                <button type="button" class="btn btn-danger py-2 mx-1" data-toggle="modal" data-target="#modalHapus<?= $row['id_peminjaman'] ?>">Hapus</button>
                            </td>
                            </tr>

                            <!-- Modal Detail -->
                            <div class="modal fade" id="modalDetail<?= $row['id_peminjaman'] ?>" tabindex="-1" aria-labelledby="detailLabel<?= $row['id_peminjaman'] ?>" aria-hidden="true">
                              <div class="modal-dialog modal-lg modal-dialog-centered">
                                  <div class="modal-content rounded-3">
                                  <div class="modal-header">
                                      <h5 class="modal-title" id="detailLabel<?= $row['id_peminjaman'] ?>">Detail Pengembalian</h5>
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                          </button>
                                  </div>
                                  <div class="modal-body">
                                      <form>
                                        <div class="row">
                                            <!-- Kolom kiri: Foto -->
                                           <div class="col-md-5 text-center">
                                            <?php if (!empty($row['foto_alat'])): ?>
                                                <?php
                                                $foto = $row['foto_alat'];
                                                $path_foto = __DIR__ . "/../../$foto";
                                                if (file_exists($path_foto)) {
                                                    echo "<img src='../../$foto' class='img-fluid rounded mb-3' style='max-height: 300px;' alt='Foto Alat'>";
                                                } else {
                                                    echo "<p class='text-danger'><i>Foto tidak ditemukan di ../../$foto</i></p>";
                                                }
                                                ?>
                                            <?php else: ?>
                                                <p class="text-muted">Tidak ada foto tersedia.</p>
                                            <?php endif; ?>
                                        </div>

                                          <!-- Kolom kanan: Detail -->
                                          <div class="col-md-7">
                                          <div class="mb-3">
                                              <label class="form-label">Nama Peminjam</label>
                                              <input type="text" class="form-control" value="<?= htmlspecialchars($row['nama_lengkap']) ?>" readonly>
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label">Kegiatan</label>
                                              <input type="text" class="form-control" value="<?= htmlspecialchars($row['kegiatan']) ?>" readonly>
                                          </div>
                                          
                                          <!-- Detail Peralatan yang dipinjam -->
                                          <div class="mb-3">
                                            <label class="form-label">Detail Peralatan</label>
                                            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                              <?php
                                              // Query detail peralatan + unit
                                              $query_detail = "
                                                SELECT 
                                                  pr.nama_peralatan, 
                                                  dp.jumlah_pinjam, 
                                                  up.kode_unit,
                                                  up.kondisi 
                                                FROM detail_peminjaman dp
                                                JOIN peralatan pr ON dp.id_peralatan = pr.id_peralatan
                                                JOIN unit_peralatan up ON dp.id_unit = up.id_unit
                                                WHERE dp.id_peminjaman = '" . $row['id_peminjaman'] . "'";

                                              $result_detail = mysqli_query($conn, $query_detail);

                                              if ($result_detail && mysqli_num_rows($result_detail) > 0):
                                                while ($detail = mysqli_fetch_assoc($result_detail)):
                                              ?>
                                                <div class="mb-2">
                                                  <div class="d-flex justify-content-between align-items-center">
                                                    <strong><?= htmlspecialchars($detail['nama_peralatan']) ?></strong>
                                                    <span class="badge badge-secondary"><?= htmlspecialchars($detail['jumlah_pinjam']) ?> Unit</span>
                                                  </div>
                                                  <small class="text-muted">
                                                    Kode Unit: <strong><?= htmlspecialchars($detail['kode_unit']) ?></strong> |
                                                    Kondisi: <strong><?= htmlspecialchars($detail['kondisi']) ?></strong>
                                                  </small>
                                                </div>
                                              <?php 
                                                endwhile;
                                              else:
                                              ?>
                                                <p class="text-muted mb-0">Tidak ada detail peralatan.</p>
                                              <?php endif; ?>
                                            </div>
                                          </div>

                                          
                                          <div class="mb-3">
                                              <label class="form-label">Tanggal Pengembalian</label>
                                              <input type="text" class="form-control" value="<?= htmlspecialchars(date('d-m-Y', strtotime($row['tanggal_pengembalian']))) ?>" readonly>
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label">Catatan Pengembalian</label>
                                              <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($row['catatan_pengembalian']) ?></textarea>
                                          </div>
                                        </div>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <!-- MOdal Hapus -->
                            <div class="modal fade" id="modalHapus<?= $row['id_peminjaman'] ?>" tabindex="-1" role="dialog" >
                                <div class="modal-dialog">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalHapusLabel">Konfirmasi Hapus Data</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="../pengembalian/pengembalian_aksi.php" method="post">
                                        <input type="hidden" name="id_peminjaman" value="<?= $row['id_peminjaman'] ?>">
                                        <div class="modal-body">
                                        <h5 class="text-center">
                                            Apakah anda yakin ingin menghapus data ?
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
                            <?php endwhile; ?>
                        <?php else: ?>
                        <?php endif; ?>
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
          Copyright © 2025. <a href="#">Adi Putra</a> Lab Seni & Budaya
        </span>
      </div>
    </footer>
  </div>

  <?php if(isset($_SESSION['alert'])): ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
  Swal.fire({
      icon: '<?= $_SESSION['alert']['type'] ?>',
      title: '<?= $_SESSION['alert']['title'] ?>',
      text: '<?= $_SESSION['alert']['text'] ?>',
      showConfirmButton: false,
      timer : 2000
  });
  </script>
  <?php 
  // Hapus session setelah ditampilkan
  unset($_SESSION['alert']);
  endif; ?>

  <?php if (isset($_SESSION['status'])): ?>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
        document.addEventListener("DOMContentLoaded", function () {
          let status = "<?= $_SESSION['status'] ?>";
          let config = {
            validasi_berhasil: { title: "Pengembalian Berhasil!", icon: "success" },
            validasi_gagal: { title: "Pengembalian Gagal!", icon: "error" }
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