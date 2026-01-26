<?php
ob_start();
require_once __DIR__ . '/../assets/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include "../../config/config.php";

$bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : null;
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : null;
$nama_bulan = $bulan ? date('F', mktime(0, 0, 0, $bulan, 10)) : 'Semua Bulan';

$html = '
<style>
    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 12px;
    }
    table, th, td {
        border: 1px solid black;
        padding: 5px;
        text-align: center;
        vertical-align: middle;
    }
    h3 {
        text-align: center;
    }
</style>

<h3>Laporan Peminjaman ' . ($bulan && $tahun ? "$nama_bulan $tahun" : '') . '</h3>
<table>
<thead>
    <tr>
        <th>No</th>
        <th>ID Peminjaman</th>
        <th>Nama</th>
        <th>Kegiatan</th>
        <th>Nama Peralatan</th>
        <th>Kode Unit</th>
        <th>Kondisi</th>
        <th>Jumlah</th>
        <th>Tanggal Pinjam</th>
        <th>Tanggal Kembali</th>
        <th>Status</th>
    </tr>
</thead>
<tbody>';

$query = "
    SELECT p.*, u.nama_lengkap 
    FROM peminjaman p
    LEFT JOIN pengguna u ON p.id_pengguna = u.id_pengguna
";
if ($bulan && $tahun) {
    $query .= " WHERE MONTH(p.tanggal_pinjam) = $bulan AND YEAR(p.tanggal_pinjam) = $tahun";
}
$query .= " ORDER BY p.id_peminjaman ASC";

$result = mysqli_query($conn, $query);
$no = 1;

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id_peminjaman'];

    // Ambil daftar peralatan yang dipinjam
    $query_peralatan = mysqli_query($conn, "
        SELECT dp.id_peralatan, p.nama_peralatan
        FROM detail_peminjaman dp
        JOIN peralatan p ON dp.id_peralatan = p.id_peralatan
        WHERE dp.id_peminjaman = '$id'
        GROUP BY dp.id_peralatan
    ");

    $semua_unit = [];
    $jumlah_total_baris = 0;

    while ($alat = mysqli_fetch_assoc($query_peralatan)) {
        $id_peralatan = $alat['id_peralatan'];
        $nama_peralatan = $alat['nama_peralatan'];

        $query_unit = mysqli_query($conn, "
            SELECT u.kode_unit, u.kondisi
            FROM detail_peminjaman dp
            JOIN unit_peralatan u ON dp.id_unit = u.id_unit
            WHERE dp.id_peminjaman = '$id' AND dp.id_peralatan = '$id_peralatan'
        ");

        $units = [];
        while ($u = mysqli_fetch_assoc($query_unit)) {
            $units[] = [
                'kode_unit' => $u['kode_unit'],
                'kondisi' => $u['kondisi']
            ];
        }

        $semua_unit[] = [
            'nama_peralatan' => $nama_peralatan,
            'jumlah' => count($units),
            'unit_data' => $units
        ];

        $jumlah_total_baris += count($units);
    }

    $first_row = true;

    foreach ($semua_unit as $alat) {
        $jumlah_unit = count($alat['unit_data']);
        $first_sub = true;

        foreach ($alat['unit_data'] as $unit) {
            $html .= "<tr>";

            if ($first_row) {
                $html .= "<td rowspan='$jumlah_total_baris'>{$no}</td>
                          <td rowspan='$jumlah_total_baris'>{$row['id_peminjaman']}</td>
                          <td rowspan='$jumlah_total_baris'>{$row['nama_lengkap']}</td>
                          <td rowspan='$jumlah_total_baris'>{$row['kegiatan']}</td>";
            }

            if ($first_sub) {
                $html .= "<td rowspan='$jumlah_unit'>{$alat['nama_peralatan']}</td>";
            }

            $html .= "<td>{$unit['kode_unit']}</td>
                      <td>{$unit['kondisi']}</td>";

            if ($first_sub) {
                $html .= "<td rowspan='$jumlah_unit'>{$alat['jumlah']}</td>";
            }

            if ($first_row) {
                $html .= "<td rowspan='$jumlah_total_baris'>{$row['tanggal_pinjam']}</td>
                          <td rowspan='$jumlah_total_baris'>{$row['tanggal_kembali']}</td>
                          <td rowspan='$jumlah_total_baris'>{$row['status']}</td>";
                $first_row = false;
            }

            $html .= "</tr>";
            $first_sub = false;
        }
    }

    $no++;
}

$html .= '</tbody></table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

ob_end_clean();
$dompdf->stream("laporan_peminjaman.pdf", array("Attachment" => true));
exit;
?>
