-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 08, 2024 at 02:28 AM
-- Server version: 8.0.39-cll-lve
-- PHP Version: 8.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sahlhafi_rm_unikom`
--

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('pelayan','koki','kasir') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `is_available` tinyint(1) GENERATED ALWAYS AS ((`stock` > 0)) STORED,
  `category` enum('makanan','minuman','camilan') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `price`, `stock`, `category`) VALUES
(1, 'Nasi Goreng', 18000.00, 10, 'makanan'),
(2, 'Es Teh', 5000.00, 97, 'minuman'),
(7, 'Mie Hot', 15000.00, 48, 'makanan'),
(8, 'Mie Ramen', 17000.00, 49, 'makanan'),
(9, 'Mie Seafood', 16000.00, 49, 'makanan'),
(10, 'Mie Kari', 16000.00, 48, 'makanan'),
(11, 'Nasi Bakar', 15000.00, 50, 'makanan'),
(12, 'Ayam Pop', 16000.00, 50, 'makanan'),
(13, 'Ayam Geprek', 14000.00, 50, 'makanan'),
(14, 'Es jeruk', 15000.00, 99, 'minuman'),
(15, 'Es Buah', 17000.00, 99, 'minuman'),
(16, 'Es Serut', 16000.00, 100, 'minuman'),
(17, 'Jus Mangga', 16000.00, 100, 'minuman'),
(18, 'Jus Mangga', 16000.00, 100, 'minuman'),
(19, 'Jus Nanas', 17000.00, 100, 'minuman'),
(20, 'Jus Alpukat', 15000.00, 99, 'minuman'),
(21, 'Jus Wortel', 16000.00, 100, 'minuman'),
(25, 'Siomay', 15000.00, 40, 'camilan'),
(26, 'Udang Keju', 17000.00, 40, 'camilan'),
(27, 'Udang Rambutan', 16000.00, 40, 'camilan'),
(28, 'Pisang Coklat', 16000.00, 40, 'camilan'),
(29, 'Roti Bakar', 17000.00, 40, 'camilan'),
(30, 'Roti Maryam', 15000.00, 40, 'camilan');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `table_id` int NOT NULL,
  `status` enum('pending','in_progress','ready','served','paid') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `paid` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `table_id`, `status`, `created_at`, `paid`) VALUES
(15, 'sddasd', 3, 'paid', '2024-07-18 13:28:37', 1),
(16, 'random4', 4, 'paid', '2024-07-19 03:34:16', 1),
(17, 'hafidz', 4, 'paid', '2024-07-19 14:40:42', 1),
(18, 'tester1', 4, 'paid', '2024-07-21 02:33:48', 1),
(19, 'contoh 1', 4, 'paid', '2024-07-29 09:55:07', 1),
(20, 'EVQ', 4, 'paid', '2024-08-06 08:40:12', 1),
(21, 'tester1', 4, 'paid', '2024-08-06 08:42:58', 1),
(22, 'tester 2', 3, 'ready', '2024-08-06 08:43:24', 0),
(23, 'tester3', 4, 'pending', '2024-08-07 19:21:53', 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `menu_item_id` int NOT NULL,
  `quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`) VALUES
(16, 15, 1, 1),
(17, 16, 1, 1),
(18, 17, 7, 1),
(19, 17, 15, 1),
(20, 18, 1, 1),
(21, 18, 2, 1),
(22, 19, 1, 1),
(23, 19, 2, 1),
(24, 20, 7, 1),
(25, 21, 8, 1),
(26, 21, 14, 1),
(27, 22, 10, 1),
(28, 22, 20, 1),
(29, 22, 9, 1),
(30, 23, 10, 1),
(31, 23, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','credit_card','debit_card') COLLATE utf8mb4_general_ci NOT NULL,
  `payment_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `amount`, `payment_method`, `payment_time`) VALUES
(14, 15, 15000.00, 'cash', '2024-07-18 13:30:40'),
(15, 16, 15000.00, 'credit_card', '2024-07-19 03:34:46'),
(16, 17, 32000.00, 'cash', '2024-07-19 14:41:01'),
(17, 18, 22000.00, 'cash', '2024-07-21 02:34:46'),
(18, 19, 22000.00, 'cash', '2024-07-29 10:00:44'),
(19, 20, 15000.00, 'cash', '2024-08-06 08:42:11'),
(20, 21, 32000.00, 'debit_card', '2024-08-06 09:18:39');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` int NOT NULL,
  `capacity` int NOT NULL,
  `status` enum('available','occupied') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'available',
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `table_number` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `capacity`, `status`, `paid`, `table_number`) VALUES
(3, 8, 'available', 0, 2),
(4, 6, 'available', 0, 1),
(5, 6, 'available', 0, 3),
(6, 0, 'available', 0, 4),
(7, 0, 'available', 0, 5),
(8, 0, 'available', 0, 6);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('pelayan','koki','kasir','owner') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'username_pelayan', 'password_pelayan', 'pelayan'),
(2, 'chef_username', 'chef_password', 'koki'),
(4, 'kasir1', 'password_kasir', 'kasir'),
(5, 'hafidz', 'admin#1234', 'owner'),
(6, 'username_pelayan2', '3322b561151a183e0a063e0af8682557', 'pelayan'),
(7, 'username_pelayan3', 'e8e9c743f9f6a2a5812f69da51668a9c', 'pelayan'),
(8, 'chef_username2', '60b502fdcda1f86ae9bc014bc63cbe1e', 'koki');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `table_id` (`table_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
