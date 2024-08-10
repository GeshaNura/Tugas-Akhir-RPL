-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 10, 2024 at 06:49 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `resto3`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `status` enum('Masih diproses','Selesai','Tidak dapat dibuat') DEFAULT NULL,
  `deskripsi_pesanan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail`, `id_pesanan`, `id_menu`, `jumlah`, `status`, `deskripsi_pesanan`) VALUES
(192, 508, 33, 1, 'Selesai', 'ga pedes bang'),
(193, 508, 34, 1, 'Selesai', ''),
(194, 508, 32, 1, 'Selesai', ''),
(195, 508, 31, 1, 'Selesai', ''),
(196, 509, 33, 1, 'Selesai', ''),
(197, 510, 33, 1, 'Masih diproses', 'pedas bos'),
(198, 510, 34, 1, 'Masih diproses', 'kurangin gulanya'),
(199, 511, 34, 1, 'Masih diproses', 'kurangin esnya'),
(200, 511, 32, 1, 'Masih diproses', 'esnya rasa vanilla aja'),
(201, 512, 31, 1, 'Masih diproses', 'kentangnya yang garing'),
(202, 513, 33, 1, 'Selesai', 'jangan pedes pedes');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id_karyawan` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `kontak` varchar(15) DEFAULT NULL,
  `jabatan` enum('pelayan','koki','kasir','manager') DEFAULT NULL,
  `password_karyawan` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `nama`, `kontak`, `jabatan`, `password_karyawan`, `alamat`) VALUES
(30, 'manajer', '081xxxxxxx', 'manager', 'manajer', 'manajer'),
(31, 'koki', '081', 'koki', 'koki', 'koki'),
(34, 'kasir', '081xxxxxxx', 'kasir', 'kasir', 'kasir'),
(35, 'pelayan', '081', 'pelayan', 'pelayan', 'pelayan');

-- --------------------------------------------------------

--
-- Table structure for table `meja`
--

CREATE TABLE `meja` (
  `id_meja` int(11) NOT NULL,
  `nomor_meja` int(11) DEFAULT NULL,
  `kapasitas` int(11) DEFAULT NULL,
  `status_meja` enum('Sedia','Ditempati') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meja`
--

INSERT INTO `meja` (`id_meja`, `nomor_meja`, `kapasitas`, `status_meja`) VALUES
(1, 1, 4, 'Sedia'),
(2, 2, 2, 'Sedia'),
(3, 3, 6, 'Sedia'),
(4, 4, 6, 'Sedia'),
(5, 5, 1, 'Sedia'),
(6, 6, 6, 'Ditempati'),
(7, 7, 1, 'Ditempati'),
(12, 8, 8, 'Sedia');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL,
  `nama_menu` varchar(100) DEFAULT NULL,
  `harga` decimal(10,2) DEFAULT NULL,
  `jenis_menu` enum('Makanan','Minuman','Penutup','Kudapan') NOT NULL,
  `kapasitas_menu` int(11) DEFAULT NULL,
  `status_menu` enum('Sedia','Tidak Sedia') NOT NULL,
  `deskripsi_Menu` text DEFAULT NULL,
  `gambar_menu` varchar(255) DEFAULT NULL,
  `rekomendasi` enum('ya','tidak') DEFAULT 'tidak'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `nama_menu`, `harga`, `jenis_menu`, `kapasitas_menu`, `status_menu`, `deskripsi_Menu`, `gambar_menu`, `rekomendasi`) VALUES
