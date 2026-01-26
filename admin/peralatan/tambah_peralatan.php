<?php
include "../../config/config.php";

$id_peralatan = '';
$nama_peralatan = '';
$kategori = '';
$tahun_pengadaan = '';
$deskripsi = '';
$gambar = '';
$jumlah_baik = 0;
$jumlah_rusak = 0;

if (isset($_GET['id'])) {
    $id_peralatan = mysqli_real_escape_string($conn, $_GET['id']);
    $query = mysqli_query($conn, "
        SELECT *,
        (SELECT COUNT(*) FROM unit_peralatan WHERE id_peralatan = p.id_peralatan AND kondisi = 'Baik') AS jumlah_baik,
        (SELECT COUNT(*) FROM unit_peralatan WHERE id_peralatan = p.id_peralatan AND kondisi = 'Rusak') AS jumlah_rusak
        FROM peralatan p WHERE id_peralatan = '$id_peralatan'
    ");

    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $nama_peralatan = htmlspecialchars($data['nama_peralatan']);
        $kategori = $data['kategori'];
        $tahun_pengadaan = htmlspecialchars($data['tahun_pengadaan']);
        $deskripsi = $data['deskripsi'];
        $gambar = $data['gambar'];
        $jumlah_baik = $data['jumlah_baik'];
        $jumlah_rusak = $data['jumlah_rusak'];
    } else {
        header("Location: data_peralatan.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Peminjaman Ruangan | Dashboard</title>

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="../assets/vendors/feather/feather.css" />
    <link rel="stylesheet" href="../assets/vendors/ti-icons/css/themify-icons.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />
    <link rel="stylesheet" href="../assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css" />
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

        <div class="main-panel">
            <div class="content-wrapper">
                <div class="row mb-3">
                    <div class="col">
                        <h6 class="font-weight-normal mb-0">
                            Dashboard / <span class="text-gray"><?= $id_peralatan ? 'Edit' : 'Tambah' ?> peralatan</span>
                        </h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-title mb-3"><?= $id_peralatan ? 'Edit' : 'Tambah' ?> peralatan</p>
                              <form action="../peralatan/peralatan_aksi.php" method="POST" enctype="multipart/form-data" id="userForm">
                                    <input type="hidden" name="id_peralatan" value="<?= $id_peralatan ?>">

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>Nama Peralatan</label>
                                            <input type="text" name="nama_peralatan" class="form-control" value="<?= $nama_peralatan ?>" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Tahun Pengadaan</label>
                                            <input type="number" name="tahun_pengadaan" class="form-control" id="tahun_pengadaan" value="<?= $tahun_pengadaan ?>" min="2000" max="<?= date('Y') ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>Kategori</label>
                                            <select name="kategori" class="form-control" required>
                                                <option value="">-- Pilih Kategori --</option>
                                                <option value="Modern" <?= $kategori == 'Modern' ? 'selected' : '' ?>>Modern</option>
                                                <option value="Tradisional" <?= $kategori == 'Tradisional' ? 'selected' : '' ?>>Tradisional</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="gambar">Gambar</label>
                                            <input type="file" name="gambar" class="form-control" id="gambar" <?= $id_peralatan ? '' : 'required' ?>>
                                            <?php if ($id_peralatan && $gambar): ?>
                                                <br>
                                                <img src="../../uploads/peralatan/<?= htmlspecialchars($gambar) ?>" width="120">
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                     <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label>Deskripsi</label>
                                            <textarea name="deskripsi" class="form-control" required><?= htmlspecialchars($deskripsi) ?></textarea>
                                        </div>
                                    </div>

                                    <button type="submit" name="<?= $id_peralatan ? 'update' : 'simpan' ?>" class="btn btn-success">Simpan</button>
                                    <a href="../dashboard/index.php?page=peralatan" class="btn btn-secondary">Batal</a>
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



<!-- Scripts -->
<?php include(__DIR__ . '/../assets/includes/scripts.php');?>

</body>
</html>