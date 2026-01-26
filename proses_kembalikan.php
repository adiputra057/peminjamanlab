<?php
session_start();
include "config/config.php";
include "config/function.php";
include "config/kirim_wa.php";

ob_start();

// Cek login
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: ../login.php");
    exit();
}

// Hanya POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: peminjaman_saya/status.php");
    exit();
}

$id_peminjaman = $_POST['id_peminjaman'] ?? '';
$tanggal_pengembalian = $_POST['tanggal_pengembalian'] ?? '';
$catatan_pengembalian = $_POST['catatan_pengembalian'] ?? '';

if (empty($id_peminjaman) || empty($tanggal_pengembalian)) {
    ob_end_clean();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
    <script>
        Swal.fire({
            icon:'error',
            title:'Error!',
            text:'Tanggal pengembalian wajib diisi!'
        }).then(() => {
            window.location.href = 'peminjaman_saya/status.php';
        });
    </script>
    </body>
    </html>
    <?php
    exit();
}

// Upload Foto
$foto_path = '';
if (isset($_FILES['foto_alat']) && $_FILES['foto_alat']['error'] === 0) {

    $upload_dir = "uploads/pengembalian/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $ext = strtolower(pathinfo($_FILES['foto_alat']['name'], PATHINFO_EXTENSION));
    $new_filename = "pengembalian_" . $id_peminjaman . "_" . time() . "." . $ext;

    // SIMPAN PATH ABSOLUTE DARI ROOT
    $foto_path = "/uploads/pengembalian/" . $new_filename;

    $allowed = ['jpg','jpeg','png','gif'];

    if (!in_array($ext, $allowed)) {
        ob_end_clean();
        ?>
        <!DOCTYPE html>
        <html><head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head><body>
        <script>
            Swal.fire({
                icon:'error',
                title:'Format tidak didukung',
                text:'Gunakan JPG/PNG/GIF'
            }).then(()=> window.location.href='peminjaman_saya/status.php');
        </script>
        </body></html>
        <?php
        exit();
    }

    move_uploaded_file($_FILES['foto_alat']['tmp_name'], "." . $foto_path);
}

try {
    // Insert pengembalian
    $stmt = $conn->prepare("INSERT INTO pengembalian 
        (id_peminjaman, tanggal_pengembalian, catatan_pengembalian, foto_alat, tanggal_dibuat)
        VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $id_peminjaman, $tanggal_pengembalian, $catatan_pengembalian, $foto_path);
    $stmt->execute();

    // Update status peminjaman
    $stmt2 = $conn->prepare("UPDATE peminjaman SET status='Dikembalikan' WHERE id_peminjaman=?");
    $stmt2->bind_param("s", $id_peminjaman);
    $stmt2->execute();

    // Update kondisi unit peralatan
    if (isset($_POST['kondisi_unit']) && is_array($_POST['kondisi_unit'])) {
        foreach ($_POST['kondisi_unit'] as $id_unit => $kondisi) {
            $stmt_unit = $conn->prepare("UPDATE unit_peralatan SET kondisi=? WHERE id_unit=?");
            $stmt_unit->bind_param("si", $kondisi, $id_unit);
            $stmt_unit->execute();
        }
    }

    $conn->commit();

    ob_end_clean();
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
            text: 'Pengembalian berhasil diajukan!',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = 'peminjaman_saya/status.php';
        });
    </script>
    </body>
    </html>
    <?php
    exit();

} catch (Exception $e) {

    $conn->rollback();
    ob_end_clean();
    ?>
    <!DOCTYPE html>
    <html>
    <head><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head>
    <body>
    <script>
        Swal.fire({
            icon:'error',
            title:'Gagal!',
            text:'<?= addslashes($e->getMessage()) ?>'
        }).then(() => {
            window.location.href='peminjaman_saya/status.php';
        });
    </script>
    </body>
    </html>
    <?php
    exit();
}
?>
