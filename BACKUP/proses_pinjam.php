<?php
include "config/config.php";
require_once "config/function.php";
require_once "config/kirim_wa.php";
session_start();

if (!isset($_SESSION['id_pengguna'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Method tidak diizinkan.");
}

$id_pengguna = $_SESSION['id_pengguna'];
$kegiatan = $_POST['kegiatan'] ?? '';
$tanggal_peminjaman = $_POST['tanggal_peminjaman'] ?? '';
$tanggal_pengembalian = $_POST['tanggal_pengembalian'] ?? '';
$catatan = $_POST['catatan'] ?? '';
$peralatan_list = $_POST['peralatan'] ?? [];

$errors = [];

if (empty($peralatan_list)) $errors[] = "Minimal satu peralatan harus dipilih.";
if (empty($kegiatan)) $errors[] = "Kegiatan harus diisi.";
if (empty($tanggal_peminjaman)) $errors[] = "Tanggal peminjaman harus diisi.";
if (empty($tanggal_pengembalian)) $errors[] = "Tanggal pengembalian harus diisi.";

foreach ($peralatan_list as $i => $alat) {
    if (empty($alat['id'])) $errors[] = "Peralatan ke-" . ($i + 1) . " tidak memiliki ID.";
    if (empty($alat['jumlah']) || $alat['jumlah'] <= 0) $errors[] = "Jumlah peralatan ke-" . ($i + 1) . " harus lebih dari 0.";
}

if (strtotime($tanggal_peminjaman) >= strtotime($tanggal_pengembalian)) {
    $errors[] = "Tanggal pengembalian harus setelah tanggal peminjaman.";
}

if (!empty($errors)) {
    $listError = '<ul style="text-align: left;">' . implode('', array_map(fn($e) => "<li>$e</li>", $errors)) . '</ul>';
    echo "<!DOCTYPE html>
    <html>
    <head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head>
    <body>
    <script>
    Swal.fire({
        title: 'Error Validasi!',
        html: `$listError`,
        icon: 'warning',
        confirmButtonText: 'Kembali ke Form',
        confirmButtonColor: '#007bff'
    }).then(() => { history.back(); });
    </script>
    </body></html>";
    exit();
}

try {
    $conn->begin_transaction();

    $id_peminjaman = generateKodePeminjaman($conn);
    $status = 'Menunggu';
    $tanggal_pengajuan = date('Y-m-d H:i:s');
    $total_jumlah = array_sum(array_column($peralatan_list, 'jumlah'));

    // Simpan ke tabel peminjaman
    $stmt = $conn->prepare("INSERT INTO peminjaman 
        (id_peminjaman, id_pengguna, jumlah, tanggal_pinjam, tanggal_kembali, kegiatan, keterangan, status, tanggal_pengajuan)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siissssss", $id_peminjaman, $id_pengguna, $total_jumlah, $tanggal_peminjaman, $tanggal_pengembalian, $kegiatan, $catatan, $status, $tanggal_pengajuan);
    if (!$stmt->execute()) {
        throw new Exception("Gagal menyimpan data peminjaman: " . $stmt->error);
    }

    // Proses peralatan satu per satu
    foreach ($peralatan_list as $alat) {
        $id_alat = (int)$alat['id'];
        $jumlah = (int)$alat['jumlah'];

        // Cek stok berdasarkan unit_peralatan (baik & tersedia)
        $stmt_stok = $conn->prepare("SELECT COUNT(*) as stok FROM unit_peralatan WHERE id_peralatan = ? AND kondisi = 'Baik' AND keterangan = 'Tersedia'");
        $stmt_stok->bind_param("i", $id_alat);
        $stmt_stok->execute();
        $stok_result = $stmt_stok->get_result();
        $stok = $stok_result->fetch_assoc()['stok'] ?? 0;

        if ($stok < $jumlah) {
            throw new Exception("Stok unit tidak cukup untuk peralatan ID $id_alat. Tersedia: $stok unit.");
        }

        for ($i = 0; $i < $jumlah; $i++) {
            // Ambil satu unit acak dari stok tersedia
            $stmt_unit = $conn->prepare("SELECT id_unit FROM unit_peralatan WHERE id_peralatan = ? AND kondisi = 'Baik' AND keterangan = 'Tersedia' ORDER BY RAND() LIMIT 1");
            $stmt_unit->bind_param("i", $id_alat);
            $stmt_unit->execute();
            $result_unit = $stmt_unit->get_result();

            if ($unit = $result_unit->fetch_assoc()) {
                $id_unit = $unit['id_unit'];

                // Simpan ke detail_peminjaman
                $stmt_detail = $conn->prepare("INSERT INTO detail_peminjaman (id_peminjaman, id_peralatan, id_unit, jumlah_pinjam) VALUES (?, ?, ?, 1)");
                $stmt_detail->bind_param("sii", $id_peminjaman, $id_alat, $id_unit);
                if (!$stmt_detail->execute()) {
                    throw new Exception("Gagal menyimpan detail peminjaman: " . $stmt_detail->error);
                }

                // Ubah status unit jadi Dipinjam
                $stmt_update = $conn->prepare("UPDATE unit_peralatan SET keterangan = 'Dipinjam' WHERE id_unit = ?");
                $stmt_update->bind_param("i", $id_unit);
                $stmt_update->execute();
            } else {
                throw new Exception("Unit peralatan tidak ditemukan atau sudah dipinjam untuk ID $id_alat.");
            }
        }
    }

    $conn->commit();

    // Ambil nama pengguna
    $nama = '';
    $result_user = mysqli_query($conn, "SELECT nama_lengkap FROM pengguna WHERE id_pengguna = $id_pengguna");
    if ($result_user && $row = mysqli_fetch_assoc($result_user)) {
        $nama = $row['nama_lengkap'];
    }

    // Ambil nama peralatan pertama untuk notifikasi
    $nama_peralatan = '';
    $id_peralatan_pertama = $peralatan_list[0]['id'];
    $result_alat = mysqli_query($conn, "SELECT nama_peralatan FROM peralatan WHERE id_peralatan = $id_peralatan_pertama");
    if ($result_alat && $row = mysqli_fetch_assoc($result_alat)) {
        $nama_peralatan = $row['nama_peralatan'];
    }

    // Kirim notifikasi WA ke semua admin
    $query_admin = mysqli_query($conn, "SELECT no_hp FROM pengguna WHERE role = 'admin'");
    while ($admin = mysqli_fetch_assoc($query_admin)) {
        $nomor_admin = $admin['no_hp'];
        kirimWaNotifikasiAdminPengajuan($nomor_admin, $nama, $tanggal_peminjaman, $tanggal_pengembalian);
    }

    unset($_SESSION['keranjang']);

    echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body>
    <script>
    Swal.fire({
        title: 'Berhasil!',
        text: 'Peminjaman berhasil diajukan. Silakan tunggu konfirmasi admin.',
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#28a745'
    }).then(() => {
        window.location.href = '/peminjamanlab/index.php';
    });
    </script></body></html>";

} catch (Exception $e) {
    $conn->rollback();

    echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body>
    <script>
    Swal.fire({
        title: 'Error!',
        text: '" . addslashes($e->getMessage()) . "',
        icon: 'error',
        confirmButtonText: 'Kembali',
        confirmButtonColor: '#dc3545'
    }).then(() => { history.back(); });
    </script></body></html>";
}

$conn->close();
?>
