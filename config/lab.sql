-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Des 2025 pada 18.00
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lab`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_peminjaman`
--

CREATE TABLE `detail_peminjaman` (
  `id_detail` int(11) NOT NULL,
  `id_peminjaman` varchar(10) DEFAULT NULL,
  `id_peralatan` int(11) NOT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `jumlah_pinjam` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_peminjaman`
--

INSERT INTO `detail_peminjaman` (`id_detail`, `id_peminjaman`, `id_peralatan`, `id_unit`, `jumlah_pinjam`) VALUES
(234, 'LOAN-6451', 22, 11, 1),
(237, 'LOAN-3177', 22, 11, 1),
(239, 'LOAN-6796', 17, 27, 1),
(240, 'LOAN-6796', 17, 28, 1),
(241, 'LOAN-6796', 17, 16, 1),
(249, 'LOAN-3418', 30, 33, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` varchar(10) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `tanggal_pengajuan` datetime DEFAULT current_timestamp(),
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `kegiatan` varchar(30) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('Menunggu','Disetujui','Ditolak','Selesai') DEFAULT 'Menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_pengguna`, `jumlah`, `tanggal_pengajuan`, `tanggal_pinjam`, `tanggal_kembali`, `kegiatan`, `keterangan`, `status`) VALUES
('LOAN-3177', 72, 1, '2025-12-05 17:37:29', '2025-12-06', '2025-12-14', '-', '-', 'Selesai'),
('LOAN-3418', 72, 1, '2025-12-05 17:48:57', '2025-12-06', '2025-12-15', '-', '-', 'Disetujui'),
('LOAN-6451', 72, 1, '2025-12-05 17:14:25', '2025-12-06', '2025-12-07', '=', '-', 'Selesai'),
('LOAN-6796', 72, 3, '2025-12-05 17:43:13', '2025-12-06', '2025-12-08', '-', '-', 'Disetujui');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id_pengembalian` int(11) NOT NULL,
  `id_peminjaman` varchar(10) DEFAULT NULL,
  `jumlah_pengembalian` int(11) NOT NULL,
  `tanggal_pengembalian` date NOT NULL,
  `catatan_pengembalian` text DEFAULT NULL,
  `foto_alat` varchar(255) DEFAULT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_pengembalian` enum('Menunggu','Valid') DEFAULT 'Menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengembalian`
--

INSERT INTO `pengembalian` (`id_pengembalian`, `id_peminjaman`, `jumlah_pengembalian`, `tanggal_pengembalian`, `catatan_pengembalian`, `foto_alat`, `tanggal_dibuat`, `status_pengembalian`) VALUES
(81, 'LOAN-6451', 0, '2025-12-05', '-', 'uploads/pengembalian/pengembalian_LOAN-6451_1764951318.jpeg', '2025-12-05 16:15:18', 'Valid'),
(82, 'LOAN-3177', 0, '2025-12-05', '-', 'uploads/pengembalian/pengembalian_LOAN-3177_1764952721.jpeg', '2025-12-05 16:38:41', 'Valid');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `nama_lengkap` varchar(30) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `tgl_daftar` datetime DEFAULT current_timestamp(),
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `status` enum('Pegawai','Mahasiswa','Dosen') DEFAULT 'Mahasiswa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `username`, `nama_lengkap`, `no_hp`, `password`, `tgl_daftar`, `role`, `status`) VALUES
(63, 'admin', 'adminsatu', '+6281999834034', '$2y$10$zL1ZYWYRME/eTkqPOnbDxu/4UF1EJtg4nBl3.WRMv529wjulNx0OC', '2025-06-28 19:00:31', 'admin', 'Pegawai'),
(71, 'arya', '-', '+6281999834030', '$2y$10$trVehroExT5QA8iLbS0ZV.e7UqBnirFeHnfde1FfIrg653mIY9V4u', '2025-07-28 11:08:09', 'user', 'Mahasiswa'),
(72, 'adiputra', 'Gede Adi Dwi Putra', '+6285647424741', '$2y$10$VAfJ./j00DXYGBscBXEQuupEp5Sm/75WOjlfDYnmvNL81QqTZ6TcS', '2025-12-04 10:49:18', 'user', 'Mahasiswa');

-- --------------------------------------------------------

--
-- Struktur dari tabel `peralatan`
--

CREATE TABLE `peralatan` (
  `id_peralatan` int(11) NOT NULL,
  `nama_peralatan` varchar(50) NOT NULL,
  `kategori` enum('Modern','Tradisional') DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `jumlah_baik` int(11) DEFAULT 0,
  `jumlah_rusak` int(11) DEFAULT 0,
  `tahun_pengadaan` year(4) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `peralatan`
--

INSERT INTO `peralatan` (`id_peralatan`, `nama_peralatan`, `kategori`, `jumlah`, `jumlah_baik`, `jumlah_rusak`, `tahun_pengadaan`, `deskripsi`, `gambar`) VALUES
(11, 'Kendang', 'Tradisional', 4, 4, 0, '2011', 'Memiliki panjang 72 cm, garis tengah\r\nteboakan besar 32 cm', 'img_686ca00cb42cd.jpg'),
(16, 'Ceng-ceng Kopyak', 'Tradisional', 6, 6, 0, '2011', '2 cakep dan dimainkan dengan\r\nmemukul kedua cakep (bilah)', 'img_686cf5f400e07.jpg'),
(17, 'Gangsa', 'Tradisional', 4, 1, 0, '2011', 'Gangsa dengan 10 bilah yang tuts\r\nlebih kecil dari pada ugal', 'img_686c9ff3336a5.jpg'),
(18, 'Drum', 'Modern', 1, 0, 1, '2012', '-', 'img_686cf7832ff3f.jpg'),
(19, 'Reong', 'Tradisional', 1, 1, 0, '2011', 'Memiliki 12 blok gong kecil dgn 12\r\nnada pelog', 'img_686cf7aec4230.jpg'),
(22, 'Angklung', 'Tradisional', 1, 1, 0, '2023', 'haha', 'img_686cf7ca6257c.jpeg'),
(23, 'Gong', 'Tradisional', 2, 1, 1, '2011', 'Gong wadon dengan ukuran\r\ndiameter 82 cm', 'img_6879f881d700b.jpg'),
(28, 'Ugal', 'Tradisional', 2, 2, 0, '2011', '10 bilah campuran kuningan,\r\ndibunyikan dengan panggul', 'img_6886eb9789e03.jpg'),
(29, 'Kantil', 'Tradisional', 4, 3, 1, '2011', 'Kantil dengan 10 bilah tuts paling\r\nkecil dengan nada oktaf terendah', 'img_6886ec0e2f5a2.jpg'),
(30, 'Jegogan', 'Tradisional', 2, 1, 0, '2011', 'Instrumen 5 bilah nada dengan tuts\r\nnada terendah', 'img_6886f23881a8b.jpg'),
(31, 'Kecek Pakebyaran', 'Tradisional', 1, 1, 0, '2011', '5 cakep ditungguh dan 2 cakep\r\nsebagai pemukul', 'img_6886f2b431d20.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `unit_peralatan`
--

CREATE TABLE `unit_peralatan` (
  `id_unit` int(11) NOT NULL,
  `id_peralatan` int(11) NOT NULL,
  `kode_unit` varchar(150) NOT NULL,
  `kondisi` enum('Baik','Rusak') DEFAULT 'Baik',
  `keterangan` enum('Tersedia','Dipinjam','Perlu Perbaikan') DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `unit_peralatan`
--

INSERT INTO `unit_peralatan` (`id_unit`, `id_peralatan`, `kode_unit`, `kondisi`, `keterangan`) VALUES
(3, 16, 'Ceng-ceng-01', 'Baik', 'Tersedia'),
(5, 16, 'Ceng-ceng-02', 'Baik', 'Tersedia'),
(9, 23, 'Gong-01', 'Baik', 'Tersedia'),
(11, 22, 'Angklung-01', 'Baik', 'Tersedia'),
(12, 11, 'Kendang-01', 'Baik', 'Tersedia'),
(13, 11, 'Kendang-02', 'Baik', 'Tersedia'),
(14, 11, 'Kendang-03', 'Baik', 'Tersedia'),
(15, 11, 'Kendang-04', 'Baik', 'Tersedia'),
(16, 17, 'Gangsa-01', 'Baik', 'Dipinjam'),
(17, 23, 'Gong-02', 'Rusak', 'Perlu Perbaikan'),
(18, 19, 'Reong', 'Baik', 'Tersedia'),
(19, 16, 'Ceng-ceng-03', 'Baik', 'Tersedia'),
(20, 16, 'Ceng-ceng-04', 'Baik', 'Tersedia'),
(21, 16, 'Ceng-ceng-05', 'Baik', 'Tersedia'),
(22, 16, 'Ceng-ceng-06', 'Baik', 'Tersedia'),
(23, 18, 'Drum-01', 'Rusak', 'Perlu Perbaikan'),
(24, 28, 'Ugal-01', 'Baik', 'Tersedia'),
(25, 28, 'Ugal-02', 'Baik', 'Tersedia'),
(26, 17, 'Gangsa-02', 'Baik', 'Tersedia'),
(27, 17, 'Gangsa-03', 'Baik', 'Dipinjam'),
(28, 17, 'Gangsa-04', 'Baik', 'Dipinjam'),
(29, 29, 'Kantil-01', 'Baik', 'Tersedia'),
(30, 29, 'Kantil-02', 'Baik', 'Tersedia'),
(31, 29, 'Kantil-03', 'Baik', 'Tersedia'),
(32, 29, 'Kantil-04', 'Rusak', 'Perlu Perbaikan'),
(33, 30, 'Jegogan-01', 'Baik', 'Dipinjam'),
(34, 30, 'Jegogan-02', 'Baik', 'Tersedia'),
(35, 31, 'Kecek Pakebyaran-01', 'Baik', 'Tersedia');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_peralatan` (`id_peralatan`),
  ADD KEY `detail_peminjaman_ibfk_1` (`id_peminjaman`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indeks untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id_pengembalian`),
  ADD KEY `id_peminjaman` (`id_peminjaman`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`);

--
-- Indeks untuk tabel `peralatan`
--
ALTER TABLE `peralatan`
  ADD PRIMARY KEY (`id_peralatan`);

--
-- Indeks untuk tabel `unit_peralatan`
--
ALTER TABLE `unit_peralatan`
  ADD PRIMARY KEY (`id_unit`),
  ADD UNIQUE KEY `kode_unit` (`kode_unit`),
  ADD KEY `id_peralatan` (`id_peralatan`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=250;

--
-- AUTO_INCREMENT untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id_pengembalian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT untuk tabel `peralatan`
--
ALTER TABLE `peralatan`
  MODIFY `id_peralatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT untuk tabel `unit_peralatan`
--
ALTER TABLE `unit_peralatan`
  MODIFY `id_unit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD CONSTRAINT `detail_peminjaman_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_peminjaman_ibfk_2` FOREIGN KEY (`id_peralatan`) REFERENCES `peralatan` (`id_peralatan`);

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `fk_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `fk_pengembalian_peminjaman` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `unit_peralatan`
--
ALTER TABLE `unit_peralatan`
  ADD CONSTRAINT `unit_peralatan_ibfk_1` FOREIGN KEY (`id_peralatan`) REFERENCES `peralatan` (`id_peralatan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
