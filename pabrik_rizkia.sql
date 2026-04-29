-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2026 at 04:02 AM
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
-- Database: `pabrik_rizkia`
--

-- --------------------------------------------------------

--
-- Table structure for table `jobs_rizkia`
--

CREATE TABLE `jobs_rizkia` (
  `id_rizkia` int(11) NOT NULL,
  `nama_job_rizkia` varchar(100) DEFAULT NULL,
  `durasi_rizkia` int(11) DEFAULT NULL,
  `deadline_rizkia` date DEFAULT NULL,
  `status_rizkia` varchar(50) DEFAULT NULL,
  `jumlah_rizkia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs_rizkia`
--

INSERT INTO `jobs_rizkia` (`id_rizkia`, `nama_job_rizkia`, `durasi_rizkia`, `deadline_rizkia`, `status_rizkia`, `jumlah_rizkia`) VALUES
(9, 'potong besi', NULL, '2026-04-30', 'Menunggu', 100);

-- --------------------------------------------------------

--
-- Table structure for table `laporan_rizkia`
--

CREATE TABLE `laporan_rizkia` (
  `id_rizkia` int(11) NOT NULL,
  `isi_rizkia` text DEFAULT NULL,
  `tanggal_rizkia` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_rizkia`
--

INSERT INTO `laporan_rizkia` (`id_rizkia`, `isi_rizkia`, `tanggal_rizkia`) VALUES
(1, 'lancar', '2026-04-23');

-- --------------------------------------------------------

--
-- Table structure for table `mesin_rizkia`
--

CREATE TABLE `mesin_rizkia` (
  `id_rizkia` int(11) NOT NULL,
  `nama_mesin_rizkia` varchar(100) DEFAULT NULL,
  `status_rizkia` varchar(50) DEFAULT NULL,
  `durasi_produksi_rizkia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mesin_rizkia`
--

INSERT INTO `mesin_rizkia` (`id_rizkia`, `nama_mesin_rizkia`, `status_rizkia`, `durasi_produksi_rizkia`) VALUES
(13, 'MCH-CUT-001 | Laser Cutting Sheet Metal', 'Normal', 18),
(14, 'MCH-CNC-001 | CNC Lathe Presisi', 'Normal', 25),
(15, 'MCH-CNC-002 | CNC Milling 3-Axis', 'Normal', 28),
(16, 'MCH-PRS-001 | Hydraulic Press 200T', 'Normal', 22),
(17, 'MCH-STP-001 | Progressive Stamping Press', 'Normal', 16),
(18, 'MCH-DRL-001 | Multi Spindle Drilling', 'Normal', 14),
(19, 'MCH-TAP-001 | Tapping Machine M3-M16', 'Normal', 12),
(20, 'MCH-GRD-001 | Surface Grinding Machine', 'Normal', 20),
(21, 'MCH-GRD-002 | Centerless Grinding', 'Normal', 24),
(22, 'MCH-HTR-001 | Heat Treatment Furnace', 'Normal', 35),
(23, 'MCH-QCH-001 | Oil Quenching Line', 'Normal', 30),
(24, 'MCH-PLT-001 | Zinc Electroplating Line', 'Normal', 27),
(25, 'MCH-PLT-002 | Nickel Chrome Plating', 'Normal', 32),
(26, 'MCH-SHT-001 | Shot Blasting Machine', 'Normal', 19),
(27, 'MCH-WLD-001 | Spot Welding Robot', 'Normal', 21),
(28, 'MCH-WLD-002 | MIG Welding Station', 'Normal', 23),
(29, 'MCH-ASM-001 | Bearing Press Assembly', 'Normal', 15),
(30, 'MCH-ASM-002 | Seal Press Assembly', 'Normal', 13),
(31, 'MCH-PNT-001 | Powder Coating Booth', 'Normal', 26),
(32, 'MCH-OVN-001 | Curing Oven Line', 'Normal', 29),
(33, 'MCH-BAL-001 | Dynamic Balancing Machine', 'Normal', 17),
(34, 'MCH-QC-001 | CMM Inspection Station', 'Normal', 10),
(35, 'MCH-QC-002 | Hardness Tester', 'Normal', 8),
(36, 'MCH-PCK-001 | Packing & Labeling Line', 'Normal', 9),
(37, 'MCH-TRM-001 | Rubber Trimming Press', 'Normal', 11),
(38, 'MCH-INJ-001 | Plastic Injection Molding', 'Normal', 31),
(39, 'MCH-CMP-001 | Air Compressor Central', 'Perbaikan', 5),
(40, 'MCH-UTL-001 | Cooling Tower Utility', 'Normal', 6);

-- --------------------------------------------------------

--
-- Table structure for table `scheduling_rizkia`
--

CREATE TABLE `scheduling_rizkia` (
  `id_rizkia` int(11) NOT NULL,
  `job_id_rizkia` int(11) DEFAULT NULL,
  `mesin_id_rizkia` int(11) DEFAULT NULL,
  `operator_id_rizkia` int(11) DEFAULT NULL,
  `waktu_mulai_rizkia` datetime DEFAULT NULL,
  `status_rizkia` varchar(50) DEFAULT NULL,
  `waktu_selesai_rizkia` datetime DEFAULT NULL,
  `notif_rizkia` int(11) DEFAULT 0,
  `kendala_rizkia` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scheduling_rizkia`
--

INSERT INTO `scheduling_rizkia` (`id_rizkia`, `job_id_rizkia`, `mesin_id_rizkia`, `operator_id_rizkia`, `waktu_mulai_rizkia`, `status_rizkia`, `waktu_selesai_rizkia`, `notif_rizkia`, `kendala_rizkia`) VALUES
(9, 2, 13, 5, '2026-04-24 11:50:00', 'Selesai', '2026-04-25 17:50:00', 0, 'lancar'),
(12, 2, 13, 5, '2026-04-23 13:38:00', 'Selesai', '2026-04-23 13:40:37', 0, ''),
(13, 7, 13, 5, '2026-04-23 13:53:00', 'Selesai', '2026-04-23 13:55:02', 0, ''),
(14, 7, 13, 5, '2026-04-23 13:57:00', 'Selesai', '2026-04-24 01:57:00', 0, NULL),
(15, 9, 13, 5, '2026-04-28 08:30:00', 'Dijadwalkan', '2026-05-06 16:30:00', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_rizkia`
--

CREATE TABLE `users_rizkia` (
  `id_rizkia` int(11) NOT NULL,
  `username_rizkia` varchar(50) DEFAULT NULL,
  `password_rizkia` varchar(255) DEFAULT NULL,
  `role_rizkia` enum('admin','operator','engineering') DEFAULT NULL,
  `created_at_rizkia` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_rizkia`
--

INSERT INTO `users_rizkia` (`id_rizkia`, `username_rizkia`, `password_rizkia`, `role_rizkia`, `created_at_rizkia`) VALUES
(5, 'operator', '202cb962ac59075b964b07152d234b70', 'operator', '2026-04-22 13:34:59'),
(6, 'engineering', '202cb962ac59075b964b07152d234b70', 'engineering', '2026-04-23 02:28:53'),
(7, 'admin', '202cb962ac59075b964b07152d234b70', 'admin', '2026-04-23 04:37:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jobs_rizkia`
--
ALTER TABLE `jobs_rizkia`
  ADD PRIMARY KEY (`id_rizkia`);

--
-- Indexes for table `laporan_rizkia`
--
ALTER TABLE `laporan_rizkia`
  ADD PRIMARY KEY (`id_rizkia`);

--
-- Indexes for table `mesin_rizkia`
--
ALTER TABLE `mesin_rizkia`
  ADD PRIMARY KEY (`id_rizkia`);

--
-- Indexes for table `scheduling_rizkia`
--
ALTER TABLE `scheduling_rizkia`
  ADD PRIMARY KEY (`id_rizkia`);

--
-- Indexes for table `users_rizkia`
--
ALTER TABLE `users_rizkia`
  ADD PRIMARY KEY (`id_rizkia`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jobs_rizkia`
--
ALTER TABLE `jobs_rizkia`
  MODIFY `id_rizkia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `laporan_rizkia`
--
ALTER TABLE `laporan_rizkia`
  MODIFY `id_rizkia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mesin_rizkia`
--
ALTER TABLE `mesin_rizkia`
  MODIFY `id_rizkia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `scheduling_rizkia`
--
ALTER TABLE `scheduling_rizkia`
  MODIFY `id_rizkia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users_rizkia`
--
ALTER TABLE `users_rizkia`
  MODIFY `id_rizkia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

-- --------------------------------------------------------
--
--

CREATE TABLE IF NOT EXISTS `sparepart_master` (
  `id_rizkia` int(11) NOT NULL AUTO_INCREMENT,
  `kode_part_rizkia` varchar(50) NOT NULL,
  `nama_part_rizkia` varchar(150) NOT NULL,
  `kategori_rizkia` varchar(100) DEFAULT NULL,
  `tipe_motor_rizkia` varchar(100) DEFAULT NULL,
  `satuan_rizkia` varchar(20) DEFAULT 'pcs',
  `grade_kualitas_rizkia` varchar(50) DEFAULT NULL,
  `aktif_rizkia` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_rizkia`),
  UNIQUE KEY `uniq_kode_part_rizkia` (`kode_part_rizkia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `sparepart_master` (`id_rizkia`, `kode_part_rizkia`, `nama_part_rizkia`, `kategori_rizkia`, `tipe_motor_rizkia`, `satuan_rizkia`, `grade_kualitas_rizkia`, `aktif_rizkia`) VALUES
(1, 'SP-BRK-001', 'Brake Pad Depan', 'Sistem Rem', 'Bebek 110-125cc', 'set', 'OEM', 1),
(2, 'SP-BRK-002', 'Brake Shoe Belakang', 'Sistem Rem', 'Bebek 110-125cc', 'set', 'OEM', 1),
(3, 'SP-FLT-001', 'Filter Udara', 'Sistem Intake', 'Matic 110-160cc', 'pcs', 'OEM', 1),
(4, 'SP-FLT-002', 'Filter Oli', 'Sistem Pelumasan', 'Sport 150-250cc', 'pcs', 'OEM', 1),
(5, 'SP-GSK-001', 'Gasket Cylinder Head', 'Engine Parts', 'Universal 110-250cc', 'pcs', 'A', 1),
(6, 'SP-GSK-002', 'Gasket Crankcase', 'Engine Parts', 'Universal 110-250cc', 'pcs', 'A', 1),
(7, 'SP-BRG-001', 'Bearing Roda Depan 6201', 'Bearing', 'Universal', 'pcs', 'OEM', 1),
(8, 'SP-BRG-002', 'Bearing Roda Belakang 6202', 'Bearing', 'Universal', 'pcs', 'OEM', 1),
(9, 'SP-CHN-001', 'Timing Chain', 'Engine Parts', 'Matic 110-160cc', 'pcs', 'A', 1),
(10, 'SP-SPR-001', 'Shockbreaker Belakang', 'Suspensi', 'Matic 110-160cc', 'pcs', 'A', 1),
(11, 'SP-CLT-001', 'Kampas Kopling', 'Transmisi', 'Bebek 110-125cc', 'set', 'OEM', 1),
(12, 'SP-CLT-002', 'Rumah Kopling', 'Transmisi', 'Bebek 110-125cc', 'pcs', 'A', 1),
(13, 'SP-PST-001', 'Piston Kit 52.4mm', 'Engine Parts', '125cc', 'set', 'OEM', 1),
(14, 'SP-RNG-001', 'Ring Piston Set', 'Engine Parts', '125cc', 'set', 'A', 1),
(15, 'SP-VLV-001', 'Klep In', 'Cylinder Head', '110-150cc', 'pcs', 'A', 1),
(16, 'SP-VLV-002', 'Klep Ex', 'Cylinder Head', '110-150cc', 'pcs', 'A', 1),
(17, 'SP-SEAL-001', 'Oil Seal Kruk As', 'Seal', 'Universal', 'pcs', 'OEM', 1),
(18, 'SP-SEAL-002', 'Oil Seal Shock Depan', 'Seal', 'Universal', 'pcs', 'OEM', 1),
(19, 'SP-GEAR-001', 'Gear Set Depan 14T', 'Final Drive', 'Bebek/Sport', 'pcs', 'A', 1),
(20, 'SP-GEAR-002', 'Gear Set Belakang 38T', 'Final Drive', 'Bebek/Sport', 'pcs', 'A', 1),
(21, 'SP-CHN-002', 'Rantai Final 428H', 'Final Drive', 'Bebek/Sport', 'pcs', 'A', 1),
(22, 'SP-HDL-001', 'Handle Rem Kanan', 'Body & Control', 'Universal', 'pcs', 'B', 1),
(23, 'SP-HDL-002', 'Handle Kopling Kiri', 'Body & Control', 'Universal', 'pcs', 'B', 1),
(24, 'SP-CBL-001', 'Kabel Gas', 'Body & Control', 'Matic 110-125cc', 'pcs', 'B', 1),
(25, 'SP-CBL-002', 'Kabel Kopling', 'Body & Control', 'Bebek/Sport', 'pcs', 'B', 1),
(26, 'SP-ELC-001', 'Regulator Rectifier', 'Kelistrikan', 'Universal', 'pcs', 'OEM', 1),
(27, 'SP-ELC-002', 'Ignition Coil', 'Kelistrikan', 'Universal', 'pcs', 'OEM', 1),
(28, 'SP-ELC-003', 'Starter Relay', 'Kelistrikan', 'Universal', 'pcs', 'OEM', 1),
(29, 'SP-BDY-001', 'Footstep Belakang', 'Body & Control', 'Matic 110-160cc', 'set', 'B', 1),
(30, 'SP-BDY-002', 'Spion Kanan', 'Body & Control', 'Universal', 'pcs', 'B', 1);

CREATE TABLE IF NOT EXISTS `sparepart_bom` (
  `id_rizkia` int(11) NOT NULL AUTO_INCREMENT,
  `sparepart_id_rizkia` int(11) NOT NULL,
  `nama_material_rizkia` varchar(150) NOT NULL,
  `qty_per_unit_rizkia` decimal(12,3) NOT NULL DEFAULT 0.000,
  `satuan_rizkia` varchar(20) DEFAULT 'pcs',
  PRIMARY KEY (`id_rizkia`),
  KEY `idx_bom_sparepart_rizkia` (`sparepart_id_rizkia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `routing_proses` (
  `id_rizkia` int(11) NOT NULL AUTO_INCREMENT,
  `sparepart_id_rizkia` int(11) NOT NULL,
  `urutan_rizkia` int(11) NOT NULL,
  `nama_operasi_rizkia` varchar(120) NOT NULL,
  `mesin_default_id_rizkia` int(11) DEFAULT NULL,
  `durasi_menit_rizkia` int(11) DEFAULT 0,
  PRIMARY KEY (`id_rizkia`),
  KEY `idx_routing_sparepart_rizkia` (`sparepart_id_rizkia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `work_order` (
  `id_rizkia` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_wo_rizkia` varchar(50) NOT NULL,
  `sparepart_id_rizkia` int(11) NOT NULL,
  `qty_target_rizkia` int(11) NOT NULL,
  `due_date_rizkia` date DEFAULT NULL,
  `status_rizkia` varchar(50) DEFAULT 'Draft',
  PRIMARY KEY (`id_rizkia`),
  UNIQUE KEY `uniq_nomor_wo_rizkia` (`nomor_wo_rizkia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `work_order_operasi` (
  `id_rizkia` int(11) NOT NULL AUTO_INCREMENT,
  `work_order_id_rizkia` int(11) NOT NULL,
  `routing_id_rizkia` int(11) NOT NULL,
  `operator_id_rizkia` int(11) DEFAULT NULL,
  `mesin_id_rizkia` int(11) DEFAULT NULL,
  `waktu_mulai_rizkia` datetime DEFAULT NULL,
  `waktu_selesai_rizkia` datetime DEFAULT NULL,
  `status_rizkia` varchar(50) DEFAULT 'Dijadwalkan',
  PRIMARY KEY (`id_rizkia`),
  KEY `idx_wo_operasi_wo_rizkia` (`work_order_id_rizkia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `stok_bahan` (
  `id_rizkia` int(11) NOT NULL AUTO_INCREMENT,
  `nama_material_rizkia` varchar(150) NOT NULL,
  `stok_rizkia` decimal(12,3) NOT NULL DEFAULT 0.000,
  `satuan_rizkia` varchar(20) DEFAULT 'pcs',
  `minimum_stok_rizkia` decimal(12,3) NOT NULL DEFAULT 0.000,
  PRIMARY KEY (`id_rizkia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `stok_barang_jadi` (
  `id_rizkia` int(11) NOT NULL AUTO_INCREMENT,
  `sparepart_id_rizkia` int(11) NOT NULL,
  `stok_rizkia` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_rizkia`),
  KEY `idx_stok_fg_sparepart_rizkia` (`sparepart_id_rizkia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `qc_result` (
  `id_rizkia` int(11) NOT NULL AUTO_INCREMENT,
  `work_order_operasi_id_rizkia` int(11) DEFAULT NULL,
  `lot_no_rizkia` varchar(50) DEFAULT NULL,
  `status_qc_rizkia` enum('Lulus','Rework','Reject') DEFAULT 'Lulus',
  `catatan_rizkia` text DEFAULT NULL,
  `created_at_rizkia` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_rizkia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `lot_traceability` (
  `id_rizkia` int(11) NOT NULL AUTO_INCREMENT,
  `lot_no_rizkia` varchar(50) NOT NULL,
  `work_order_id_rizkia` int(11) DEFAULT NULL,
  `operator_id_rizkia` int(11) DEFAULT NULL,
  `mesin_id_rizkia` int(11) DEFAULT NULL,
  `keterangan_rizkia` text DEFAULT NULL,
  `created_at_rizkia` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_rizkia`),
  KEY `idx_trace_lot_rizkia` (`lot_no_rizkia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `maintenance_plan` (
  `id_rizkia` int(11) NOT NULL AUTO_INCREMENT,
  `mesin_id_rizkia` int(11) NOT NULL,
  `tanggal_pm_rizkia` date NOT NULL,
  `checklist_rizkia` text DEFAULT NULL,
  `status_rizkia` varchar(50) DEFAULT 'Terjadwal',
  PRIMARY KEY (`id_rizkia`),
  KEY `idx_pm_mesin_rizkia` (`mesin_id_rizkia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `maintenance_log` (
  `id_rizkia` int(11) NOT NULL AUTO_INCREMENT,
  `mesin_id_rizkia` int(11) NOT NULL,
  `mulai_rizkia` datetime DEFAULT NULL,
  `selesai_rizkia` datetime DEFAULT NULL,
  `durasi_menit_rizkia` int(11) DEFAULT NULL,
  `catatan_rizkia` text DEFAULT NULL,
  PRIMARY KEY (`id_rizkia`),
  KEY `idx_ml_mesin_rizkia` (`mesin_id_rizkia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
