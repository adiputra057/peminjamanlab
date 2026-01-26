<?php
require_once __DIR__ . '/../admin/assets/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include "../config/config.php";

// Validasi ID
if (!isset($_GET['id'])) {
    die("ID tidak ditemukan.");
}

$id = $_GET['id'];

// Ambil data utama peminjaman + nama peminjam
$query = "
    SELECT p.*, u.nama_lengkap AS nama_peminjam
    FROM peminjaman p
    LEFT JOIN pengguna u ON p.id_pengguna = u.id_pengguna
    WHERE p.id_peminjaman = '$id'
";

$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Data tidak ditemukan.");
}

$row = mysqli_fetch_assoc($result);

// Ambil nama admin (karena di tabel tidak ada id_admin)
$qAdmin = mysqli_query($conn, "SELECT nama_lengkap FROM pengguna WHERE role='admin' LIMIT 1");
$admin = mysqli_fetch_assoc($qAdmin);
$nama_admin = $admin['nama_lengkap'];

// Ambil detail peralatan
$query_detail = mysqli_query($conn, "
    SELECT pr.nama_peralatan, SUM(dp.jumlah_pinjam) AS total_jumlah
    FROM detail_peminjaman dp
    JOIN peralatan pr ON dp.id_peralatan = pr.id_peralatan
    WHERE dp.id_peminjaman = '$id'
    GROUP BY pr.nama_peralatan
");

// Mulai HTML
$html = '
<style>
    body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
    h2 { text-align: center; margin: 0; font-size: 20px; }
    h4 { text-align: center; margin: 0 0 20px 0; font-size: 16px; }

    .info-table { width: 100%; margin-bottom: 10px; border-collapse: collapse; }
    .info-table td { padding: 5px; }

    .peralatan-table {
        width: 100%; border-collapse: collapse; margin-bottom: 20px;
    }

    .peralatan-table th, .peralatan-table td {
        border: 1px solid #000; padding: 6px; text-align: center;
    }

    .footer { text-align: center; font-size: 10px; margin-top: 30px; }
</style>

<h2>Laboratorium Seni dan Budaya</h2>
<h4>Bukti Peminjaman Peralatan</h4>

<table class="info-table">
    <tr>
        <td><strong>Nama Peminjam</strong></td>
        <td>: ' . htmlspecialchars($row['nama_peminjam']) . '</td>
    </tr>
    <tr>
        <td><strong>Kegiatan</strong></td>
        <td>: ' . htmlspecialchars($row['kegiatan']) . '</td>
    </tr>
    <tr>
        <td><strong>Tanggal Pinjam</strong></td>
        <td>: ' . htmlspecialchars($row['tanggal_pinjam']) . '</td>
    </tr>
    <tr>
        <td><strong>Tanggal Kembali</strong></td>
        <td>: ' . htmlspecialchars($row['tanggal_kembali']) . '</td>
    </tr>
</table>

<table class="peralatan-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Peralatan</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
while ($d = mysqli_fetch_assoc($query_detail)) {
    $html .= "
        <tr>
            <td>{$no}</td>
            <td>" . htmlspecialchars($d['nama_peralatan']) . "</td>
            <td>" . htmlspecialchars($d['total_jumlah']) . " Unit</td>
        </tr>
    ";
    $no++;
}

$html .= '
    </tbody>
</table>

<!-- Bagian TTD Menggunakan TABLE agar pasti muncul di DOMPDF -->
<table style="width:100%; margin-top:40px; text-align:center;">
    <tr>
        <td style="width:50%;">
            Peminjam<br><br><br><br><br><br>
            <strong>' . htmlspecialchars($row['nama_peminjam']) . '</strong><br>
            _______________________ 
        </td>

        <td style="width:50%;">
            Admin Lab<br><br><br><br><br><br>
            
            <strong>' . htmlspecialchars($nama_admin) . '</strong><br>
             _______________________ 
        </td>
    </tr>
</table>

<p class="footer">
    Dicetak secara otomatis oleh sistem. Tidak memerlukan tanda tangan basah. Harap simpan sebagai arsip.
</p>
';

// Render PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

// Fix output buffering
if (ob_get_length()) ob_end_clean();

$dompdf->stream("Bukti_Peminjaman_$id.pdf", ["Attachment" => true]);
exit;
?>