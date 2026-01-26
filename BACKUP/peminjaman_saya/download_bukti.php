<?php
require_once __DIR__ . '/../admin/assets/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include "../config/config.php";

// Validasi ID
if (!isset($_GET['id'])) {
    die("ID tidak ditemukan.");
}

$id = $_GET['id'];

// Ambil data utama peminjaman
$query = "
    SELECT p.*, u.nama_lengkap
    FROM peminjaman p
    LEFT JOIN pengguna u ON p.id_pengguna = u.id_pengguna
    LEFT JOIN pengembalian pg ON pg.id_peminjaman = p.id_peminjaman
    WHERE p.id_peminjaman = '$id'
";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Data tidak ditemukan.");
}
$row = mysqli_fetch_assoc($result);

// Ambil detail peralatan dengan jumlah total per alat
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
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        margin: 20px;
    }
        h2 {
        margin-top: 0;
        margin-bottom: 5px;
        font-size: 20px;
        text-align: center;
    }

    h4 {
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 16px;
        text-align: center;
    }
    .info-table {
        width: 100%;
        margin-bottom: 10px;
        border-collapse: collapse;
    }
    .info-table td {
        padding: 5px;
        vertical-align: top;
    }
    .info-table td:first-child {
        width: 150px; /* atur sesuai kebutuhan */
        white-space: nowrap;
    }
    .info-table td:last-child {
        width: auto;
    }
    .peralatan-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }
    .peralatan-table th, .peralatan-table td {
        border: 1px solid #000;
        padding: 6px;
        text-align: center;
    }
    .footer {
        margin-top: 20px;
        font-size: 10px;
        text-align: center;
    }
   .ttd {
        margin-top: 40px;
        width: 100%;
        font-size: 12px;
    }
    .ttd .left, .ttd .right {
        width: 45%;
        text-align: center;
        display: inline-block;
        vertical-align: top;
    }
    .ttd .left {
        float: left;
    }
    .ttd .right {
        float: right;
    }

</style>

<h2>Laboratorium Seni dan Budaya</h2>
<h4>Bukti Peminjaman Peralatan</h4>

<table class="info-table">
    <tr>
        <td style="width: 150px;"><<strong>Nama Peminjam</strong></td>
        <td>: ' . htmlspecialchars($row['nama_lengkap']) . '</td>
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
    $html .= "<tr>
                <td>$no</td>
                <td>" . htmlspecialchars($d['nama_peralatan']) . "</td>
                <td>" . htmlspecialchars($d['total_jumlah']) . " Unit</td>
              </tr>";
    $no++;
}

$html .= '</tbody>
</table>

<div class="ttd">
    <div class="left">
        Peminjam<br><br><br><br>
        _______________________
    </div>
    <div class="right">
        Admin Lab<br><br><br><br>
        _______________________
    </div>
</div>
<div style="clear: both;"></div>


<p class="footer">
    Dicetak secara otomatis oleh sistem. Tidak memerlukan tanda tangan basah. Harap simpan sebagai arsip.
</p>';

// Render PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
ob_end_clean();
$dompdf->stream("Bukti_Peminjaman_$id.pdf", array("Attachment" => true));

exit;
?>
