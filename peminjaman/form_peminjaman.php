<?php
session_start();
include_once __DIR__ . '/../config/config.php';

// Ambil data user
$id_pengguna = $_SESSION['id_pengguna'];
$query_user = $conn->prepare("SELECT * FROM pengguna WHERE id_pengguna = ?");
$query_user->bind_param("i", $id_pengguna);
$query_user->execute();
$data_user = $query_user->get_result()->fetch_assoc();

// Data peralatan
$peralatan = [];

// PRIORITAS 1: Jika berasal dari keranjang (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['keranjang'])) {
    // Update session keranjang dengan data jumlah terbaru dari form
    $_SESSION['keranjang'] = $_POST['keranjang'];
    $peralatan = $_SESSION['keranjang'];
    
    // Debug: Tampilkan data yang diterima
    // var_dump($_POST['keranjang']); // Uncomment untuk debug
}
// PRIORITAS 2: Jika sudah ada data di session keranjang
elseif (!empty($_SESSION['keranjang'])) {
    $peralatan = $_SESSION['keranjang'];
}
// PRIORITAS 3: Jika pinjam langsung dari URL (GET) dan keranjang kosong
elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $query = $conn->prepare("
        SELECT p.id_peralatan, p.nama_peralatan,
            (SELECT COUNT(*) FROM unit_peralatan WHERE id_peralatan = p.id_peralatan AND kondisi = 'Baik' AND keterangan = 'Tersedia') as stok
        FROM peralatan p
        WHERE p.id_peralatan = ?
    ");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $peralatan[] = [
            'id_peralatan' => $data['id_peralatan'],
            'nama' => $data['nama_peralatan'],
            'stok' => $data['stok'],
            'jumlah' => 1
        ];
        // JANGAN update session keranjang untuk pinjam langsung
        // $_SESSION['keranjang'] = $peralatan; // ❌ HAPUS BARIS INI
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

<?php include '../layout/navbar.php'; ?>

  <main class="main">
     <!-- Peminjaman Section -->
    <section id="peminjaman" class="peminjaman section">

      <!-- Section Title -->
      <div class="container section-title mt-5" data-aos="fade-up">
        <h2>Peminjaman Peralatan</h2>
      </div><!-- End Section Title -->

    <!-- Form Section -->
    <div class="container">
    <div class="card shadow-lg border-0 rounded-4" data-aos="fade-up" data-aos-delay="400">
      <div class="card-body p-4">
      <form action="<?= BASE_URL ?>proses_pinjam.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_pengguna" value="<?= $id_pengguna ?>">

        <!-- Informasi Pengguna dan Kegiatan -->
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Nama Peminjam</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($data_user['nama_lengkap']) ?>" readonly>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Acara / Kegiatan</label>
                <input type="text" name="kegiatan" class="form-control" placeholder="Contoh: Pentas Seni" required>
            </div>
        </div>

        <!-- Tabel Peralatan -->
        <?php if (count($peralatan) > 0): ?>
        <div class="table-responsive mt-3">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>Nama Peralatan</th>
                        <th>Jumlah</th>
                        <th>Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($peralatan as $index => $item): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($item['nama'] ?? $item['nama_peralatan'] ?? '-') ?>
                            <input type="hidden" name="peralatan[<?= $index ?>][id]" value="<?= htmlspecialchars($item['id_peralatan'] ?? $item['id'] ?? '') ?>">
                            <input type="hidden" name="peralatan[<?= $index ?>][nama]" value="<?= htmlspecialchars($item['nama'] ?? $item['nama_peralatan'] ?? '') ?>">
                            <input type="hidden" name="peralatan[<?= $index ?>][stok]" value="<?= htmlspecialchars($item['stok'] ?? '') ?>">
                        </td>
                        <td>
                            <input type="number" name="peralatan[<?= $index ?>][jumlah]" class="form-control text-center" value="<?= $item['jumlah'] ?>" min="1" max="<?= $item['stok'] ?>" required>
                        </td>
                        <td><?= $item['stok'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-warning mt-3">Tidak ada peralatan yang dipilih.</div>
        <?php endif; ?>

        <!-- Tanggal & Catatan -->
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Tanggal Peminjaman</label>
                <input type="date" name="tanggal_peminjaman" class="form-control" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Tanggal Pengembalian</label>
                <input type="date" name="tanggal_pengembalian" class="form-control" required>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <label class="form-label fw-semibold">Catatan</label>
                <textarea name="catatan" class="form-control" rows="2" placeholder="Tulis catatan jika ada..."></textarea>
            </div>
        </div>

        <!-- Tombol -->
        <div class="d-flex flex-wrap gap-2 mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle pe-2"></i>Pinjam
            </button>
            <a href="<?= BASE_URL ?>index.php" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle pe-2"></i>Batal
            </a>
        </div>
    </form>

    </div>
  </div>
</div>
    </section>
    <!-- /Form Section -->
  
    
   <!-- Footer Section -->
    <hr class="my-3" />
    <div class="container copyright text-center mt-2">
       <p>&copy; 2025 <strong class="text-primary">Adi Putra</strong>. All Rights Reserved.</p>
    </div>
    <!-- /Footer Section -->

  </main>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/aos/aos.js"></script>
  <script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="../assets/vendor/purecounter/purecounter_vanilla.js"></script>
  
  <!-- Main JS File -->
  <script src="../assets/js/main.js"></script>
  <script>
  window.onload = function () {
    const params = new URLSearchParams(window.location.search);
    const target = params.get("page");
    if (target) {
      const el = document.getElementById(target);
      if (el) {
        el.scrollIntoView({ behavior: "smooth" });
      }
    }
  };
</script>

</body>
</html>