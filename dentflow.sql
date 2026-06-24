-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
<<<<<<< HEAD
-- Generation Time: Jun 24, 2026 at 06:54 PM
=======
-- Generation Time: Jun 24, 2026 at 10:31 AM
>>>>>>> 7490bcb317bc45ffcc1d4c71cde188f4c06a8a7f
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
-- Database: `dentflow`
--

-- --------------------------------------------------------

--
<<<<<<< HEAD
=======
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `appt_date` date NOT NULL,
  `appt_time` time NOT NULL,
  `age_group` varchar(20) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `urgency` varchar(20) DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `service` varchar(100) DEFAULT NULL,
  `service_price` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `payment_status` varchar(20) DEFAULT NULL,
  `completed_date` date DEFAULT NULL,
  `booked_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_history`
--

CREATE TABLE `booking_history` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `appt_date` date NOT NULL,
  `appt_time` time NOT NULL,
  `age_group` varchar(20) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `urgency` varchar(20) DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `service` varchar(100) DEFAULT NULL,
  `service_price` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT NULL,
  `booked_at` datetime DEFAULT current_timestamp(),
  `completed_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
>>>>>>> 7490bcb317bc45ffcc1d4c71cde188f4c06a8a7f
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `sender` varchar(20) NOT NULL,
  `sender_email` varchar(100) DEFAULT NULL,
  `sender_name` varchar(100) DEFAULT NULL,
  `recipient` varchar(100) DEFAULT NULL,
  `message_text` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

<<<<<<< HEAD
=======
-- --------------------------------------------------------

--
-- Table structure for table `dental_services`
--

CREATE TABLE `dental_services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dental_services`
--

INSERT INTO `dental_services` (`id`, `name`, `description`, `price`) VALUES
(1, 'Teeth Cleaning', 'Professional dental cleaning and polishing', 80.00),
(2, 'Tooth Extraction', 'Surgical removal of damaged teeth', 150.00),
(3, 'Root Canal Therapy', 'Endodontic treatment for infected teeth', 350.00),
(4, 'Dental Crowns', 'Custom fitted crowns for damaged teeth', 500.00),
(5, 'Teeth Whitening', 'Professional teeth whitening treatment', 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `item` varchar(100) NOT NULL,
  `status` varchar(20) DEFAULT 'Out of Stock',
  `price` decimal(10,2) DEFAULT 0.00,
  `quantity` int(11) DEFAULT 0,
  `low_stock_limit` int(11) DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `item`, `status`, `price`, `quantity`, `low_stock_limit`) VALUES
(1, 'Dental Composite Syringes', 'In Stock', 45.00, 50, 10),
(2, 'Local Anesthetic Carpules', 'In Stock', 12.50, 100, 20),
(3, 'Sterilization Pouches', 'Out of Stock', 8.75, 0, 15),
(4, 'Prophy Paste Cups', 'In Stock', 5.25, 200, 30);

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Vince', 'vince@gmail.com', '1234_ABC', '2026-06-24 16:19:38');

>>>>>>> 7490bcb317bc45ffcc1d4c71cde188f4c06a8a7f
--
-- Indexes for dumped tables
--

--
<<<<<<< HEAD
=======
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `booking_history`
--
ALTER TABLE `booking_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
>>>>>>> 7490bcb317bc45ffcc1d4c71cde188f4c06a8a7f
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
<<<<<<< HEAD
=======
-- Indexes for table `dental_services`
--
ALTER TABLE `dental_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
>>>>>>> 7490bcb317bc45ffcc1d4c71cde188f4c06a8a7f
-- AUTO_INCREMENT for dumped tables
--

--
<<<<<<< HEAD
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
=======
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `booking_history`
--
ALTER TABLE `booking_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_services`
--
ALTER TABLE `dental_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_history`
--
ALTER TABLE `booking_history`
  ADD CONSTRAINT `booking_history_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_history_ibfk_2` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;
>>>>>>> 7490bcb317bc45ffcc1d4c71cde188f4c06a8a7f
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
