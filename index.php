<?php
include __DIR__ . "/config/config.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$data = null;
if (isset($_SESSION['id_pengguna'])) {
    $id = $_SESSION['id_pengguna'];
    $result = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_pengguna = '$id'");
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    }
}

$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 8;
$current_page = max(1, $current_page);
$offset = ($current_page - 1) * $items_per_page;

// HITUNG TOTAL DATA TANPA JOIN
$count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM peralatan");
$count_result = mysqli_fetch_assoc($count_query);
$total_items = $count_result['total'];
$total_pages = ceil($total_items / $items_per_page);
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
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
</head>

<body class="index-page">
<div class="wrapper"> 
<?php if (isset($_SESSION['status'])): ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if ($_SESSION['status'] == 'sukses_edit'): ?>

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Profil berhasil diperbarui.',
                showConfirmButton: false,
                timer: 2000
            });

        <?php elseif ($_SESSION['status'] == 'gagal_edit_db'): ?>

            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat memperbarui profil.',
                showConfirmButton: false,
                timer: 2000
            });

        <?php elseif ($_SESSION['status'] == 'password_tidak_cocok'): ?>

            Swal.fire({
                icon: 'warning',
                title: 'Password tidak cocok!',
                text: 'Silakan periksa kembali password Anda.',
                showConfirmButton: false,
                timer: 2000
            });

        <?php elseif ($_SESSION['status'] == 'password_terlalu_pendek'): ?>

            Swal.fire({
                icon: 'warning',
                title: 'Password terlalu pendek!',
                text: 'Minimal 6 karakter.',
                showConfirmButton: false,
                timer: 2000
            });

        <?php elseif ($_SESSION['status'] == 'email_invalid'): ?>

            Swal.fire({
                icon: 'error',
                title: 'Email Tidak Valid!',
                text: 'Harap gunakan email STIKOM (@stikom-bali.ac.id).',
                showConfirmButton: false,
                timer: 2000
            });

        <?php elseif ($_SESSION['status'] == 'email_terdaftar'): ?>

            Swal.fire({
                icon: 'error',
                title: 'Email Sudah Terdaftar!',
                text: 'Gunakan email lain.',
                showConfirmButton: false,
                timer: 2000
            });

        <?php endif; ?>
    });
  </script>
  <?php unset($_SESSION['status']); ?>
<?php endif; ?>


