<?php
session_start();
$keranjang = $_SESSION['keranjang'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Website Peminjaman Alat</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }
    body {
      display: flex;
      flex-direction: column;
    }
    .wrapper {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    main.content {
      flex: 1;
    }
    .footer {
      background-color: #fff;
      padding: 1rem 0;
      text-align: center;
      color: #6c757d;
      border-top: 1px solid #dee2e6;
    }
    .item-card {
      background-color: #ffffff;
      border-radius: 12px;
      padding: 1rem 1.5rem;
      margin-bottom: 1rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .item-name {
      font-weight: 600;
      font-size: 1.1rem;
    }
    .item-info {
      color: #6c757d;
      font-size: 0.9rem;
      margin-bottom: 0.75rem;
    }
    .quantity-controls {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      flex-wrap: wrap;
    }
    .quantity-left {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .quantity-input {
      width: 80px;
      padding: 0.5rem;
      text-align: center;
      border: 1px solid #ced4da;
      border-radius: 6px;
    }
    .form-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 2rem;
    }
    .form-actions .btn {
      padding: 0.75rem 1.5rem;
      font-weight: 500;
    }
 @media (max-width: 576px) {
  .quantity-controls {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    text-align: left;
    width: 100%;
  }

  .quantity-left {
    flex-direction: row;
    align-items: center;
    gap: 0.5rem;
  }

  .item-card {
    text-align: left;
  }

  .quantity-input {
    width: 60px;
  }
}
  </style>
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
  <section id="peminjaman" class="peminjaman section">
    <!-- TAMBAHKAN DI SINI -->
    <div class="container section-title mt-5" data-aos="fade-up">
      <h2>Keranjang</h2>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
          <div class="card shadow border-0">
            <div class="card-body">
              <?php if (empty($keranjang)): ?>
              <div class="text-center">
                <i class="bi bi-cart-x" style="font-size: 3rem; color: #ccc;"></i>
                <h4 class="text-muted">Keranjang kosong</h4>
               <a href="<?= BASE_URL ?>index.php#peralatan" class="btn btn-outline-secondary mt-3">Pinjam Peralatan</a>
              </div>
              <?php else: ?>
              <form action="../peminjaman/form_peminjaman.php" method="POST">
                <?php foreach ($keranjang as $index => $item): ?>
                <div class="item-card">
                  <div class="item-name"><?= htmlspecialchars($item['nama']) ?></div>
                  <div class="item-info">Stok tersedia: <?= $item['stok'] ?> unit</div>
                  <div class="quantity-controls">
                    <div class="quantity-left">
                      <span>Jumlah:</span>
                      <input type="number" name="keranjang[<?= $index ?>][jumlah]" value="<?= $item['jumlah'] ?>" min="1" max="<?= $item['stok'] ?>" class="quantity-input">
                    </div>
                      <a href="#" class="btn btn-danger btn-hapus" data-index="<?= $index ?>"><i class="bi bi-trash me-1"></i></a>
                  </div>
                  <input type="hidden" name="keranjang[<?= $index ?>][id_peralatan]" value="<?= $item['id_peralatan'] ?>">
                  <input type="hidden" name="keranjang[<?= $index ?>][nama]" value="<?= htmlspecialchars($item['nama']) ?>">
                  <input type="hidden" name="keranjang[<?= $index ?>][stok]" value="<?= $item['stok'] ?>">
                </div>
                <?php endforeach; ?>
                <div class="form-actions">
                  <a href="<?= BASE_URL ?>index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
                  <button type="submit" class="btn btn-primary">  <i class="bi bi-check-circle me-2"></i>Pinjam</button>
                </div>
              </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<footer class="footer">
  <div class="container">
    <p>&copy; 2025 <strong class="text-primary">Adi Putra</strong>. All Rights Reserved.</p>
  </div>
</footer>
</div>
  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/aos/aos.js"></script>
  <script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="../assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- Main JS File -->
  <script src="../assets/js/main.js"></script>

<script>
document.querySelectorAll('.btn-hapus').forEach(button => {
  button.addEventListener('click', function (e) {
    e.preventDefault();
    const index = this.getAttribute('data-index');
    Swal.fire({
      title: 'Hapus Item?',
      text: "Anda yakin ingin menghapus item ini dari keranjang?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = `../keranjang/hapus_keranjang.php?index=${index}`;
      }
    });
  });
});
</script>
</body>
</html>

