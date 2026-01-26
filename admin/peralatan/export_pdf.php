<?php
ob_start();
require_once __DIR__ . '/../assets/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include "../../config/config.php";

// Ambil semua data dari tabel peralatan dan unit_peralatan
$query = "
    SELECT 
        p.id_peralatan, 
        p.nama_peralatan, 
        p.kategori, 
        p.tahun_pengadaan, 
        p.deskripsi, 
        p.gambar,
        COUNT(u.id_unit) AS total_unit,
        SUM(CASE WHEN u.kondisi = 'Baik' THEN 1 ELSE 0 END) AS jumlah_baik,
        SUM(CASE WHEN u.kondisi = 'Rusak' THEN 1 ELSE 0 END) AS jumlah_rusak
    FROM peralatan p
    LEFT JOIN unit_peralatan u ON u.id_peralatan = p.id_peralatan
    GROUP BY 
        p.id_peralatan, 
        p.nama_peralatan, 
        p.kategori, 
        p.tahun_pengadaan, 
        p.deskripsi, 
        p.gambar
    ORDER BY p.nama_peralatan ASC
";

$result = mysqli_query($conn, $query);

// Buat konten HTML
ob_start();
?>

<style>
    body { font-family: Arial, sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table, th, td { border: 1px solid black; }
    th, td { padding: 6px; text-align: center; }
    h2 { text-align: center; }
</style>

<h2>Daftar Inventaris Peralatan Laboratorium Seni & Budaya</h2>
<p><strong>Periode:</strong> Semua Data</p>

<!-- Tambah kolom <th> Deskripsi -->
<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Nama Peralatan</th>
      <th>Kategori</th>
      <th>Tahun Pengadaan</th>
      <th>Deskripsi</th>
      <th>Kode Unit</th>
      <th>Kondisi</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $no = 1;

    // Query ambil data + deskripsi
    $query = "
      SELECT 
        p.id_peralatan, 
        p.nama_peralatan, 
        p.kategori, 
        p.tahun_pengadaan,
        p.deskripsi,
        u.kode_unit, 
        u.kondisi
      FROM peralatan p
      LEFT JOIN unit_peralatan u ON u.id_peralatan = p.id_peralatan
      ORDER BY p.nama_peralatan ASC, u.kode_unit ASC
    ";
    $result = mysqli_query($conn, $query);

    // Simpan ke array
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[$row['id_peralatan']]['info'] = [
            'nama_peralatan' => $row['nama_peralatan'],
            'kategori' => $row['kategori'],
            'tahun_pengadaan' => $row['tahun_pengadaan'],
            'deskripsi' => $row['deskripsi']
        ];
        $data[$row['id_peralatan']]['units'][] = [
            'kode_unit' => $row['kode_unit'],
            'kondisi' => $row['kondisi']
        ];
    }

    // Tampilkan
   foreach ($data as $id => $peralatan) {
    $units = isset($peralatan['units']) ? $peralatan['units'] : [['kode_unit' => '-', 'kondisi' => '-']];
    $rowspan = count($units);
    $first = true;

    foreach ($units as $unit) {
        echo "<tr>";
        if ($first) {
            echo "<td rowspan='$rowspan'>" . $no++ . "</td>";
            echo "<td rowspan='$rowspan'>" . htmlspecialchars($peralatan['info']['nama_peralatan']) . "</td>";
            echo "<td rowspan='$rowspan'>" . htmlspecialchars($peralatan['info']['kategori']) . "</td>";
            echo "<td rowspan='$rowspan'>" . htmlspecialchars($peralatan['info']['tahun_pengadaan']) . "</td>";
            echo "<td rowspan='$rowspan'>" . nl2br(htmlspecialchars($peralatan['info']['deskripsi'])) . "</td>";
            $first = false;
        }
        echo "<td>" . htmlspecialchars($unit['kode_unit']) . "</td>";
        echo "<td>" . htmlspecialchars($unit['kondisi']) . "</td>";
        echo "</tr>";
    }
}

    ?>
  </tbody>
</table>



<?php
$html = ob_get_clean();

// Konversi ke PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("Laporan_Peralatan.pdf", array("Attachment" => false));
exit;
