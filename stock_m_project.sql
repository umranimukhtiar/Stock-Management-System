-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2025 at 06:07 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `malaria_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `commodities`
--

CREATE TABLE `commodities` (
  `id` int(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `unit` varchar(64) NOT NULL,
  `min_consumption` int(64) NOT NULL,
  `max_consumption` int(64) NOT NULL,
  `description` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commodities`
--

INSERT INTO `commodities` (`id`, `name`, `unit`, `min_consumption`, `max_consumption`, `description`, `created_at`) VALUES
(1, 'Panadol', 'Tab', 0, 0, '', '2024-12-18 07:43:06.275182'),
(2, 'Cophurb', 'Syrup', 0, 0, '', '2024-12-18 07:43:06.275182'),
(3, 'Dispirin', '', 0, 0, 'Aspirin tablets', '2024-12-31 10:40:12.000000'),
(4, 'Primaquine 7.5mg', '', 0, 0, 'Malaria Medicin Primaquine 7.5mg', '2025-01-11 05:31:19.000000');

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` int(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `province` varchar(64) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`id`, `name`, `province`, `created_at`) VALUES
(1, 'Matiari', '', '2024-12-18 07:38:52.412311'),
(2, 'Larkana', '', '2024-12-18 07:38:52.412311'),
(3, 'Hub', 'Balochistan', '2025-01-11 05:32:31.000000'),
(4, 'Lasbela', 'Balochistan', '2025-01-11 05:40:55.100952'),
(5, 'Kech', 'Balochistan', '2025-01-11 05:33:05.000000');

-- --------------------------------------------------------

--
-- Table structure for table `stock_in`
--

CREATE TABLE `stock_in` (
  `id` int(64) NOT NULL,
  `folio_no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `commodity_id` int(64) NOT NULL,
  `quantity` int(64) NOT NULL,
  `remarks` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_in`
--

INSERT INTO `stock_in` (`id`, `folio_no`, `commodity_id`, `quantity`, `remarks`, `created_at`) VALUES
(5, '1', 1, 0, 'Not any.', '2025-01-11 05:29:56.395141'),
(6, '2', 1, 0, '2nd time.', '2024-12-31 11:55:54.535726'),
(7, '1235', 2, 0, 'Not any.', '2025-01-11 05:28:38.265116'),
(8, '1547', 4, 1700, 'Received in good condition.', '2025-01-11 05:33:32.094251');

-- --------------------------------------------------------

--
-- Table structure for table `stock_out`
--

CREATE TABLE `stock_out` (
  `id` int(64) NOT NULL,
  `commodity_id` int(64) NOT NULL,
  `district_id` int(64) NOT NULL,
  `quantity` int(64) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_out`
--

INSERT INTO `stock_out` (`id`, `commodity_id`, `district_id`, `quantity`, `created_at`) VALUES
(16, 1, 1, 90, '2024-12-31 11:54:23.000000'),
(17, 1, 1, 90, '2024-12-31 11:55:28.000000'),
(18, 1, 2, 20, '2024-12-31 11:55:54.000000'),
(19, 2, 1, 200, '2025-01-11 05:28:38.000000'),
(20, 4, 3, 100, '2025-01-11 05:33:22.000000'),
(21, 4, 4, 200, '2025-01-11 05:33:32.000000');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `commodities`
--
ALTER TABLE `commodities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_in`
--
ALTER TABLE `stock_in`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_out`
--
ALTER TABLE `stock_out`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `commodities`
--
ALTER TABLE `commodities`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `stock_in`
--
ALTER TABLE `stock_in`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `stock_out`
--
ALTER TABLE `stock_out`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
