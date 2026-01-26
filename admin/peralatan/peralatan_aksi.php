<?php
session_start();
include "../../config/config.php";

// Fungsi bantu
function cleanInput($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}

function isValidImage($file) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    return in_array($ext, $allowed);
}

function sinkronJumlahPeralatan($conn, $id_peralatan) {
    $q = mysqli_query($conn, "
        SELECT 
            SUM(CASE WHEN kondisi = 'Baik' THEN 1 ELSE 0 END) AS baik,
            SUM(CASE WHEN kondisi = 'Rusak' THEN 1 ELSE 0 END) AS rusak
        FROM unit_peralatan WHERE id_peralatan = $id_peralatan
    ");
    $res = mysqli_fetch_assoc($q);
    $baik = $res['baik'] ?? 0;
    $rusak = $res['rusak'] ?? 0;
    $total = $baik + $rusak;

    mysqli_query($conn, "
        UPDATE peralatan SET jumlah = $total, jumlah_baik = $baik, jumlah_rusak = $rusak
        WHERE id_peralatan = $id_peralatan
    ");
}

// ===================== TAMBAH =====================
if (isset($_POST['simpan'])) {
    $nama_peralatan   = cleanInput($conn, $_POST['nama_peralatan']);
    $kategori         = cleanInput($conn, $_POST['kategori']);
    $tahun_pengadaan  = cleanInput($conn, $_POST['tahun_pengadaan']);
    $deskripsi        = cleanInput($conn, $_POST['deskripsi']);

    if (empty($nama_peralatan) || empty($kategori) || empty($tahun_pengadaan)) {
        $_SESSION['status'] = 'input_tidak_lengkap';
        header("Location: ../dashboard/index.php?page=tambah_peralatan");
        exit;
    }

    $nama_file = 'default.png';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        if (!isValidImage($_FILES['gambar'])) {
            $_SESSION['status'] = 'format_gambar_tidak_valid';
            header("Location: ../dashboard/index.php?page=tambah_peralatan");
            exit;
        }

        $upload_dir = __DIR__ . "/../../uploads/peralatan/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $nama_file = uniqid('img_') . "." . $ext;
        $upload_path = $upload_dir . $nama_file;

        if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
            $_SESSION['status'] = 'upload_gagal';
            header("Location: ../dashboard/index.php?page=tambah_peralatan");
            exit;
        }
    }

    $query = "INSERT INTO peralatan (nama_peralatan, jumlah, jumlah_baik, jumlah_rusak, kategori, tahun_pengadaan, deskripsi, gambar)
              VALUES ('$nama_peralatan', 0, 0, 0, '$kategori', '$tahun_pengadaan', '$deskripsi', '$nama_file')";
    $simpan = mysqli_query($conn, $query);

    $_SESSION['status'] = $simpan ? 'sukses_tambah' : 'gagal_simpan_db';
    header("Location: ../dashboard/index.php?page=peralatan");
    exit;
}

// ===================== UPDATE =====================
if (isset($_POST['update'])) {
    $id_peralatan     = intval($_POST['id_peralatan']);
    $nama_peralatan   = cleanInput($conn, $_POST['nama_peralatan']);
    $kategori         = cleanInput($conn, $_POST['kategori']);
    $tahun_pengadaan  = cleanInput($conn, $_POST['tahun_pengadaan']);
    $deskripsi        = cleanInput($conn, $_POST['deskripsi']);

    if (empty($nama_peralatan) || empty($kategori) || empty($tahun_pengadaan)) {
        $_SESSION['status'] = 'input_tidak_lengkap';
        header("Location: ../dashboard/index.php?page=peralatan");
        exit;
    }

    $upload_dir = __DIR__ . "/../../uploads/peralatan/";
    $res = mysqli_query($conn, "SELECT gambar FROM peralatan WHERE id_peralatan = $id_peralatan");
    $oldData = mysqli_fetch_assoc($res);
    $gambar_lama = $oldData['gambar'] ?? '';

    if (!empty($_FILES['gambar']['name'])) {
        if (!isValidImage($_FILES['gambar'])) {
            $_SESSION['status'] = 'format_gambar_tidak_valid';
            header("Location: ../dashboard/index.php?page=peralatan");
            exit;
        }

        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $gambar_baru = uniqid('img_') . "." . $ext;
        $upload_path = $upload_dir . $gambar_baru;

        if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
            $_SESSION['status'] = 'upload_gagal';
            header("Location: ../dashboard/index.php?page=peralatan");
            exit;
        }

        if (!empty($gambar_lama) && file_exists($upload_dir . $gambar_lama)) {
            unlink($upload_dir . $gambar_lama);
        }
    } else {
        $gambar_baru = $gambar_lama;
    }

    $query = "UPDATE peralatan SET 
                nama_peralatan='$nama_peralatan',
                kategori='$kategori',
                tahun_pengadaan='$tahun_pengadaan',
                deskripsi='$deskripsi',
                gambar='$gambar_baru'
              WHERE id_peralatan=$id_peralatan";

    $result = mysqli_query($conn, $query);

    // Sinkron otomatis setelah update
    sinkronJumlahPeralatan($conn, $id_peralatan);

    $_SESSION['status'] = $result ? 'berhasil_update' : 'gagal_update';
    header("Location: ../dashboard/index.php?page=peralatan");
    exit;
}

// ===================== HAPUS =====================
if (isset($_POST['hapus'])) {
    $id = intval($_POST['id_peralatan']);
    $result = mysqli_query($conn, "SELECT gambar FROM peralatan WHERE id_peralatan = $id");
    $data = mysqli_fetch_assoc($result);
    if ($data && !empty($data['gambar'])) {
        $filepath = __DIR__ . "/../../uploads/peralatan/" . $data['gambar'];
        if (file_exists($filepath)) unlink($filepath);
    }

    mysqli_query($conn, "DELETE FROM unit_peralatan WHERE id_peralatan = $id");
    $hapus = mysqli_query($conn, "DELETE FROM peralatan WHERE id_peralatan = $id");

    $_SESSION['status'] = $hapus ? 'sukses_hapus' : 'gagal_hapus_db';
    header("Location: ../dashboard/index.php?page=peralatan");
    exit;
}
?>
