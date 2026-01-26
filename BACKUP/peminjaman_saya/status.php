<?php
session_start();
include "../config/config.php";

// Pagination settings
$items_per_page = 8; // Jumlah item per halaman
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, $current_page); // Minimal halaman 1
$offset = ($current_page - 1) * $items_per_page;

// Filter status
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
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
  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
  
  
  <!-- Main CSS File -->
  <link href="../assets/css/main.css" rel="stylesheet">
</head>

<body class="index-page">
 
<?php include '../layout/navbar.php'; ?>

<div class="wrapper">  
<main class="content">

  <!-- Status Section -->
<section id="status" class="status section">
  <div class="container mt-5">
    <div class="container section-title" data-aos="fade-up">
      <h2>Status Peminjaman</h2>
    </div>

    <?php
    if (session_status() === PHP_SESSION_NONE) session_start();
   

    
    // Query untuk menghitung total data setiap status
    $count_query = "
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Menunggu' THEN 1 ELSE 0 END) as menunggu,
            SUM(CASE WHEN status = 'Disetujui' THEN 1 ELSE 0 END) as disetujui,
            SUM(CASE WHEN status = 'Ditolak' THEN 1 ELSE 0 END) as ditolak,
            SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as selesai
        FROM peminjaman 
        WHERE id_pengguna = ?";
    
    $stmt_count = $conn->prepare($count_query);
    $stmt_count->bind_param("i", $id_pengguna);
    $stmt_count->execute();
    $count_result = $stmt_count->get_result()->fetch_assoc();
    
    // Query utama dengan filter dan pagination
    $where_clause = "WHERE p.id_pengguna = ?";
    $params = [$id_pengguna];
    $param_types = "i";
    
    if ($status_filter !== 'all') {
        $where_clause .= " AND p.status = ?";
        $params[] = $status_filter;
        $param_types .= "s";
    }
    
    // Query untuk data dengan pagination
  $query = "
    SELECT 
        p.*, 
        p.kegiatan, 
        u.username, 
        u.nama_lengkap,
        GROUP_CONCAT(DISTINCT pa.nama_peralatan SEPARATOR ', ') AS nama_peralatan_list,
        SUM(dp.jumlah_pinjam) AS total_jumlah,
        pg.tanggal_pengembalian
    FROM peminjaman p
    LEFT JOIN pengguna u ON p.id_pengguna = u.id_pengguna
    LEFT JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
    LEFT JOIN peralatan pa ON dp.id_peralatan = pa.id_peralatan
    LEFT JOIN pengembalian pg ON p.id_peminjaman = pg.id_peminjaman
    $where_clause
    GROUP BY p.id_peminjaman
    ORDER BY p.id_peminjaman DESC
    LIMIT ? OFFSET ?
