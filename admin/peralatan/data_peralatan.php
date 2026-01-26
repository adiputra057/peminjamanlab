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
    <link rel="stylesheet" href="../../uploads/peralatan/" />
    
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
                <h6 class="font-weight-normal mb-0">Dashboard / <span class="text-muted">Data Peralatan</span></h6>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistik Box -->
      <div class="row">
        <div class="col-md-12 grid-margin transparent">
          <div class="row">
              
          <!-- Tabel Peralatan -->
            <div class="col-lg-12 grid-margin">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title m-0">Data Peralatan</h4>
                  <div class="d-flex justify-content-end align-items-center mb-3" style="gap: 10px;">
                  <form action="../peralatan/export_pdf.php" method="GET" class="mb-0">
                    <button type="submit" class="btn btn-success btn-md px-4">
                      <i class="bx bx-download me-1"></i> Download
                    </button>
                  </form>
                  <a href="index.php?page=tambah_peralatan" class="btn btn-primary">Tambah Peralatan</a>
                </div>
                  </div>
                  <div class="table-responsive">
                    <table class="display expandable-table text-center"style="width: 100%;">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nama Peralatan</th>
                          <th>Jumlah Total</th>
                          <th>Jumlah Tersedia</th>
                          <th>Kategori</th>
                          <th>Tahun Pengadaan</th>
                          <th>Gambar</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $no = 1;
                          $query = "SELECT * FROM peralatan ORDER BY id_peralatan ASC";
                          $tampil = mysqli_query($conn, $query);
                          while ($data = mysqli_fetch_assoc($tampil)):
                          ?>
                        <tr>
                          <td><?= $no++ ?></td>
                          <td><?= htmlspecialchars($data['nama_peralatan']) ?></td>
                          <td><?= htmlspecialchars($data['jumlah']) ?></td>
                          <td><?= htmlspecialchars($data['jumlah_baik']) ?></td>
                          <td><?= htmlspecialchars($data['kategori']) ?></td>
                          <td><?= htmlspecialchars($data['tahun_pengadaan']) ?></td>
                          <td><img src="../../uploads/peralatan/<?= htmlspecialchars($data['gambar']) ?>" width="100" alt="Gambar Peralatan"></td>
                          <td> 
                            <a href="index.php?page=edit_peralatan&id=<?= $data['id_peralatan'] ?>" class="btn btn-warning py-2">
                              Edit
                            </a>
                             <a href="#" class="btn btn-primary py-2" data-toggle="modal" data-target="#modalDetail<?= $data['id_peralatan'] ?>">
                              Detail
                            </a>
                            <a href="#" class="btn btn-danger py-2" data-toggle="modal" data-target="#modalHapus<?= $data['id_peralatan'] ?>">
                             Hapus
                            </a>
                          </td>
                        </tr>

                        <!-- Modal Detail Peralatan -->
                        <div class="modal fade" id="modalDetail<?= $data['id_peralatan'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel<?= $data['id_peralatan'] ?>" aria-hidden="true">
                          <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="modalDetailLabel<?= $data['id_peralatan'] ?>">Detail Peralatan</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                <form>
                                  <div class="row">
                                    <!-- Gambar -->
                                    <div class="col-md-4 text-center mb-3">
                                      <img src="../../uploads/peralatan/<?= htmlspecialchars($data['gambar']) ?>" alt="<?= htmlspecialchars($data['nama_peralatan']) ?>" class="img-fluid rounded" style="max-height: 400px;">
                                    </div>

                                    <!-- Informasi Detail -->
                                    <div class="col-md-8">
                                      <div class="row">
                                        <div class="col-md-6 mb-3">
                                          <label class="form-label">Nama Peralatan</label>
                                          <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama_peralatan']) ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                          <label class="form-label">Kategori</label>
                                          <input type="text" class="form-control" value="<?= htmlspecialchars($data['kategori']) ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                          <label class="form-label">Tahun Pengadaan</label>
                                          <input type="text" class="form-control" value="<?= htmlspecialchars($data['tahun_pengadaan']) ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                          <label class="form-label">Total Jumlah</label>
                                          <input type="text" class="form-control" value="<?= htmlspecialchars($data['jumlah']) ?> unit" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                          <label class="form-label">Jumlah Baik</label>
                                          <input type="text" class="form-control" value="<?= htmlspecialchars($data['jumlah_baik']) ?> unit" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                          <label class="form-label">Jumlah Rusak</label>
                                          <input type="text" class="form-control" value="<?= htmlspecialchars($data['jumlah_rusak']) ?> unit" readonly>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                          <label class="form-label">Deskripsi</label>
                                          <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($data['deskripsi']) ?></textarea>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </form>
                              </div>
                              <div class="modal-footer justify-content-end">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                              </div>
                            </div>
                          </div>
                        </div>



                        <!-- Modal Hapus -->
                        <div class="modal fade" id="modalHapus<?= $data['id_peralatan'] ?>" tabindex="-1" role="dialog">
                          <div class="modal-dialog">
                            <div class="modal-content">
                            <form action="../peralatan/peralatan_aksi.php" method="post">
                               <input type="hidden" name="id_peralatan" value="<?= $data['id_peralatan'] ?>">   
                                <div class="modal-header">
                                  <h5 class="modal-title" id="modalHapusLabel<?= $data['id_peralatan'] ?>">Konfirmasi Hapus Data</h5>
                                  <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <div class="modal-body">
                                  <input type="hidden" name="id_peralatan" value="<?= $data['id_peralatan'] ?>">
                                  <p class="text-center">Apakah Anda yakin ingin menghapus data <strong><?= htmlspecialchars($data['nama_peralatan']) ?></strong>?</p>
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
      sukses_hapus: "Data peralatan berhasil dihapus",
      berhasil_update: "Data berhasil diupdate",
    };

    // Status yang dianggap sukses
    let successStatuses = ['sukses_tambah', 'sukses_hapus', 'berhasil_update'];

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

