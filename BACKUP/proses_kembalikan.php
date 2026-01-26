<?php
session_start();
include "config/config.php";
include "config/function.php";
include "config/kirim_wa.php";

// Cek login
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: ../login.php");
    exit();
}

// Cek method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /status/status.php");
    exit();
}

// Ambil data utama
$id_peminjaman = $_POST['id_peminjaman'] ?? '';
$jumlah_pengembalian = $_POST['jumlah_pengembalian'] ?? '';
$tanggal_pengembalian = $_POST['tanggal_pengembalian'] ?? '';
$catatan_pengembalian = $_POST['catatan_pengembalian'] ?? '';

// Ambil data unit dan kondisi unit dari form
$id_unit_list = $_POST['id_unit'] ?? [];
$kondisi_unit_data = $_POST['kondisi_unit'] ?? [];

// Validasi
if (empty($id_peminjaman) || empty($tanggal_pengembalian)) {
    echo "<script>
        Swal.fire({icon:'error', title:'Error!', text:'Semua field wajib diisi!'}).then(() => {
            window.location.href = '/status/status.php';
        });
    </script>";
    exit();
}

// Upload foto
$foto_path = '';
if (isset($_FILES['foto_alat']) && $_FILES['foto_alat']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = "uploads/pengembalian/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    $file_extension = pathinfo($_FILES['foto_alat']['name'], PATHINFO_EXTENSION);
    $new_filename = "pengembalian_" . $id_peminjaman . "_" . time() . "." . $file_extension;
    $foto_path = $upload_dir . $new_filename;

    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array(strtolower($file_extension), $allowed_types)) {
        echo "<script>
            Swal.fire({icon:'error', title:'Format File Tidak Didukung!', text:'Gunakan JPG, PNG, atau GIF'}).then(() => {
                window.location.href = '/status/status.php';
            });
        </script>";
        exit();
    }

    if (!move_uploaded_file($_FILES['foto_alat']['tmp_name'], $foto_path)) {
        echo "<script>
            Swal.fire({icon:'error', title:'Upload Gagal!', text:'Gagal mengupload foto.'}).then(() => {
                window.location.href = '/status/status.php';
            });
        </script>";
        exit();
    }
}

try {
    $conn->autocommit(FALSE);

    // Simpan ke tabel pengembalian dengan status 'Menunggu Validasi'
    $stmt = $conn->prepare("INSERT INTO pengembalian 
    (id_peminjaman, tanggal_pengembalian, catatan_pengembalian, foto_alat, status_validasi, tanggal_dibuat)
    VALUES (?, ?, ?, ?, 'Menunggu Validasi', NOW())");
    $stmt->bind_param("ssss", $id_peminjaman, $tanggal_pengembalian, $catatan_pengembalian, $foto_path);

    if (!$stmt->execute()) throw new Exception("Gagal menyimpan pengembalian: " . $stmt->error);
    
    $id_pengembalian = $conn->insert_id;

    if (empty($id_unit_list)) throw new Exception("Tidak ada data unit dikirim.");

    // Simpan detail kondisi unit ke tabel detail_pengembalian
    foreach ($id_unit_list as $id_unit) {
        $id_unit = (int)$id_unit;
        $kondisi_unit = $kondisi_unit_data[$id_unit] ?? 'Baik';

        // Cek id_peralatan
        $stmt_unit = $conn->prepare("SELECT id_peralatan FROM unit_peralatan WHERE id_unit = ?");
        $stmt_unit->bind_param("i", $id_unit);
        $stmt_unit->execute();
        $result_unit = $stmt_unit->get_result();
        if (!$result_unit || $result_unit->num_rows === 0) {
            throw new Exception("Unit dengan ID $id_unit tidak ditemukan.");
        }
        $id_peralatan = $result_unit->fetch_assoc()['id_peralatan'];

        // Simpan ke detail_pengembalian
        $stmt_detail = $conn->prepare("INSERT INTO detail_pengembalian 
            (id_pengembalian, id_unit, id_peralatan, kondisi_kembali) VALUES (?, ?, ?, ?)");
        $stmt_detail->bind_param("iiis", $id_pengembalian, $id_unit, $id_peralatan, $kondisi_unit);
        
        if (!$stmt_detail->execute()) {
            throw new Exception("Gagal menyimpan detail pengembalian untuk unit ID $id_unit");
        }
    }

    // Update status peminjaman menjadi 'Dikembalikan' (belum Selesai)
    $stmt_update = $conn->prepare("UPDATE peminjaman SET status = 'Dikembalikan' WHERE id_peminjaman = ?");
    $stmt_update->bind_param("s", $id_peminjaman);
    if (!$stmt_update->execute()) throw new Exception("Gagal mengupdate status peminjaman.");

    $conn->commit();

    // Notifikasi WhatsApp untuk user
    $id_pengguna = $_SESSION['id_pengguna'];
    $res_user = mysqli_query($conn, "SELECT nama_lengkap, no_hp FROM pengguna WHERE id_pengguna = '$id_pengguna'");
    if ($res_user && mysqli_num_rows($res_user) > 0) {
        $user = mysqli_fetch_assoc($res_user);
        $nama = $user['nama_lengkap'];
        $no_hp = preg_replace('/^0/', '62', $user['no_hp']);
        kirimWaPengembalian($no_hp, $nama);
    }
    
    // SweetAlert untuk sukses
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Pengembalian berhasil diajukan! Menunggu validasi admin.',
                timer: 2000
            }).then((result) => {
               window.location.href = '<?= BASE_URL ?>status/status.php';
            });
        </script>
    </body>
    </html>
    <?php
    
} catch (Exception $e) {
    // Rollback jika ada error
    $conn->rollback();
    
    // SweetAlert untuk error
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                text: '<?php echo addslashes($e->getMessage()); ?>',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/status/status.php';
                }
            });
        </script>
    </body>
    </html>
    <?php
} finally {
    // Kembalikan autocommit
    $conn->autocommit(TRUE);
}
?>