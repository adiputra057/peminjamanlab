<?php 
include "../../config/config.php";
include "../../config/kirim_wa.php"; // fungsi kirim WA


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ====== SETUJUI PEMINJAMAN ======
if (isset($_POST['setujui'])) {
    $id = $_POST['id_peminjaman'];
    
    // Mulai transaksi database
    mysqli_begin_transaction($conn);
    
    try {
        // Update status peminjaman ke "Disetujui"
        $query = mysqli_query($conn, "UPDATE peminjaman SET status = 'Disetujui' WHERE id_peminjaman = '$id'");
        
        if (!$query) {
            throw new Exception("Gagal update status peminjaman");
        }
        
        // ✅ Kurangi stok peralatan saat disetujui
        $detail = mysqli_query($conn, "SELECT id_peralatan, jumlah_pinjam FROM detail_peminjaman WHERE id_peminjaman = '$id'");
        
        while ($row = mysqli_fetch_assoc($detail)) {
            $id_peralatan = $row['id_peralatan'];
            $jumlah_pinjam = $row['jumlah_pinjam'];
            
            // Cek stok tersedia
            $cek_stok = mysqli_query($conn, "SELECT jumlah_baik FROM peralatan WHERE id_peralatan = $id_peralatan");
            $stok_data = mysqli_fetch_assoc($cek_stok);
            
            if ($stok_data['jumlah_baik'] < $jumlah_pinjam) {
                throw new Exception("Stok tidak mencukupi untuk peralatan ID: $id_peralatan");
            }
            
            // Kurangi stok
            $update_stok = mysqli_query($conn, "UPDATE peralatan SET jumlah_baik = jumlah_baik - $jumlah_pinjam WHERE id_peralatan = $id_peralatan");
            
            if (!$update_stok) {
                throw new Exception("Gagal mengurangi stok peralatan ID: $id_peralatan");
            }
        }
        
        // Commit transaksi
        mysqli_commit($conn);
        
        // Kirim notifikasi WA
        $data = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT p.nama_lengkap, p.no_hp 
            FROM peminjaman pj 
            JOIN pengguna p ON pj.id_pengguna = p.id_pengguna 
            WHERE pj.id_peminjaman = '$id'
        "));
        
        if ($data) {
            kirimWaStatusPengajuan($data['no_hp'], $data['nama_lengkap'], 'Disetujui');
        }
        
        $_SESSION['status'] = 'disetujui';
        
    } catch (Exception $e) {
        // Rollback jika ada error
        mysqli_rollback($conn);
        $_SESSION['status'] = 'gagal_setujui';
        error_log("Error setujui peminjaman: " . $e->getMessage());
    }
    
    header("Location: ../dashboard/index.php?page=peminjaman");
    exit;
}

// ====== TOLAK PEMINJAMAN ======
if (isset($_POST['tolak'])) {
    $id = $_POST['id_peminjaman'];

    mysqli_begin_transaction($conn);

    try {
        // Update status ke "Ditolak"
        $query = mysqli_query($conn, "UPDATE peminjaman SET status = 'Ditolak' WHERE id_peminjaman = '$id'");
        if (!$query) {
            throw new Exception("Gagal update status peminjaman");
        }

        // Kembalikan unit ke 'Tersedia'
        $detail = mysqli_query($conn, "SELECT id_peralatan, jumlah_pinjam FROM detail_peminjaman WHERE id_peminjaman = '$id'");

        while ($row = mysqli_fetch_assoc($detail)) {
            $id_peralatan = $row['id_peralatan'];
            $jumlah_pinjam = $row['jumlah_pinjam'];

            $update_unit = mysqli_query($conn, "
                UPDATE unit_peralatan 
                SET keterangan = 'Tersedia' 
                WHERE id_peralatan = $id_peralatan AND keterangan = 'Dipinjam' 
                LIMIT $jumlah_pinjam
            ");

            if (!$update_unit) {
                throw new Exception("Gagal mengembalikan unit ke tersedia");
            }
        }

        mysqli_commit($conn);

        // Kirim notifikasi WA
        $data = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT p.nama_lengkap, p.no_hp 
            FROM peminjaman pj 
            JOIN pengguna p ON pj.id_pengguna = p.id_pengguna 
            WHERE pj.id_peminjaman = '$id'
        "));

        if ($data) {
            $catatan = $_POST['catatan'] ?? 'Mohon maaf, pengajuan tidak dapat diproses.';
            kirimWaStatusPengajuan($data['no_hp'], $data['nama_lengkap'], 'Ditolak', $catatan);
        }

        $_SESSION['status'] = 'ditolak';

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['status'] = 'gagal_tolak';
        error_log("Error tolak peminjaman: " . $e->getMessage());
    }

    header("Location: ../dashboard/index.php?page=peminjaman");
    exit;
}


// ====== SELESAIKAN PEMINJAMAN ======
if (isset($_POST['selesai'])) {
    $id = $_POST['id_peminjaman'];
    
    // Mulai transaksi database
    mysqli_begin_transaction($conn);
    
    try {
        // Update status peminjaman ke "Selesai"
        $query = mysqli_query($conn, "UPDATE peminjaman SET status = 'Selesai' WHERE id_peminjaman = '$id'");
        
        if (!$query) {
            throw new Exception("Gagal update status peminjaman");
        }
        
        // ✅ Kembalikan stok peralatan saat selesai
        $detail = mysqli_query($conn, "SELECT id_peralatan, jumlah_pinjam FROM detail_peminjaman WHERE id_peminjaman = '$id'");
        
        while ($row = mysqli_fetch_assoc($detail)) {
            $id_peralatan = $row['id_peralatan'];
            $jumlah_pinjam = $row['jumlah_pinjam'];
            
            // Tambah kembali stok
            $update_stok = mysqli_query($conn, "UPDATE peralatan SET jumlah_baik = jumlah_baik + $jumlah_pinjam WHERE id_peralatan = $id_peralatan");
            
            if (!$update_stok) {
                throw new Exception("Gagal mengembalikan stok peralatan ID: $id_peralatan");
            }
        }
        
        // Commit transaksi
        mysqli_commit($conn);
        
        // Kirim notifikasi WA
        $data = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT p.nama_lengkap, p.no_hp 
            FROM peminjaman pj 
            JOIN pengguna p ON pj.id_pengguna = p.id_pengguna 
            WHERE pj.id_peminjaman = '$id'
        "));
        
        if ($data) {
            kirimWaStatusPengajuan($data['no_hp'], $data['nama_lengkap'], 'Selesai');
        }
        
        $_SESSION['status'] = 'selesai';
        
    } catch (Exception $e) {
        // Rollback jika ada error
        mysqli_rollback($conn);
        $_SESSION['status'] = 'gagal_selesai';
        error_log("Error selesai peminjaman: " . $e->getMessage());
    }
    
    header("Location: ../dashboard/index.php?page=peminjaman");
    exit;
}
?>