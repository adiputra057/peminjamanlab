<?php
include "../../config/config.php";

$query = mysqli_query($conn, "
    SELECT MONTH(tanggal_pinjam) AS bulan, COUNT(*) AS total 
    FROM peminjaman 
    WHERE YEAR(tanggal_pinjam) = 2025
    GROUP BY MONTH(tanggal_pinjam)
");

$bulanan = array_fill(1, 12, 0); // Inisialisasi 12 bulan = 0
while ($row = mysqli_fetch_assoc($query)) {
    $bulanan[(int)$row['bulan']] = (int)$row['total'];
}

echo json_encode([
    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
    'data' => array_values($bulanan)
]);
?>
