<?php
include "../../config/config.php";

$query = mysqli_query($conn, "
  SELECT pl.nama_peralatan, SUM(dp.jumlah_pinjam) AS total_dipinjam
  FROM detail_peminjaman dp
  JOIN peralatan pl ON dp.id_peralatan = pl.id_peralatan
  GROUP BY dp.id_peralatan
  ORDER BY total_dipinjam DESC
  LIMIT 10
");

$labels = [];
$data = [];
$jumlah_detail = []; // Jika kamu ingin memisahkan jumlah sebagai array lain

while ($row = mysqli_fetch_assoc($query)) {
    $nama = $row['nama_peralatan'];
    $jumlah = (int)$row['total_dipinjam'];
    
    $labels[] = "$nama ($jumlah)";
    $data[] = $jumlah;
    $jumlah_detail[] = $jumlah;
}

echo json_encode([
    'labels' => $labels,
    'data' => $data,
    'jumlah' => $jumlah_detail // jika ingin akses khusus jumlah saja
]);
?>