";


    
    $params[] = $items_per_page;
    $params[] = $offset;
    $param_types .= "ii";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($param_types, ...$params);
    $stmt->execute();
    $tampil_data = $stmt->get_result();
    
    // Query untuk total data (untuk pagination)
    $count_filtered_query = "
        SELECT COUNT(*) as total
        FROM peminjaman p
        {$where_clause}";
    
    $stmt_total = $conn->prepare($count_filtered_query);
    $filtered_params = array_slice($params, 0, -2); // Remove LIMIT and OFFSET params
    $filtered_param_types = substr($param_types, 0, -2); // Remove 'ii'
    if (!empty($filtered_params)) {
        $stmt_total->bind_param($filtered_param_types, ...$filtered_params);
    }
    $stmt_total->execute();
    $total_filtered = $stmt_total->get_result()->fetch_assoc()['total'];
    
    $total_pages = ceil($total_filtered / $items_per_page);
    ?>

    <!-- Tombol Filter -->
    <div class="mb-4 d-flex justify-content-center" data-aos="fade-up" data-aos-delay="100">
        <div class="btn-group flex-wrap justify-content-center" role="group" id="filter-buttons">
        <a href="?page=1&status=all" class="btn btn-outline-secondary mb-2 <?= $status_filter === 'all' ? 'active' : '' ?>">
            Semua <span class="badge bg-secondary ms-1"><?= $count_result['total'] ?></span>
        </a>
        <a href="?page=1&status=Menunggu" class="btn btn-outline-warning mb-2 <?= $status_filter === 'Menunggu' ? 'active' : '' ?>">
            Menunggu <span class="badge bg-warning text-dark ms-1"><?= $count_result['menunggu'] ?></span>
        </a>
        <a href="?page=1&status=Disetujui" class="btn btn-outline-primary mb-2 <?= $status_filter === 'Disetujui' ? 'active' : '' ?>">
            Disetujui <span class="badge bg-primary ms-1"><?= $count_result['disetujui'] ?></span>
        </a>
        <a href="?page=1&status=Ditolak" class="btn btn-outline-danger mb-2 <?= $status_filter === 'Ditolak' ? 'active' : '' ?>">
            Ditolak <span class="badge bg-danger ms-1"><?= $count_result['ditolak'] ?></span>
        </a>
        <a href="?page=1&status=Selesai" class="btn btn-outline-success mb-2 <?= $status_filter === 'Selesai' ? 'active' : '' ?>">
            Selesai <span class="badge bg-success ms-1"><?= $count_result['selesai'] ?></span>
        </a>
      </div>
    </div>

    <!-- Card Container -->
    <div class="row card-container mt-3" data-aos="fade-up" data-aos-delay="100">
      <?php
      if (!$tampil_data) {
          echo "Query Error: " . $conn->error;
      } elseif ($tampil_data->num_rows > 0) {
          while ($row = $tampil_data->fetch_assoc()) :
              $nama_lengkap = $row['nama_lengkap'];
              $kegiatan = $row['kegiatan'];
              $tanggal_pinjam = date('d M Y', strtotime($row['tanggal_pinjam']));
              $tanggal_kembali = date('d M Y', strtotime($row['tanggal_kembali']));
              $tanggal_diajukan = date('d M Y', strtotime($row['tanggal_pengajuan']));
              $jumlah = $row['total_jumlah'] ?? 0;
              $status = $row['status'];
              $nama_peralatan = $row['nama_peralatan_list'] ?? 'Tidak ada peralatan';
              $keterangan = $row['keterangan'] ?? '-';

             $warnaList = [
    'Disetujui' => 'primary',
    'Ditolak' => 'danger',
    'Selesai' => 'success',
    'Menunggu' => 'warning'
];

$warna = $warnaList[$status] ?? 'secondary';

              
      ?>
      <div class="col-md-4 mb-4 card-item">
        <div class="card shadow-sm h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
          <span><?= htmlspecialchars($row['id_peminjaman']) ?></span>
            <span class="badge bg-<?= $warna ?> text-light fw-bold fs-6"><?= htmlspecialchars($status) ?></span>
          </div>
          <div class="card-body">
             <p class="card-text"><strong>Peminjam :</strong> <?= $nama_lengkap ?></p>
            <div class="row">
              <div class="col-6">
                <p class="card-text"><strong>Tanggal Pinjam</strong><br><?= $tanggal_pinjam ?></p>
              </div>
              <div class="col-6">
                <p class="card-text"><strong>Peralatan</strong><br><?= htmlspecialchars($nama_peralatan) ?></p>
              </div>
            </div>
           <div class="row mt-2">
              <div class="col-6">
                <p class="card-text">
                  <strong>Tanggal Kembali</strong><br>
                  <?= $tanggal_kembali ?>
                </p>
              </div>
              <div class="col-6">
                <p class="card-text"><strong>Kegiatan</strong><br><?= htmlspecialchars($kegiatan) ?></p>
              </div>
            </div>
           <p class="card-text text-muted mt-2">
              <small>
                <?php if ($status === 'Dikembalikan' || $status === 'Selesai'): ?>
                  <strong>Dikembalikan pada:</strong>
                  <?= date('d M Y', strtotime($row['tanggal_pengembalian'])) ?>
                <?php else: ?>
                  <strong>Diajukan pada:</strong>
                  <?= $tanggal_diajukan ?> 
                <?php endif; ?>
              </small>
            </p>
            <div class="row mt-2">
              <div class="col-6 mb-2">
                <button class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#ModalDetail<?= $row['id_peminjaman'] ?>">
                  <i class="fas fa-info-circle"></i> Detail
                </button>
              </div>
             <?php if ($status === 'Disetujui') : ?>
              <div class="col-6 mb-2">
                <button type="submit" class="btn btn-sm btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#ModalKembalikan<?= $row['id_peminjaman'] ?>">
                  <i class="fas fa-undo-alt"></i> Kembalikan 
                </button> 
              </div>
               <div class="col-12 mb-2">
                <button onclick="downloadPDF('<?= $row['id_peminjaman'] ?>')" class="btn btn-sm btn-outline-primary w-100">
                  <i class="bi bi-download"></i> Download PDF
                </button>
              </div>
              <?php endif; ?>
            </div>
            </div>
          </div>
        </div>
    
      <?php
      $id_peminjaman = $row['id_peminjaman'];
      $detail_peralatan = [];

      $query = "
          SELECT 
              dp.id_peralatan,
              pa.nama_peralatan,
              GROUP_CONCAT(up.kode_unit ORDER BY up.kode_unit SEPARATOR ', ') AS kode_units,
              COUNT(dp.id_unit) AS jumlah_pinjam
          FROM detail_peminjaman dp
          JOIN peralatan pa ON dp.id_peralatan = pa.id_peralatan
          JOIN unit_peralatan up ON dp.id_unit = up.id_unit
          WHERE dp.id_peminjaman = ?
          GROUP BY dp.id_peralatan
      ";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $id_peminjaman);
      $stmt->execute();
      $result = $stmt->get_result();
      while ($row_detail = $result->fetch_assoc()) {
          $detail_peralatan[] = $row_detail;
      }
      $stmt->close();
      ?>



<!-- Modal Detail -->
<div class="modal fade" id="ModalDetail<?= $row['id_peminjaman'] ?>" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-3">
      <div class="modal-header">
       <h5 class="modal-title" id="profilModalLabel"> Detail <?= $row['id_peminjaman'] ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
       <form>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label"><strong>Nama Peminjam</strong></label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($row['nama_lengkap']) ?>" readonly>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label"><strong>Kegiatan</strong></label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($row['kegiatan']) ?>" readonly>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label"><strong>Tanggal Pengajuan</strong></label>
              <input type="text" class="form-control" value="<?= $tanggal_diajukan ?>" readonly>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label"><strong>Tanggal Peminjaman</strong></label>
              <input type="text" class="form-control" value="<?= $tanggal_pinjam ?> - <?= $tanggal_kembali ?>" readonly>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label"><strong>Status</strong></label>
              <input type="text" class="form-control" value="<?= $status ?>" readonly>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label"><strong>Keterangan</strong></label>
              <textarea class="form-control" rows="1" readonly><?= htmlspecialchars($keterangan) ?></textarea>
            </div>
            <div class="col-12 mb-3">
            <label class="form-label"><strong>Peralatan yang Dipinjam</strong></label>
            <div class="table-responsive">
              <?php if (!empty($detail_peralatan)): ?>
                 <table class="table table-bordered table-striped table-hover align-middle shadow-sm rounded">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama Peralatan</th>
                        <th>Kode Unit</th>
                        <th>Jumlah Pinjam</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $no = 1; foreach ($detail_peralatan as $item): ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($item['nama_peralatan']) ?></td>
                        <td><?= htmlspecialchars($item['kode_units']) ?></td>
                        <td><?= $item['jumlah_pinjam'] ?> Unit</td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                <?php else: ?>
                <p class="text-muted">Tidak ada data peralatan untuk peminjaman ini.</p>
                <?php endif; ?>
            </div>
          </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Kembalikan -->
<div class="modal fade" id="ModalKembalikan<?= $row['id_peminjaman'] ?>" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-3">
      <div class="modal-header">
        <h5 class="modal-title">Form Pengembalian <?= $row['id_peminjaman'] ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
       <form action="<?= BASE_URL ?>proses_kembalikan.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="id_peminjaman" value="<?= $row['id_peminjaman'] ?>">

          <div class="row">
            <div class="col-md-6 mb-3">
              <label><strong>Nama Peminjam</strong></label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($row['nama_lengkap']) ?>" readonly>
            </div>
            <div class="col-md-6 mb-3">
              <label><strong>Kegiatan</strong></label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($row['kegiatan']) ?>" readonly>
            </div>
            <div class="col-md-6 mb-3">
              <label><strong>Tanggal Pengembalian</strong></label>
              <input type="date" name="tanggal_pengembalian" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="col-6 mb-3">
              <label class="form-label"><strong>Catatan</strong></label>
              <textarea name="catatan_pengembalian" class="form-control" rows="1" required></textarea>
            </div>

            <div class="col-md-6 mb-3">
              <label><strong>Foto Alat</strong></label>
              <input type="file" name="foto_alat" class="form-control" accept="image/*" required>
              <small class="text-muted">Opsional - Format: JPG, PNG</small>
            </div>
            
          </div>
            <div class="table-responsive mb-3">
            <label><strong>Detail Peralatan yang Dikembalikan</strong></label>
            <table class="table table-bordered table-striped table-hover align-middle shadow-sm rounded mt-2">
              <thead class="table-light">
                <tr>
                  <th>No</th>
                  <th>Nama Peralatan</th>
                  <th>Kode Unit</th>
                  <th>Jumlah Pinjam</th>
                  <th>Kondisi</th>
                </tr>
              </thead>
           <?php
            $id_peminjaman = $row['id_peminjaman'];
            $peralatan = [];

            $query = "
              SELECT dp.id_peralatan, pa.nama_peralatan, COUNT(dp.id_unit) AS jumlah_pinjam
              FROM detail_peminjaman dp
              JOIN peralatan pa ON dp.id_peralatan = pa.id_peralatan
              WHERE dp.id_peminjaman = ?
              GROUP BY dp.id_peralatan
            ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $id_peminjaman);
            $stmt->execute();
            $res = $stmt->get_result();

            while ($rowp = $res->fetch_assoc()) {
              $id_peralatan = $rowp['id_peralatan'];

              // Ambil semua unit
              $unit_query = mysqli_query($conn, "
                SELECT up.id_unit, up.kode_unit
                FROM detail_peminjaman dp
                JOIN unit_peralatan up ON dp.id_unit = up.id_unit
                WHERE dp.id_peminjaman = '$id_peminjaman' AND dp.id_peralatan = '$id_peralatan'
              ");

              $units = [];
              while ($u = mysqli_fetch_assoc($unit_query)) {
                $units[] = $u;
              }

              $peralatan[] = [
                'id_peralatan' => $id_peralatan,
                'nama_peralatan' => $rowp['nama_peralatan'],
                'jumlah_pinjam' => $rowp['jumlah_pinjam'],
                'units' => $units
              ];
            }
            ?>

            <tbody>
            <?php $no = 1; foreach ($peralatan as $alat): ?>
              <?php $rowspan = count($alat['units']); $first = true; ?>
              <?php foreach ($alat['units'] as $unit): ?>
                <tr>
                  <?php if ($first): ?>
                    <td rowspan="<?= $rowspan ?>"><?= $no++ ?></td>
                    <td rowspan="<?= $rowspan ?>"><?= htmlspecialchars($alat['nama_peralatan']) ?></td>
                  <?php endif; ?>

                  <td><?= htmlspecialchars($unit['kode_unit']) ?></td>

                  <?php if ($first): ?>
                    <td rowspan="<?= $rowspan ?>">
                      <?= $alat['jumlah_pinjam'] ?> Unit
                      <input type="hidden" name="jumlah_pengembalian[<?= $alat['id_peralatan'] ?>]" value="<?= $alat['jumlah_pinjam'] ?>">
                      <input type="hidden" name="id_peralatan[]" value="<?= $alat['id_peralatan'] ?>">
                    </td>
                  <?php endif; ?>

                  <td>
                    <input type="hidden" name="id_unit[]" value="<?= $unit['id_unit'] ?>">
                    <select name="kondisi_unit[<?= $unit['id_unit'] ?>]" class="form-select" required>
                      <option value="Baik">Baik</option>
                      <option value="Rusak">Rusak</option>
                    </select>
                  </td>
                </tr>
                <?php $first = false; ?>
              <?php endforeach; ?>
            <?php endforeach; ?>
            </tbody>
            </table>
          </div>

          <div class="text-start">
            <button type="submit" class="btn btn-success">
              <i class="fas fa-check"></i> Kembalikan
            </button>
             <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times"></i> Batal
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php endwhile; 
      } else { 
          // Empty state
          echo '<div class="col-12">
                    <div class="card shadow-sm text-center" style="height: 300px;">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <div>
                               <i class="bi bi-funnel text-muted" style="font-size: 2rem;"></i>
                                <h5 class="card-title text-muted mt-2">Tidak ada data</h5>
                                <p class="text-muted">Belum ada peminjaman dengan status ini</p>
                            </div>
                        </div>
                    </div>
                </div>';
      }
      ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
   <div class="pagination-wrapper d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-2" data-aos="fade-up" data-aos-delay="200">
        <div class="pagination-info ">
          Showing  <?= $offset + 1 ?> to <?= min($offset + $items_per_page, $total_filtered) ?> of <?= $total_filtered ?> entries
        </div>
        
        <nav aria-label="Navigasi halaman">
            <ul class="pagination">
                <!-- Previous Button -->
                <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $current_page > 1 ? '?page=' . ($current_page - 1) . '&status=' . $status_filter : '#' ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                
                <?php
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                // First page
                if ($start_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=1&status=<?= $status_filter ?>">1</a>
                    </li>
                    <?php if ($start_page > 2): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif;
                endif;
                
                // Page numbers
                for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&status=<?= $status_filter ?>"><?= $i ?></a>
                    </li>
                <?php endfor;
                
                // Last page
                if ($end_page < $total_pages): 
                    if ($end_page < $total_pages - 1): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $total_pages ?>&status=<?= $status_filter ?>"><?= $total_pages ?></a>
                    </li>
                <?php endif; ?>
                
                <!-- Next Button -->
                <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $current_page < $total_pages ? '?page=' . ($current_page + 1) . '&status=' . $status_filter : '#' ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>

  </div>
</section>
</main>
    
   <!-- Footer Section -->
    <hr class="my-3" />
    <div class="container copyright text-center mt-2">
         <p>&copy; 2025 <strong class="text-primary">Adi Putra</strong>. All Rights Reserved.</p>
    </div>
    <!-- /Footer Section -->

</div>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/aos/aos.js"></script>
  <script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="../assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Main JS File -->
  <script src="../assets/js/main.js"></script>
  <script src="../assets/js/filter-status.js"></script>
  <script src="../assets/js/scroll-handler.js"></script>

  <script>
function downloadPDF(id) {
  window.open('download_bukti.php?id=' + id, '_blank');
}
</script>

</body>
</html>