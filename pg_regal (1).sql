-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2025 at 07:35 PM
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
-- Database: `pg_regal`
--

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `email` varchar(500) NOT NULL,
  `password` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`email`, `password`) VALUES
('admin@gmail.com', 'password');

-- --------------------------------------------------------

--
-- Table structure for table `onwer_room_template`
--

CREATE TABLE `onwer_room_template` (
  `id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `bulding_name` varchar(500) NOT NULL,
  `bulding_photo` varchar(500) NOT NULL,
  `bio` varchar(1000) NOT NULL,
  `email` varchar(500) NOT NULL,
  `rooms_photo` varchar(500) NOT NULL,
  `room_type` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `onwer_room_template`
--

INSERT INTO `onwer_room_template` (`id`, `name`, `bulding_name`, `bulding_photo`, `bio`, `email`, `rooms_photo`, `room_type`) VALUES
(6, 'Ankush Dhupe', 'Lotus Aprtment 1', 'building_68e3d7851952e7.77271200.png', 'Testing Address', 'ankushdhupe2017@gmail.com', 'room_68e1a98a006719.70019400.jpg,room_68e1a98a007888.20480024.jpg,room_68e1a98a0098c6.24769104.jpg', ''),
(7, 'Testing 2', 'Ganesh Apartment 1', 'building_68e3d80c5fbcd7.73825207.png', 'Ganesh Apartment', 'kojamib485@mv6a.com', 'room_68e3d7f579c545.32523571.png,room_68e3d7f57a7775.25871058.png,room_68e3d7f57aacd4.51889144.png,room_68e3d7f57aede8.62052820.png,room_68e3d7f57b25e6.12392681.png,room_68e3d7f57b5c59.31784938.png,room_68e3d7f57b8ca5.45904147.png,room_68e3d7f57bcc19.52326555.png,room_68e3d7f57c3941.24808509.png,room_68e3d7f57c8704.77315969.png,room_68e3d7f57ce533.07104744.png,room_68e3d7f57d6608.16627996.png,room_68e3d7f57dcef3.90151353.png,room_68e3d7f57e10e2.98899329.png,room_68e3d7f57e6bc5.29742830.png', ''),
(8, 'demo template', 'demo template', 'building_68e3c991299b11.02619110.png', 'demo template', 'ankushdhupe2017@gmail.com', '', 'male'),
(9, 'pankaj mohan', 'Mohan House', 'building_68e530386e4334.33839327.jpg', 'mohan house near gut no 17 bajajnagar  , chh. sambhaji nagr', 'ankushdhupe2017@gmail.com', '', 'Both');

-- --------------------------------------------------------

--
-- Table structure for table `otp_verification`
--

CREATE TABLE `otp_verification` (
  `id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `email` varchar(500) NOT NULL,
  `otp` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `owner_registration`
--

CREATE TABLE `owner_registration` (
  `id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `email` varchar(500) NOT NULL,
  `mobile` bigint(20) NOT NULL,
  `address` varchar(500) NOT NULL,
  `password` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `owner_registration`
--

INSERT INTO `owner_registration` (`id`, `name`, `email`, `mobile`, `address`, `password`) VALUES
(5, 'Ankush', 'ankushdhupe2017@gmail.com', 9359927134, 'testing 1', 'Ankush@123'),
(7, 'testing 2', 'kojamib485@mv6a.com', 9658745265, 'testing 2', 'Ankush@123'),
(8, 'pankaj mohan', 'vegir55092@fintehs.com', 9849848945, 'dertghdrfgbdsf', 'pankaj@123'),
(9, 'Sanket jadhav', 'covod73459@erynka.com', 9956565652, 'beyufwefuwgbfisdbfsiyuovfbugfv', 'sanket@123');

-- --------------------------------------------------------

--
-- Table structure for table `room_request`
--

CREATE TABLE `room_request` (
  `id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `email` varchar(500) NOT NULL,
  `mobile` bigint(20) NOT NULL,
  `status` varchar(500) NOT NULL,
  `onwer_name` varchar(500) NOT NULL,
  `onwer_email` varchar(500) NOT NULL,
  `bulding_name` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_request`
--

INSERT INTO `room_request` (`id`, `name`, `email`, `mobile`, `status`, `onwer_name`, `onwer_email`, `bulding_name`) VALUES
(2, 'Ankush ', 'ankushdhupe2017@gmail.com', 9359927134, 'accepted', 'Ankush Dhupe', 'ankushdhupe2017@gmail.com', 'Lotus Aprtment 1'),
(3, 'Ankush ', 'ankushdhupe2017@gmail.com', 9359927134, 'rejected', 'Testing 2', 'kojamib485@mv6a.com', 'Ganesh Apartment 1'),
(4, 'Ankush dhupe', 'vosin30642@fintehs.com', 6454154649, 'accepted', 'Ankush Dhupe', 'ankushdhupe2017@gmail.com', 'Lotus Aprtment 1'),
(5, 'Ankush dhupe', 'vosin30642@fintehs.com', 6454154649, 'requesting', 'Testing 2', 'kojamib485@mv6a.com', 'Ganesh Apartment 1');

-- --------------------------------------------------------

--
-- Table structure for table `user_registration`
--

CREATE TABLE `user_registration` (
  `id` int(11) NOT NULL,
  `name` varchar(500) DEFAULT NULL,
  `email` varchar(500) NOT NULL,
  `mobile` bigint(20) NOT NULL,
  `gender` varchar(500) NOT NULL,
  `password` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_registration`
--

INSERT INTO `user_registration` (`id`, `name`, `email`, `mobile`, `gender`, `password`) VALUES
(1, 'Ankush ', 'ankushdhupe2017@gmail.com', 9359927134, 'male', 'Ankush@123'),
(2, 'testing', 'raxiwed175@erynka.com', 6598985455, 'male', 'testing@1'),
(3, 'Ankush dhupe', 'vosin30642@fintehs.com', 6454154649, 'female', 'Testing@3'),
(4, 'tesitng finalk 1', 'xetita5793@fintehs.com', 9846465456, 'male', 'testing@123'),
(5, 'Anita Dhut', 'celimat871@erynka.com', 9846556415, 'male', 'Anita@123'),
(6, 'Sanket jadhav', 'xebil99788@erynka.com', 9865145694, 'male', 'sanket@123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `onwer_room_template`
--
ALTER TABLE `onwer_room_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otp_verification`
--
ALTER TABLE `otp_verification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `owner_registration`
--
ALTER TABLE `owner_registration`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_request`
--
ALTER TABLE `room_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_registration`
--
ALTER TABLE `user_registration`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `onwer_room_template`
--
ALTER TABLE `onwer_room_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `otp_verification`
--
ALTER TABLE `otp_verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `owner_registration`
--
ALTER TABLE `owner_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `room_request`
--
ALTER TABLE `room_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_registration`
--
ALTER TABLE `user_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
