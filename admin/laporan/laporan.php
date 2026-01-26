<?php 
include "../../config/config.php";

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
                Dashboard / <span class="text-gray">Laporan Peminjaman</span>
              </h6>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistik Peminjaman -->
      <div class="row">
        <div class="col-md-12 grid-margin transparent">
          <div class="row">

             <!-- Tabel Peminjaman -->
              <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                   <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title m-0">Laporan Peminjaman</h4>
                    <form action="../laporan/export_pdf.php" method="GET" class="d-flex align-items-center">
                      <select name="bulan" class="form-control form-control-sm mr-2" required>
                        <option value="">-- Pilih Bulan --</option>
                        <?php
                          for ($i = 1; $i <= 12; $i++) {
                            $nama_bulan = date('F', mktime(0, 0, 0, $i, 10));
                            echo "<option value='$i'>$nama_bulan</option>";
                          }
                        ?>
                      </select>

                      <select name="tahun" class="form-control form-control-sm mr-2" required>
                        <option value="">-- Pilih Tahun --</option>
                        <?php
                          $tahun_sekarang = date('Y');
                          for ($t = $tahun_sekarang; $t >= ($tahun_sekarang - 5); $t--) {
                            echo "<option value='$t'>$t</option>";
                          }
                        ?>
                      </select>
                    <button type="submit" class="btn btn-success btn-sm px-4">
                    <i class="bx bx-download fs-5 me-2"> </i> Download
                    </button>
                    </form>
                    
                  </div>
                    <div class="table-responsive">
                     <table class="display expandable-table" style="width: 100%;" >
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>ID Peminjaman</th>
                          <th>Nama Peminjam</th>
                          <th>Kegiatan</th>
                          <th>Tanggal Peminjaman</th>
                          <th>Tanggal Pengembalian</th>
                          <th class="text-center">Status</th>
                          <th class="text-center">Aksi</th>
                        </tr>
                      </thead>
                     <tbody>
                      <?php
                      $no = 1;
                      $query = "
                        SELECT p.*, u.nama_lengkap, SUM(dp.jumlah_pinjam) AS jumlah, pg.catatan_pengembalian 
                        FROM peminjaman p
                        LEFT JOIN pengguna u ON p.id_pengguna = u.id_pengguna
                        LEFT JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
                        LEFT JOIN pengembalian pg ON p.id_peminjaman = pg.id_peminjaman
                        GROUP BY p.id_peminjaman
                        ORDER BY p.id_peminjaman ASC
                      ";

                      $tampil_data = mysqli_query($conn, $query);

                      if (!$tampil_data) {
                          echo "Query Error: " . mysqli_error($conn);
                      } elseif (mysqli_num_rows($tampil_data) > 0) {
                          while ($row = mysqli_fetch_assoc($tampil_data)) :
                      ?>
                      <tr>
                        <td><?= $no++; ?></td>
                         <td><?= htmlspecialchars($row['id_peminjaman']); ?></td>
                        <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                        <td><?= htmlspecialchars($row['kegiatan']); ?></td>
                        <td><?= $row['tanggal_pinjam']; ?></td>
                        <td><?= $row['tanggal_kembali']; ?></td>
                        <td class="text-center">
                          <span class="btn btn-<?= ($row['status'] == 'Disetujui') ? 'success' : (($row['status'] == 'Ditolak') ? 'danger' : 'primary') ?> py-2">
                            <?= $row['status']; ?>
                          </span>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-primary py-2" data-toggle="modal" data-target="#modalDetail<?= $row['id_peminjaman'] ?>">
                              Detail
                            </button>
                        </td>
                      </tr>
                      <!-- Modal Detail -->
                      <div class="modal fade" id="modalDetail<?= $row['id_peminjaman'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel<?= $row['id_peminjaman'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                          <div class="modal-content rounded">
                            <div class="modal-header">
                              <h5 class="modal-title" id="modalDetailLabel<?= $row['id_peminjaman'] ?>">Detail Peminjaman</h5>
                              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>

                            <div class="modal-body">
                              <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6 border-right">
                                  <div class="form-group">
                                    <label><strong>ID Peminjaman</strong></label>
                                    <input type="text" class="form-control" readonly value="<?= $row['id_peminjaman']; ?>">
                                  </div>
                                  <div class="form-group">
                                    <label><strong>Nama Peminjam</strong></label>
                                    <input type="text" class="form-control" readonly value="<?= $row['nama_lengkap']; ?>">
                                  </div>
                                  <div class="form-group">
                                    <label><strong>Kegiatan</strong></label>
                                    <textarea class="form-control" rows="1" readonly><?= $row['kegiatan']; ?></textarea>
                                  </div>
                                  <div class="form-group">
                                    <label><strong>Catatan Pengembalian</strong></label>
                                    <textarea class="form-control" readonly rows="1"><?= htmlspecialchars($row['catatan_pengembalian']); ?></textarea>
                                  </div>
                                  <div class="form-group">
                                    <label><strong>Status</strong></label><br>
                                    <span class="badge badge-<?= ($row['status'] == 'Disetujui') ? 'success' : (($row['status'] == 'Ditolak') ? 'danger' : (($row['status'] == 'Dikembalikan' || $row['status'] == 'Selesai') ? 'primary' : 'secondary')) ?>">
                                      <?= $row['status']; ?>
                                    </span>
                                  </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label><strong>Tanggal Peminjaman</strong></label>
                                    <input type="text" class="form-control" readonly value="<?= date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?>">
                                  </div>
                                  <div class="form-group">
                                    <label><strong>Tanggal Pengembalian</strong></label>
                                    <input type="text" class="form-control" readonly value="<?= date('d-m-Y', strtotime($row['tanggal_kembali'])); ?>">
                                  </div>
                                 
                                  <?php if (!empty($row['catatan_pengembalian'])): ?>
                                  <?php endif; ?>
                                 <div class="form-group">
                                    <label><strong>Peralatan yang Dipinjam</strong></label>
                                    <ul class="pl-3 mb-2">
                                      <?php
                                      $id_pinjam = $row['id_peminjaman'];
                                      $q_detail = mysqli_query($conn, "
                                        SELECT 
                                          pr.nama_peralatan, 
                                          COUNT(dp.id_unit) AS jumlah_pinjam,
                                          GROUP_CONCAT(up.kode_unit ORDER BY up.kode_unit SEPARATOR ', ') AS kode_unit
                                        FROM detail_peminjaman dp
                                        JOIN peralatan pr ON dp.id_peralatan = pr.id_peralatan
                                        JOIN unit_peralatan up ON dp.id_unit = up.id_unit
                                        WHERE dp.id_peminjaman = '$id_pinjam'
                                        GROUP BY dp.id_peralatan
                                      ");

                                      while ($alat = mysqli_fetch_assoc($q_detail)) {
                                        echo "<li>" . htmlspecialchars($alat['nama_peralatan']);
                                        echo "<br><small>Kode Unit: <em>" . htmlspecialchars($alat['kode_unit']) . "</em> - <strong>" . $alat['jumlah_pinjam'] . " unit</strong></small></li>";
                                      }
                                      ?>
                                    </ul>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      
                      <?php
                          endwhile;
                      }
                      ?>
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


