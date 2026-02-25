-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2026 at 02:01 AM
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
-- Database: `loafly_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `archived_products`
--

CREATE TABLE `archived_products` (
  `id` int(11) NOT NULL,
  `original_product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `deleted_by` int(11) NOT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `original_created_at` timestamp NULL DEFAULT NULL,
  `reason` varchar(255) DEFAULT 'Deleted by admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `archived_products`
--
ALTER TABLE `archived_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_original_product_id` (`original_product_id`),
  ADD KEY `idx_deleted_by` (`deleted_by`),
  ADD KEY `idx_deleted_at` (`deleted_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `archived_products`
--
ALTER TABLE `archived_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
