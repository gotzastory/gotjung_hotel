-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 27, 2025 at 07:11 PM
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
-- Database: `hotel_bookings`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin'),
(2, 'gotchan', 'tnp@0503');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id_booking` int(11) NOT NULL,
  `id_rooms` int(11) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('pending','paid') DEFAULT 'pending',
  `id_customers` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `nights` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_slip` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id_booking`, `id_rooms`, `booking_date`, `payment_status`, `id_customers`, `check_in`, `check_out`, `nights`, `total_price`, `payment_slip`) VALUES
(6, 2, '2025-01-26 14:51:08', 'paid', 2, '2025-01-26', '2025-01-27', 1, 1.00, '473761340_1639070366708399_9173598766972158309_n.jpg'),
(8, 2, '2025-01-26 16:56:25', 'paid', 6, '2025-01-26', '2025-01-27', 1, 1.00, '473761340_1639070366708399_9173598766972158309_n.jpg'),
(11, 1, '2025-01-27 07:53:23', 'paid', 8, '2025-01-27', '2025-01-28', 1, 1000.00, '473761340_1639070366708399_9173598766972158309_n.jpg'),
(12, 1, '2025-01-27 17:36:23', 'paid', 1, '2025-01-28', '2025-01-29', 1, 1000.00, '473761340_1639070366708399_9173598766972158309_n.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id_contacts` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `contact_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id_contacts`, `full_name`, `email`, `phone_number`, `message`, `contact_date`) VALUES
(4, 'สมเด็จวัว', 'cowcat@gmail.com', 'xxx', 'เหมี๊ยว', '2025-01-13 14:27:38'),
(5, 'สมเด็จวัว', 'cowcat@gmail.com', 'xxx', 'เหมี๊ยว', '2025-01-13 15:24:07'),
(6, 'สมเด็จวัว', 'cowcat@gmail.com', 'xxx', 'เหมี๊ยว', '2025-01-13 15:24:14'),
(7, 'สมเด็จวัว', 'cowcat@gmail.com', 'xxx', 'เหมี๊ยว', '2025-01-13 15:24:31'),
(8, 'สมเด็จวัว', 'cowcat@gmail.com', 'xxx', 'เหมี๊ยว', '2025-01-13 15:45:24'),
(9, 'สมเด็จวัว', 'cowcat@gmail.com', 'xxx', 'มอร์', '2025-01-13 16:05:19'),
(10, 'สมเด็จวัว', 'cowcat@gmail.com', 'xxx', 'มอร์', '2025-01-13 16:06:02'),
(11, 'เมจิ', 'meji@gmail.com', '0918457813', 'ก็อตเม', '2025-01-21 16:08:59'),
(12, 'เมจิ', 'meji@gmail.com', '0918457813', 'ก็อตจ้าาา', '2025-01-22 08:22:33'),
(13, 'ครูแตง', 'krutang@gmail.com', '080000000', 'สวัสดีครับ', '2025-01-27 07:49:28');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id_customers` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id_customers`, `fullname`, `email`, `phone`, `password`, `created_at`) VALUES
(1, 'got', 'got@gmail.com', 'xxx', '1234', '2025-01-06 14:29:42'),
(2, 'tnp', 'tnp@gmail.com', 'xxx', '0503', '2025-01-06 14:29:53'),
(3, 'gotchan', 'gotchan@gmail.com', 'xxx', '1234', '2025-01-06 14:49:20'),
(4, 'ggg', 'ggg@gmail.com', 'xxx', '1234', '2025-01-06 16:29:17'),
(6, 'kanyewest', 'ye@gmail.com', '0999999999', 'ye', '2025-01-26 16:55:43'),
(7, 'เมจิคนฉ๋วยยย', 'meji@gmail.com', '0918457813', 'meji', '2025-01-26 17:42:33'),
(8, 'ครูแตง', 'krutang@gmail.com', '0800000000', '1234', '2025-01-27 07:48:07');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id_reviews` int(11) NOT NULL,
  `id_booking` int(11) NOT NULL,
  `id_customers` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id_rooms` int(11) NOT NULL,
  `name_rooms` varchar(100) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(200) NOT NULL,
  `gallery_image1` varchar(255) DEFAULT NULL,
  `gallery_image2` varchar(255) DEFAULT NULL,
  `gallery_image3` varchar(255) DEFAULT NULL,
  `gallery_image4` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `type_rooms` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id_rooms`, `name_rooms`, `price`, `image`, `gallery_image1`, `gallery_image2`, `gallery_image3`, `gallery_image4`, `description`, `amenities`, `type_rooms`) VALUES
(1, 'Sweet Room', 1000.00, '351048947694.jpg', '351048947694_g1.jpg', '351048947694_g2.jpg', '351048947694_g3.jpg', '351048947694_g4.jpg', 'ห้องนอนชั้นดี', 'Swimming Pool, Fitness Center, Restaurant, Spa', 'Standard'),
(2, 'ยำรวมมิตร', 1.00, '470867612_2611776015672804_4066184825703543605_n.jpg', 'IMG_2561-scaled_g1.jpg', 'IMG_8892 ปรับโทน_g2.jpg', '471679039_540825558960521_357756192479994834_n_g3.jpg', '471874716_567960679476445_5768628145487853768_n_g4.jpg', 'test เฉยๆครับ', 'Swimming Pool, Fitness Center, Restaurant, Spa', 'Standard'),
(101, 'Deluxe Room', 2000.00, '', '', '', '', '', 'sdasdsadasdas', 'Swimming Pool, Restaurant', 'Deluxe'),
(102, 'ห้องนอน', 1.00, 'luxurious-bedroom-pastel-colours-neoclassical-style-with-large-bed-dressing-table-with-tv-unit-3d-render-733x550.jpg', 'luxurious-bedroom-pastel-colours-neoclassical-style-with-large-bed-dressing-table-with-tv-unit-3d-render-733x550_g1.jpg', '1e3c0f8252f6463b86c11fea35ca74f7_g2.jpg', '351048947694_g3.jpg', '259ff3b1-f7c8-47d2-9abf-4e7957760d8d_g4.jpg', 'นอนอย่างเดียว', 'Swimming Pool, Fitness Center, Restaurant, Spa', 'Suite');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id_booking`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id_contacts`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id_customers`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id_reviews`),
  ADD KEY `id_booking` (`id_booking`),
  ADD KEY `id_customers` (`id_customers`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id_rooms`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id_booking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id_contacts` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id_customers` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id_reviews` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id_rooms` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`id_customers`) REFERENCES `customers` (`id_customers`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
