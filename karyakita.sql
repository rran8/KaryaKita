-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 16, 2025 at 08:40 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `karyakita`
--

-- --------------------------------------------------------

--
-- Table structure for table `artworks`
--

CREATE TABLE `artworks` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `student_class` varchar(50) DEFAULT NULL,
  `category_slug` varchar(100) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text,
  `media_type` varchar(50) NOT NULL,
  `media_url` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `upload_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `views` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `artworks`
--

INSERT INTO `artworks` (`id`, `title`, `student_name`, `student_class`, `category_slug`, `category_name`, `description`, `media_type`, `media_url`, `status`, `created_at`, `upload_date`, `views`) VALUES
(7, 'YESS', 'ADMIMM', 'AAA', 'seni-lukis', 'Seni Lukis', '<p>SSS</p>\r\n', 'image', 'uploads/1752638416_rating.jpeg', 'published', '2025-07-16 04:00:16', '2025-07-16 11:00:16', 0),
(8, 'TEST', 'rantisi', 'undefined', 'fotografi', 'Fotografi', 'pepek\n', 'image', 'uploads/1752639387_report2.png', 'published', '2025-07-16 04:16:27', '2025-07-16 11:16:27', 0),
(13, 'test', 'test', 'xii rpl 1', 'ict', 'ICT', '<p>aaa</p>\r\n', 'pdf', 'uploads/1752649529_Requirement_Website_KaryaKita.pdf', 'published', '2025-07-16 07:05:29', '2025-07-16 14:05:29', 0),
(14, 'ayayayay', 'adadada', 'dada', 'kerajinan', 'Kerajinan', '<p>dadaa</p>\r\n', 'image', 'uploads/1752649553_rating.jpeg', 'published', '2025-07-16 07:05:53', '2025-07-16 14:05:53', 0),
(15, 'testing', 'awokawokk', 'awww', 'kerajinan', 'Kerajinan', '<p>aaaa</p>\r\n', 'image', 'uploads/1752649581_report2.png', 'published', '2025-07-16 07:06:21', '2025-07-16 14:06:21', 0),
(16, 'aaaa', 'eeee', 'wwww', 'seni-lukis', 'Seni Lukis', '<p>dwdw</p>\r\n', 'image', 'uploads/1752649602_report3.jpeg', 'published', '2025-07-16 07:06:42', '2025-07-16 14:06:42', 0);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Seni Lukis', 'seni-lukis'),
(3, 'Kerajinan', 'kerajinan'),
(4, 'ICT', 'ict');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`) VALUES
(1, 'admin', 'admin123', 'admin'),
(2, 'admin2', '$2y$10$AzltPTPZAFHt7jeER7PKburjM7etXlSd.kCdfcuwqsTaAbL94IR4u', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artworks`
--
ALTER TABLE `artworks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artworks`
--
ALTER TABLE `artworks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