<?php include 'layout/navbar.php'; ?>

  <main class="content">

    <!-- Hero Section -->
    <section id="home" class="hero section">
      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
            <h1 data-aos="fade-up">Sistem Peminjaman Alat Lab Seni & Budaya</h1>
            <p data-aos="fade-up" data-aos-delay="100" style="margin-top: 7px;">Silahkan Baca Terlebih Dahulu Cara Peminjaman</p>
            <div class="d-flex flex-column flex-md-row" data-aos="fade-up" data-aos-delay="200">
              <a class="btn-button flex-md-shrink-0 rounded fw-bold" href="#peralatan">Pinjam Sekarang<i class="bi bi-arrow-right"> </i></a>
            </div>
          </div>
          <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out">
            <img src="assets/img/background bali.png" class="img-fluid animated" alt="">
          </div>
        </div>
      </div>
    </section>
    <!-- /Hero Section -->

    <!-- Alur Section -->
    <section id="carapeminjaman" class="alur section">
        <div class="container section-title" data-aos="fade-up">
          <h2>Cara Peminjaman</h2>
        </div>
      
        <div class="container">
         <div class="row gy-4">
          <!-- Pilih Peralatan -->
          <div class="col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="100">
            <div class="alur-item position-relative text-center">
              <div class="icon mb-3 d-flex align-items-center justify-content-center" style="height: 60px;">
                <i class="bi bi-bag-plus fs-1"></i>
              </div>
              <h3 class="h5 mb-3">Pilih Peralatan</h3>
              <p class="text-muted small">Pengguna melihat daftar peralatan yang tersedia. Pengguna dapat mencari peralatan yang dibutuhkan untuk kegiatan tertentu sebelum memutuskan untuk meminjam.</p>
            </div>
          </div>

          <!-- Peminjaman -->
          <div class="col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="200">
            <div class="alur-item position-relative text-center">
              <div class="icon mb-3 d-flex align-items-center justify-content-center" style="height: 60px;">
                <i class="bi bi-box-arrow-in-down fs-1"></i>
              </div>
              <h3 class="h5 mb-3">Peminjaman</h3>
              <p class="text-muted small">Pengguna memiliki dua opsi peminjaman. Pengguna dapat langsung meminjam satu peralatan atau menambahkan beberapa peralatan ke keranjang. Keduanya akan diarahkan ke form peminjaman.</p>
            </div>
          </div>

          <!-- Tunggu Konfirmasi -->
          <div class="col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="200">
            <div class="alur-item position-relative text-center">
              <div class="icon mb-3 d-flex align-items-center justify-content-center" style="height: 60px;">
                <i class="bi bi-hourglass-split fs-1"></i>
              </div>
              <h3 class="h5 mb-3">Tunggu Konfirmasi</h3>
              <p class="text-muted small">Setelah pengajuan dikirim, admin akan meninjau pengajuan dan memberikan konfirmasi disetujui atau ditolak.Hasil verifikasi dikirimkan ke pengguna melalui notifikasi WhatsApp.
              </p>
            </div>
          </div>

          <!-- Pengembalian -->
          <div class="col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="200">
            <div class="alur-item position-relative text-center">
              <div class="icon mb-3 d-flex align-items-center justify-content-center" style="height: 60px;">
                <i class="bi bi-arrow-repeat fs-1"></i>
              </div>
              <h3 class="h5 mb-3">Pengembalian</h3>
              <p class="text-muted small">Pengguna wajib mengembalikan peralatan tepat waktu sesuai jadwal serta mengisi form pengembalian sebagai bukti bahwa proses telah selesai.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /Alur Section -->

    <!-- Alat Section -->
    <section id="peralatan" class="alat section">
    <div class="container section-title" data-aos="fade-up">
      <h2>Peralatan Kesenian</h2>
    </div>

    <div class="container">

      <!-- Cards -->
   <div class="row">
  <?php
    // Query dengan LIMIT dan OFFSET untuk pagination
    $query = mysqli_query($conn, "
      SELECT * FROM peralatan 
      ORDER BY nama_peralatan ASC 
      LIMIT $items_per_page OFFSET $offset
    ");
    
    if (mysqli_num_rows($query) > 0) {
      while ($row = mysqli_fetch_assoc($query)) {
        $id = $row['id_peralatan'];

        // ✅ Ambil jumlah unit yang "Baik"
        $q_baik = mysqli_query($conn, "SELECT COUNT(*) AS jumlah_baik FROM unit_peralatan WHERE id_peralatan = '$id' AND kondisi = 'Baik' AND keterangan = 'Tersedia'");
        $data_baik = mysqli_fetch_assoc($q_baik);
        $jumlah_baik = $data_baik['jumlah_baik'];
  ?>
  <!-- Kartu Peralatan -->
  <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 stretch-card" data-aos="fade-up" data-aos-delay="400">
    <div class="card bg-light h-100">
      <img src="uploads/peralatan/<?= htmlspecialchars($row['gambar']) ?>" class="card-img-top" style="height: 250px; object-fit: cover;" alt="<?= htmlspecialchars($row['nama_peralatan']) ?>">
      <div class="card-body d-flex flex-column justify-content-between">
        <h5 class="card-title text-muted fw-bold mb-2"><?= htmlspecialchars($row['nama_peralatan']) ?></h5>
        
        <div class="d-flex align-items-center mb-2">
          <!-- Status Stok -->
          <?php if ($jumlah_baik > 0): ?>
            <span class="badge bg-success px-3 py-2">Tersedia</span>
          <?php else: ?>
            <span class="badge bg-danger px-3 py-2">Tidak Tersedia</span>
          <?php endif; ?>

          <!-- Ikon Keranjang -->
          <?php if ($jumlah_baik > 0): ?>
            <?php if (isset($_SESSION['id_pengguna'])): ?>
             <a href="<?= BASE_URL ?>keranjang/tambah_keranjang.php?id=<?= $row['id_peralatan'] ?>" class="text-dark" data-bs-toggle="tooltip" title="Tambah ke Keranjang">
               <i class="bi bi-cart-plus fs-4 ms-3"></i>
             </a>
            <?php else: ?>
              <a href="javascript:void(0);" class="text-dark" onclick="showLoginAlert()"><i class="bi bi-cart-plus fs-4 ms-3"></i></a>
            <?php endif; ?>
          <?php else: ?>
            <a href="javascript:void(0);" class="text-dark" title="Stok tidak tersedia"><i class="bi bi-cart-plus fs-4 ms-3"></i></a>
          <?php endif; ?>
        </div>

        <div class="d-flex gap-2 tombol-wrapper mt-auto">
          <?php if ($jumlah_baik > 0): ?>
            <?php if (isset($_SESSION['id_pengguna'])): ?>
             <a href="<?= BASE_URL ?>peminjaman/form_peminjaman.php?id=<?= $id ?>"  class="btn btn-primary flex-fill">Pinjam</a>
            <?php else: ?>
              <a href="javascript:void(0);" class="btn btn-primary flex-fill" onclick="showLoginAlert()">Pinjam</a>
            <?php endif; ?>
          <?php else: ?>
            <a href="javascript:void(0);" class="btn btn-primary flex-fill disabled" title="Stok tidak tersedia">Pinjam</a>
          <?php endif; ?>

          <!-- Tombol Detail -->
          <button type="button" class="btn btn-outline-secondary text-dark flex-fill" data-bs-toggle="modal" data-bs-target="#DetailPeralatan<?= $id ?>">
            Detail
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Detail -->
  <div class="modal fade" id="DetailPeralatan<?= $id ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content rounded-3">
        <div class="modal-header">
          <h5 class="modal-title">Detail Peralatan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 d-flex justify-content-center align-items-center">
              <img src="uploads/peralatan/<?= htmlspecialchars($row['gambar']) ?>" class="img-fluid rounded" style="max-height: 250px; width:300px; object-fit: contain;" alt="Gambar Peralatan" />
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label class="form-label"><strong>Nama Peralatan</strong></label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($row['nama_peralatan']) ?>" readonly>
              </div>
              <div class="form-group mb-3">
                <label class="form-label"><strong>Jumlah Tersedia</strong></label>
                <input type="number" class="form-control" value="<?= $jumlah_baik ?>" readonly>
              </div>
              <div class="form-group mb-3">
                <label class="form-label"><strong>Kategori Peralatan</strong></label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($row['kategori']) ?>" readonly>
              </div>
              <div class="form-group mb-3">
                <label class="form-label"><strong>Deskripsi</strong></label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($row['deskripsi']) ?>" readonly>
              </div>
              <div class="form-group mb-3">
                <label class="form-label"><strong>Tahun Pengadaan</strong></label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($row['tahun_pengadaan']) ?>" readonly>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php 
      } // end while
    } else {
  ?>
  <div class="col-12 text-center">
    <p class="text-muted">Tidak ada peralatan yang tersedia.</p>
  </div>
  <?php } ?>
</div>


      <!-- Pagination -->
      <?php if ($total_items > $items_per_page): ?>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2 flex-wrap">

          <!-- Info hanya muncul kalau lebih dari 1 halaman -->
          <div class="pagination-info mt-4 mb-md-0 text-muted">
            Menampilkan <?= ($offset + 1) ?> - <?= min($offset + $items_per_page, $total_items) ?> dari <?= $total_items ?> peralatan
          </div>

          <nav aria-label="Pagination Navigation">
            <ul class="pagination mb-0">
              <!-- Tombol Previous -->
              <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= max(1, $current_page - 1) ?>#peralatan"><i class="bi bi-chevron-left"></i></a>
              </li>

              <?php
              $range = 3;
              $start_page = max(1, $current_page - $range);
              $end_page = min($total_pages, $current_page + $range);

              if ($start_page > 1) {
                echo '<li class="page-item"><a class="page-link" href="?page=1#peralatan">1</a></li>';
                if ($start_page > 2) {
                  echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
              }

              for ($i = $start_page; $i <= $end_page; $i++): ?>
                <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>#peralatan"><?= $i ?></a>
                </li>
              <?php endfor;

              if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                  echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
                echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '#peralatan">' . $total_pages . '</a></li>';
              }
              ?>

              <!-- Tombol Next -->
              <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= min($total_pages, $current_page + 1) ?>#peralatan"><i class="bi bi-chevron-right"></i></a>
              </li>
            </ul>
          </nav>

        </div>
      <?php endif; ?>
    </div>
    </section>
    <!-- /Alat Section -->

    <!-- Kontak Section -->
    <section id="kontak" class="kontak section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Kontak</h2>
      </div><!-- End Section Title -->

      <div class="container">
      <div class="row gy-4 justify-content-center">
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
          <div class="team-member d-flex align-items-start">
            <div class="pic">
              <img src="assets/img/man.png" class="img-fluid" alt="">
            </div>
            <div class="member-info">
              <h4>Karsono</h4>
              <span>Laboran</span>
              <p>STAFF RUANGAN LAB SENI DAN BUDAYA</p>
              <div class="social">
                <a href="https://wa.me/6281558684788" target="_blank" title="Chat via WhatsApp">
                  <i class="bi bi-whatsapp"></i>
                </a>
                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=contoh@email.com" target="_blank" title="Kirim Email via Gmail">
                  <i class="bi bi-envelope"></i>
                </a>
              </div>
            </div>
          </div>
        </div><!-- End Team Member -->
      </div>
    </div>

    </section>
    <!-- /Kontak Section -->
     
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
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>
  
<script>
  // Fungsi untuk alert login
  function showLoginAlert() {
    Swal.fire({
      icon: 'info',
      title: 'Akses Ditolak',
      text: 'Silahkan Login dulu sebelum meminjam peralatan.',
      showConfirmButton: false,
      timer: 2000
    });
  }

  // Fungsi scroll ke anchor
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
<?php if (isset($_SESSION['flash'])): ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    Swal.fire({
      icon: '<?= $_SESSION['flash']['type']; ?>',
      title: '<?= $_SESSION['flash']['title']; ?>',
      text: '<?= $_SESSION['flash']['text']; ?>',
      showConfirmButton: false,
      timer: 2000
    });
  </script>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

</body>
</html>