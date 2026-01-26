<?php
include "../../config/config.php";

$id_unit = '';
$id_peralatan = '';
$kode_unit = '';
$kondisi = '';
$keterangan = '';

// Jika mode edit
if (isset($_GET['id'])) {
    $id_unit = mysqli_real_escape_string($conn, $_GET['id']);
    $query = mysqli_query($conn, "SELECT * FROM unit_peralatan WHERE id_unit = '$id_unit'");

    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $id_peralatan = $data['id_peralatan'];
        $kode_unit = $data['kode_unit'];
        $kondisi = $data['kondisi'];
        $keterangan = $data['keterangan'];
    } else {
        header("Location: index.php?page=daftar_peralatan");
        exit();
    }
}

// Ambil daftar peralatan
$daftar_peralatan = mysqli_query($conn, "SELECT id_peralatan, nama_peralatan FROM peralatan ORDER BY nama_peralatan ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Unit Peralatan | Dashboard</title>

  <!-- CSS -->
  <link rel="stylesheet" href="../assets/vendors/feather/feather.css" />
  <link rel="stylesheet" href="../assets/vendors/ti-icons/css/themify-icons.css" />
  <link rel="stylesheet" href="../assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
</head>
<body>

<div class="container-scroller">
  <?php include __DIR__ . '/../layout/navbar.php'; ?>
  <div class="container-fluid page-body-wrapper">
    <?php include __DIR__ . '/../layout/sidebar.php'; ?>

    <div class="main-panel">
      <div class="content-wrapper">
        <div class="row mb-3">
          <div class="col">
            <h6 class="font-weight-normal mb-0">
              Dashboard / <span class="text-gray"><?= $id_unit ? 'Edit' : 'Tambah' ?> Unit Peralatan</span>
            </h6>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <p class="card-title mb-3"><?= $id_unit ? 'Edit' : 'Tambah' ?> Unit Peralatan</p>

                <form action="../daftar_peralatan/unit_aksi.php" method="POST" id="unitForm">
                  <input type="hidden" name="id_unit" value="<?= htmlspecialchars($id_unit) ?>">

                  <div class="form-row">
                    <div class="form-group col-md-6">
                    <label>Nama Peralatan</label>
                    <select name="id_peralatan" id="id_peralatan" class="form-control" required>
                      <option value="">-- Pilih Peralatan --</option>
                      <?php while ($row = mysqli_fetch_assoc($daftar_peralatan)): ?>
                        <option value="<?= $row['id_peralatan'] ?>" <?= $row['id_peralatan'] == $id_peralatan ? 'selected' : '' ?>>
                          <?= htmlspecialchars($row['nama_peralatan']) ?>
                        </option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                   <div class="form-group col-md-6">
                    <label>Kode Unit</label>
                  <input type="text" name="kode_unit" id="kode_unit" class="form-control" value="<?= htmlspecialchars($kode_unit) ?>" readonly required>
                  </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>Kondisi</label>
                      <select name="kondisi" class="form-control" required>
                        <option value="">-- Pilih Kondisi --</option>
                        <option value="Baik" <?= $kondisi == 'Baik' ? 'selected' : '' ?>>Baik</option>
                        <option value="Rusak" <?= $kondisi == 'Rusak' ? 'selected' : '' ?>>Rusak</option>
                      </select>
                    </div>

                    <div class="form-group col-md-6">
                      <label>Status</label>
                      <select name="keterangan" class="form-control" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="Tersedia" <?= $keterangan == 'Tersedia' ? 'selected' : '' ?>>Tersedia</option>
                        <option value="Dipinjam" <?= $keterangan == 'Dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                        <option value="Perlu Perbaikan" <?= $keterangan == 'Perlu Perbaikan' ? 'selected' : '' ?>>Perlu Perbaikan</option>
                      </select>
                    </div>
                  </div>

                  <button type="submit" name="<?= $id_unit ? 'update_unit' : 'tambah_unit' ?>" class="btn btn-success">Simpan</button>
                  <a href="index.php?page=daftar_peralatan" class="btn btn-secondary">Batal</a>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>

      <footer class="footer">
        <div class="d-sm-flex justify-content-center justify-content-sm-between">
          <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
            Copyright &copy; 2025. <a href="#">Adi Putra</a> Lab Seni & Budaya
          </span>
        </div>
      </footer>
    </div>
  </div>
</div>

<!-- JS -->
<?php include(__DIR__ . '/../assets/includes/scripts.php'); ?>

<script>
document.getElementById('id_peralatan').addEventListener('change', function () {
  var idPeralatan = this.value;

  if (idPeralatan !== '') {
    fetch('../daftar_peralatan/get_kode_unit.php?id_peralatan=' + idPeralatan)
      .then(response => response.text())
      .then(kode => {
        document.getElementById('kode_unit').value = kode;
      });
  } else {
    document.getElementById('kode_unit').value = '';
  }
});
</script>


</body>
</html>
