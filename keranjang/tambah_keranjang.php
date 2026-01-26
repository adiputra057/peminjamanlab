<?php
session_start();
include_once __DIR__ . '/../config/config.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'title' => 'Gagal',
        'text'  => 'ID peralatan tidak valid.'
    ];
    header("Location: ../index.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM peralatan WHERE id_peralatan = '$id'");
$peralatan = mysqli_fetch_assoc($query);

if (!$peralatan) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'title' => 'Gagal',
        'text'  => 'Peralatan tidak ditemukan.'
    ];
    header("Location: ../index.php");
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) AS jumlah FROM unit_peralatan WHERE id_peralatan = ? AND kondisi = 'Baik' AND keterangan = 'Tersedia'");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data_stok = $result->fetch_assoc();
$jumlah = $data_stok['jumlah'] ?? 0;

$item = [
    'id_peralatan' => $peralatan['id_peralatan'],
    'nama'         => $peralatan['nama_peralatan'],
    'jumlah'       => 1,
    'stok'         => $jumlah,
    'gambar'       => $peralatan['gambar'] ?? null
];

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

$keranjang = &$_SESSION['keranjang'];
$exists = false;

// Cek apakah peralatan ini sudah ada di keranjang
foreach ($keranjang as &$k) {
    if ($k['id_peralatan'] == $item['id_peralatan']) {
        $exists = true;
        if ($k['jumlah'] < $k['stok']) {
            $k['jumlah']++;
            $_SESSION['flash'] = [
                'type' => 'success',
                'title' => 'Diperbarui',
                'text'  => 'Jumlah peralatan diperbarui.'
            ];
        } else {
            $_SESSION['flash'] = [
                'type' => 'warning',
                'title' => 'Stok Maksimal',
                'text'  => 'Jumlah melebihi stok yang tersedia.'
            ];
        }
        break;
    }
}
unset($k);

if (!$exists) {
    $_SESSION['keranjang'][] = $item;
    $_SESSION['flash'] = [
        'type' => 'success',
        'title' => 'Berhasil',
        'text'  => 'Peralatan berhasil ditambahkan ke keranjang.'
    ];
}

header("Location: ../index.php");
exit;