(31, 'kentang gorenag', 13000.00, 'Kudapan', 1, 'Sedia', 'kentang gorenag', '66b6e941a6d2d.png', 'tidak'),
(32, 'Es Krim', 8000.00, 'Penutup', 1, 'Sedia', '0', '66b6e980cbd47.png', 'tidak'),
(33, 'Salmon Panggang', 39000.00, 'Makanan', 1, 'Sedia', '0', '66b6e9bb32c4f.png', 'tidak'),
(34, 'Jus Jeruk', 10000.00, 'Minuman', 1, 'Sedia', 'JUS DARI JERUK SEGAR PILIHAN', '66b6e9ed3a611.png', 'ya');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama_pelanggan`) VALUES
(678, 'Kilang'),
(679, 'Kolli'),
(680, 'yono'),
(681, 'KK'),
(682, 'yono'),
(683, 'hh'),
(684, 'Lopa'),
(685, 'ss'),
(686, 'HAHAM'),
(687, 'ZZ'),
(688, 'KK'),
(689, 'azmi'),
(690, 'haikal'),
(691, 'dim'),
(692, 'tuan tanah'),
(693, '12'),
(694, 'dimas'),
(695, 'haikal'),
(696, 'oi'),
(697, 'hi'),
(698, 'hi'),
(699, 'hihu'),
(700, 'hihu'),
(701, '2'),
(702, 'rt'),
(703, 'dinda'),
(704, 'dafa'),
(705, 'azmi'),
(706, 'haikal'),
(707, 'Dimas'),
(708, 'Mang oleh'),
(709, 'tuan tanah'),
(710, 'haikal');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pesanan` int(11) DEFAULT NULL,
  `id_karyawan` int(11) DEFAULT NULL,
  `total_harga` decimal(10,2) DEFAULT NULL,
  `tanggal_waktu_Pembayaran` timestamp NULL DEFAULT current_timestamp(),
  `metode_pembayaran` enum('Tunai','Kartu') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pesanan`, `id_karyawan`, `total_harga`, `tanggal_waktu_Pembayaran`, `metode_pembayaran`) VALUES
(40, 508, NULL, 77000.00, '2024-08-10 04:44:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id_karyawan` int(11) DEFAULT NULL,
  `jabatan` enum('Koki','Pelayan','Kasir') DEFAULT NULL,
  `nama_pelanggan` varchar(100) DEFAULT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `id_menu` int(11) DEFAULT NULL,
  `nama_menu` varchar(100) DEFAULT NULL,
  `jumlah_pelanggan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `id_karyawan` int(11) DEFAULT NULL,
  `id_meja` int(11) DEFAULT NULL,
  `tanggal_waktu_pesanan` timestamp NULL DEFAULT current_timestamp(),
  `status_pembayaran` enum('belum','sudah') DEFAULT 'belum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_pelanggan`, `id_karyawan`, `id_meja`, `tanggal_waktu_pesanan`, `status_pembayaran`) VALUES
(481, 678, NULL, 2, '2024-08-04 14:15:46', ''),
(482, 679, NULL, 2, '2024-08-04 14:19:12', ''),
(483, 680, NULL, 2, '2024-08-04 14:22:07', ''),
(484, 681, NULL, 6, '2024-08-04 14:27:13', ''),
(485, 682, NULL, 2, '2024-08-04 15:16:13', ''),
(486, 683, NULL, 2, '2024-08-04 15:24:23', ''),
(487, 684, NULL, 3, '2024-08-04 16:01:01', ''),
(488, 685, NULL, 3, '2024-08-04 16:02:57', ''),
(489, 686, NULL, 4, '2024-08-04 16:06:46', ''),
(490, 687, NULL, 4, '2024-08-04 16:08:38', ''),
(491, 688, NULL, 1, '2024-08-04 16:19:30', ''),
(492, 689, NULL, 7, '2024-08-09 14:16:36', ''),
(493, 690, NULL, 7, '2024-08-09 14:16:58', ''),
(494, 691, NULL, 1, '2024-08-09 14:17:15', ''),
(495, 692, NULL, 5, '2024-08-09 14:17:45', ''),
(496, 693, NULL, 4, '2024-08-09 14:18:42', ''),
(497, 694, NULL, 4, '2024-08-09 14:37:46', ''),
(498, 695, NULL, 7, '2024-08-09 14:38:07', ''),
(499, 696, NULL, 1, '2024-08-09 14:40:35', ''),
(503, 700, NULL, 7, '2024-08-09 14:45:48', ''),
(504, 701, NULL, 7, '2024-08-09 14:50:51', ''),
(505, 702, NULL, 4, '2024-08-09 14:51:09', ''),
(506, 703, NULL, 4, '2024-08-09 14:51:24', ''),
(507, 704, NULL, 2, '2024-08-09 14:51:39', ''),
(508, 705, NULL, 1, '2024-08-10 04:25:07', ''),
(509, 706, NULL, 2, '2024-08-10 04:25:21', 'belum'),
(510, 707, NULL, 1, '2024-08-10 04:41:41', 'belum'),
(511, 708, NULL, 1, '2024-08-10 04:42:26', 'belum'),
(512, 709, NULL, 6, '2024-08-10 04:42:42', 'belum'),
(513, 710, NULL, 6, '2024-08-10 04:43:05', 'belum');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`) USING BTREE,
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_menu` (`id_menu`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`);

--
-- Indexes for table `meja`
--
ALTER TABLE `meja`
  ADD PRIMARY KEY (`id_meja`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD KEY `id_karyawan` (`id_karyawan`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `id_karyawan` (`id_karyawan`),
  ADD KEY `id_meja` (`id_meja`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=203;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `meja`
--
ALTER TABLE `meja`
  MODIFY `id_meja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=711;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=514;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `penjualan_ibfk_2` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pesanan_ibfk_3` FOREIGN KEY (`id_meja`) REFERENCES `meja` (`id_meja`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
