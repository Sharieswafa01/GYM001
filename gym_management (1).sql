-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 31, 2025 at 01:47 PM
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
-- Database: `gym_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('SuperAdmin','Manager') DEFAULT 'Manager',
  `last_login` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `email`, `password`, `role`, `last_login`, `profile_picture`, `full_name`, `username`, `title`, `otp`, `otp_expiry`) VALUES
(1, 'admin@example.com', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'Manager', '2025-04-02 02:44:53', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'admin@gmail.com', '$2y$10$ebnugdZKwNHf1RxuFkZnW.9Jkz/IxwKRyjtfm0pO0okfOHNTL4/km', '', '2025-05-06 11:45:43', 'admin_68562a198e2223.82900778.jpg', 'Sharies Esoto', 'shxriez', 'ADMIN', '855018', '2025-08-28 11:29:57');

-- --------------------------------------------------------

--
-- Table structure for table `alerts_log`
--

CREATE TABLE `alerts_log` (
  `id` int(11) NOT NULL,
  `user_membership_id` int(11) NOT NULL,
  `alert_sent_at` datetime DEFAULT current_timestamp(),
  `message` text DEFAULT NULL,
  `via` enum('Email','SMS','Both') DEFAULT 'Email'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `message`, `created_at`) VALUES
(1, 'Gym Closure Notice', 'We would like to inform all members that the gym will be closed tomorrow. Please plan your workouts accordingly. Regular operations will resume the following day. Thank you for your understanding and cooperation.', '2025-05-17 16:03:01');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('Login','Logout') NOT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `day` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `status`, `login_time`, `logout_time`, `timestamp`, `day`, `month`, `year`) VALUES
(1, 2, 'Logout', '2025-05-14 11:49:11', '2025-05-14 11:49:27', '2025-05-14 03:49:11', NULL, NULL, NULL),
(6, 1, 'Login', '2025-05-14 12:48:18', NULL, '2025-05-14 04:48:18', NULL, NULL, NULL),
(7, 2, 'Logout', '2025-05-14 13:13:35', '2025-05-14 15:01:28', '2025-05-14 05:13:35', NULL, NULL, NULL),
(8, 1, 'Logout', '2025-05-14 15:24:58', '2025-05-14 15:25:04', '2025-05-14 07:24:58', NULL, NULL, NULL),
(9, 2, 'Login', '2025-05-17 10:55:38', NULL, '2025-05-17 02:55:38', NULL, NULL, NULL),
(11, 5, 'Logout', '2025-05-17 16:25:50', '2025-05-17 16:33:28', '2025-05-17 08:25:50', NULL, NULL, NULL),
(13, 2, 'Login', '2025-05-17 16:34:25', NULL, '2025-05-17 08:34:25', NULL, NULL, NULL),
(15, 2, 'Logout', '2025-05-17 16:35:53', '2025-05-17 16:35:59', '2025-05-17 08:35:53', NULL, NULL, NULL),
(16, 2, 'Logout', '2025-05-17 16:37:06', '2025-05-17 21:45:48', '2025-05-17 08:37:06', NULL, NULL, NULL),
(18, 2, 'Logout', '2025-05-17 17:00:13', '2025-05-17 21:45:40', '2025-05-17 09:00:13', NULL, NULL, NULL),
(20, 2, 'Logout', '2025-05-17 21:18:28', '2025-05-17 21:45:33', '2025-05-17 13:18:28', NULL, NULL, NULL),
(22, 2, 'Logout', '2025-05-17 21:19:18', '2025-05-17 21:20:03', '2025-05-17 13:19:18', NULL, NULL, NULL),
(23, 2, 'Logout', '2025-05-17 21:28:11', '2025-05-17 21:28:36', '2025-05-17 13:28:11', NULL, NULL, NULL),
(24, 2, 'Logout', '2025-05-17 21:35:35', '2025-05-17 21:35:48', '2025-05-17 13:35:35', NULL, NULL, NULL),
(25, 2, 'Logout', '2025-05-17 21:37:23', '2025-05-17 21:37:37', '2025-05-17 13:37:23', NULL, NULL, NULL),
(26, 2, 'Logout', '2025-05-17 21:38:00', '2025-05-17 21:38:05', '2025-05-17 13:38:00', NULL, NULL, NULL),
(27, 2, 'Logout', '2025-05-17 21:44:10', '2025-05-17 21:44:38', '2025-05-17 13:44:10', NULL, NULL, NULL),
(30, 2, 'Logout', '2025-05-17 21:46:20', '2025-05-17 21:46:24', '2025-05-17 13:46:20', NULL, NULL, NULL),
(32, 2, 'Logout', '2025-05-17 21:47:27', '2025-05-17 21:48:02', '2025-05-17 13:47:27', NULL, NULL, NULL),
(34, 2, 'Login', '2025-05-17 21:54:23', NULL, '2025-05-17 13:54:23', NULL, NULL, NULL),
(36, 2, 'Login', '2025-05-17 21:54:57', NULL, '2025-05-17 13:54:57', NULL, NULL, NULL),
(38, 2, 'Logout', '2025-05-17 21:55:07', '2025-05-17 21:55:35', '2025-05-17 13:55:07', NULL, NULL, NULL),
(39, 2, 'Login', '2025-05-17 21:59:01', NULL, '2025-05-17 13:59:01', NULL, NULL, NULL),
(41, 2, 'Logout', '2025-05-17 22:03:10', '2025-05-17 22:03:35', '2025-05-17 14:03:10', NULL, NULL, NULL),
(43, 2, 'Logout', '2025-05-17 22:08:44', '2025-05-17 22:09:00', '2025-05-17 14:08:44', NULL, NULL, NULL),
(46, 2, 'Login', '2025-05-17 22:10:53', NULL, '2025-05-17 14:10:53', NULL, NULL, NULL),
(48, 2, 'Login', '2025-05-17 22:11:08', NULL, '2025-05-17 14:11:08', NULL, NULL, NULL),
(50, 2, 'Login', '2025-05-17 22:13:56', NULL, '2025-05-17 14:13:56', NULL, NULL, NULL),
(52, 2, 'Logout', '2025-05-17 22:14:15', '2025-05-17 22:14:28', '2025-05-17 14:14:15', NULL, NULL, NULL),
(54, 2, 'Login', '2025-05-17 22:15:22', NULL, '2025-05-17 14:15:22', NULL, NULL, NULL),
(56, 2, 'Login', '2025-05-17 22:15:32', NULL, '2025-05-17 14:15:32', NULL, NULL, NULL),
(58, 2, 'Logout', '2025-05-18 17:11:54', '2025-05-20 15:01:36', '2025-05-18 09:11:54', NULL, NULL, NULL),
(60, 2, 'Logout', '2025-05-20 14:40:03', '2025-05-20 14:59:47', '2025-05-20 06:40:03', NULL, NULL, NULL),
(62, 2, 'Logout', '2025-05-20 14:43:13', '2025-05-20 14:43:39', '2025-05-20 06:43:13', NULL, NULL, NULL),
(63, 2, 'Logout', '2025-05-20 14:59:16', '2025-05-20 14:59:42', '2025-05-20 06:59:16', NULL, NULL, NULL),
(66, 2, 'Logout', '2025-05-20 15:03:37', '2025-05-20 15:07:38', '2025-05-20 07:03:37', NULL, NULL, NULL),
(67, 2, 'Login', '2025-05-20 15:07:48', NULL, '2025-05-20 07:07:48', NULL, NULL, NULL),
(69, 2, 'Login', '2025-05-20 15:08:06', NULL, '2025-05-20 07:08:06', NULL, NULL, NULL),
(70, 2, 'Logout', '2025-05-20 15:09:23', '2025-05-20 15:09:28', '2025-05-20 07:09:23', NULL, NULL, NULL),
(71, 2, 'Logout', '2025-05-20 15:10:23', '2025-05-20 15:15:31', '2025-05-20 07:10:23', NULL, NULL, NULL),
(72, 2, 'Logout', '2025-05-20 15:16:04', '2025-05-20 15:16:11', '2025-05-20 07:16:04', NULL, NULL, NULL),
(73, 2, 'Logout', '2025-05-20 15:27:51', '2025-05-20 15:27:57', '2025-05-20 07:27:51', NULL, NULL, NULL),
(75, 2, 'Login', '2025-05-20 15:28:20', NULL, '2025-05-20 07:28:20', NULL, NULL, NULL),
(76, 2, 'Logout', '2025-05-20 15:34:38', '2025-05-20 15:35:00', '2025-05-20 07:34:38', NULL, NULL, NULL),
(78, 2, 'Logout', '2025-05-20 15:39:41', '2025-05-20 15:39:50', '2025-05-20 07:39:41', NULL, NULL, NULL),
(79, 2, 'Logout', '2025-05-20 15:40:02', '2025-05-20 15:41:01', '2025-05-20 07:40:02', NULL, NULL, NULL),
(80, 2, 'Logout', '2025-05-20 15:42:30', '2025-05-20 15:42:36', '2025-05-20 07:42:30', NULL, NULL, NULL),
(81, 2, 'Logout', '2025-05-20 15:46:34', '2025-05-20 15:46:42', '2025-05-20 07:46:34', NULL, NULL, NULL),
(82, 2, 'Logout', '2025-05-20 15:46:59', '2025-05-20 15:47:07', '2025-05-20 07:46:59', NULL, NULL, NULL),
(84, 2, 'Logout', '2025-05-20 15:47:24', '2025-05-20 15:48:59', '2025-05-20 07:47:24', NULL, NULL, NULL),
(85, 2, 'Logout', '2025-05-20 15:49:11', '2025-05-20 15:50:04', '2025-05-20 07:49:11', NULL, NULL, NULL),
(87, 2, 'Logout', '2025-05-20 15:50:29', '2025-05-20 15:51:22', '2025-05-20 07:50:29', NULL, NULL, NULL),
(89, 2, 'Logout', '2025-05-20 15:51:58', '2025-05-20 15:52:03', '2025-05-20 07:51:58', NULL, NULL, NULL),
(90, 2, 'Login', '2025-05-20 15:52:10', NULL, '2025-05-20 07:52:10', NULL, NULL, NULL),
(92, 2, 'Login', '2025-05-20 15:52:17', NULL, '2025-05-20 07:52:17', NULL, NULL, NULL),
(94, 2, 'Logout', '2025-05-20 15:54:17', '2025-05-20 15:56:09', '2025-05-20 07:54:17', NULL, NULL, NULL),
(96, 2, 'Logout', '2025-05-20 15:56:39', '2025-05-20 15:56:43', '2025-05-20 07:56:39', NULL, NULL, NULL),
(98, 2, 'Logout', '2025-05-20 15:59:37', '2025-05-20 16:00:39', '2025-05-20 07:59:37', NULL, NULL, NULL),
(102, 2, 'Login', '2025-05-20 16:06:22', NULL, '2025-05-20 08:06:22', NULL, NULL, NULL),
(103, 2, 'Login', '2025-05-20 23:17:22', NULL, '2025-05-20 15:17:22', NULL, NULL, NULL),
(106, 2, 'Logout', '2025-05-20 23:19:00', '2025-05-20 23:19:34', '2025-05-20 15:19:00', NULL, NULL, NULL),
(107, 2, 'Logout', '2025-05-20 23:19:43', '2025-05-20 23:20:46', '2025-05-20 15:19:43', NULL, NULL, NULL),
(112, 2, 'Logout', '2025-05-20 23:21:31', '2025-05-20 23:21:42', '2025-05-20 15:21:31', NULL, NULL, NULL),
(114, 2, 'Login', '2025-05-20 23:23:44', NULL, '2025-05-20 15:23:44', NULL, NULL, NULL),
(116, 2, 'Logout', '2025-05-20 23:25:51', '2025-05-20 23:33:42', '2025-05-20 15:25:51', NULL, NULL, NULL),
(118, 2, 'Logout', '2025-05-20 23:44:16', '2025-05-20 23:49:39', '2025-05-20 15:44:16', NULL, NULL, NULL),
(119, 2, 'Logout', '2025-05-20 23:49:55', '2025-05-20 23:50:03', '2025-05-20 15:49:55', NULL, NULL, NULL),
(120, 2, 'Logout', '2025-05-20 23:50:18', '2025-05-20 23:50:24', '2025-05-20 15:50:18', NULL, NULL, NULL),
(122, 2, 'Logout', '2025-05-20 23:51:10', '2025-05-20 23:54:52', '2025-05-20 15:51:10', NULL, NULL, NULL),
(124, 2, 'Logout', '2025-05-20 23:55:22', '2025-05-20 23:59:27', '2025-05-20 15:55:22', NULL, NULL, NULL),
(126, 2, 'Logout', '2025-05-20 23:59:41', '2025-05-21 10:13:15', '2025-05-20 15:59:41', NULL, NULL, NULL),
(127, 2, 'Logout', '2025-05-21 10:13:21', '2025-05-21 10:20:32', '2025-05-21 02:13:21', NULL, NULL, NULL),
(132, 1, 'Login', '2025-05-21 10:21:06', NULL, '2025-05-21 02:21:06', NULL, NULL, NULL),
(134, 2, 'Login', '2025-05-21 10:24:01', NULL, '2025-05-21 02:24:01', NULL, NULL, NULL),
(136, 1, 'Login', '2025-05-21 10:28:33', NULL, '2025-05-21 02:28:33', NULL, NULL, NULL),
(137, 1, 'Logout', '2025-05-21 10:30:30', '2025-05-21 10:33:15', '2025-05-21 02:30:30', NULL, NULL, NULL),
(138, 2, 'Logout', '2025-05-21 10:30:36', '2025-05-21 10:54:47', '2025-05-21 02:30:36', NULL, NULL, NULL),
(139, 2, 'Logout', '2025-05-21 10:33:32', '2025-05-21 10:35:03', '2025-05-21 02:33:32', NULL, NULL, NULL),
(140, 2, 'Logout', '2025-05-21 10:35:19', '2025-05-21 10:37:21', '2025-05-21 02:35:19', NULL, NULL, NULL),
(141, 2, 'Logout', '2025-05-21 10:38:29', '2025-05-21 10:38:43', '2025-05-21 02:38:29', NULL, NULL, NULL),
(142, 2, 'Logout', '2025-05-21 10:42:19', '2025-05-21 10:42:27', '2025-05-21 02:42:19', NULL, NULL, NULL),
(143, 2, 'Logout', '2025-05-21 10:46:17', '2025-05-21 10:46:23', '2025-05-21 02:46:17', NULL, NULL, NULL),
(144, 1, 'Login', '2025-05-21 10:50:42', NULL, '2025-05-21 02:50:42', NULL, NULL, NULL),
(147, 1, 'Login', '2025-05-21 10:55:38', NULL, '2025-05-21 02:55:38', NULL, NULL, NULL),
(149, 2, 'Login', '2025-05-21 10:56:38', NULL, '2025-05-21 02:56:38', NULL, NULL, NULL),
(150, 2, 'Logout', '2025-05-21 10:56:58', '2025-05-21 10:57:48', '2025-05-21 02:56:58', NULL, NULL, NULL),
(152, 2, 'Logout', '2025-05-21 11:01:29', '2025-05-21 11:10:41', '2025-05-21 03:01:29', NULL, NULL, NULL),
(153, 1, 'Logout', '2025-05-21 11:06:04', '2025-05-21 11:06:14', '2025-05-21 03:06:04', NULL, NULL, NULL),
(154, 1, 'Logout', '2025-05-21 11:06:22', '2025-05-21 11:11:06', '2025-05-21 03:06:22', NULL, NULL, NULL),
(156, 1, 'Logout', '2025-05-21 11:11:36', '2025-05-21 11:13:18', '2025-05-21 03:11:36', NULL, NULL, NULL),
(157, 1, 'Logout', '2025-05-21 11:13:43', '2025-05-21 11:13:57', '2025-05-21 03:13:43', NULL, NULL, NULL),
(158, 2, 'Logout', '2025-05-21 11:13:53', '2025-05-21 11:18:26', '2025-05-21 03:13:53', NULL, NULL, NULL),
(159, 1, 'Logout', '2025-05-21 11:18:06', '2025-05-21 11:25:21', '2025-05-21 03:18:06', NULL, NULL, NULL),
(163, 2, 'Logout', '2025-05-21 11:23:32', '2025-05-21 11:24:16', '2025-05-21 03:23:32', NULL, NULL, NULL),
(164, 2, 'Logout', '2025-05-21 11:25:26', '2025-05-21 11:28:54', '2025-05-21 03:25:26', NULL, NULL, NULL),
(165, 1, 'Login', '2025-05-21 11:29:08', NULL, '2025-05-21 03:29:08', NULL, NULL, NULL),
(167, 2, 'Logout', '2025-05-21 11:36:17', '2025-05-21 11:37:23', '2025-05-21 03:36:17', NULL, NULL, NULL),
(168, 1, 'Login', '2025-05-21 11:37:39', NULL, '2025-05-21 03:37:39', NULL, NULL, NULL),
(170, 1, 'Login', '2025-05-21 11:41:09', NULL, '2025-05-21 03:41:09', NULL, NULL, NULL),
(171, 2, 'Logout', '2025-05-21 11:42:03', '2025-05-21 11:53:28', '2025-05-21 03:42:03', NULL, NULL, NULL),
(172, 1, 'Logout', '2025-05-21 11:53:49', '2025-05-21 12:04:02', '2025-05-21 03:53:49', NULL, NULL, NULL),
(173, 2, 'Logout', '2025-05-21 11:56:15', '2025-05-21 12:00:12', '2025-05-21 03:56:15', NULL, NULL, NULL),
(177, 2, 'Logout', '2025-05-21 12:03:39', '2025-05-21 12:05:06', '2025-05-21 04:03:39', NULL, NULL, NULL),
(180, 1, 'Logout', '2025-05-21 12:09:51', '2025-05-21 12:13:38', '2025-05-21 04:09:51', NULL, NULL, NULL),
(181, 2, 'Logout', '2025-05-21 12:10:01', '2025-05-21 12:11:41', '2025-05-21 04:10:01', NULL, NULL, NULL),
(182, 5, 'Logout', '2025-05-21 12:10:34', '2025-05-21 12:13:16', '2025-05-21 04:10:34', NULL, NULL, NULL),
(184, 2, 'Logout', '2025-05-21 12:29:19', '2025-05-21 12:30:22', '2025-05-21 04:29:19', NULL, NULL, NULL),
(185, 1, 'Login', '2025-05-21 12:29:32', NULL, '2025-05-21 04:29:32', NULL, NULL, NULL),
(187, 2, 'Login', '2025-05-21 12:31:08', NULL, '2025-05-21 04:31:08', NULL, NULL, NULL),
(188, 2, 'Logout', '2025-05-22 10:48:47', '2025-05-22 10:52:12', '2025-05-22 02:48:47', NULL, NULL, NULL),
(189, 1, 'Login', '2025-05-22 10:49:38', NULL, '2025-05-22 02:49:38', NULL, NULL, NULL),
(192, 2, 'Login', '2025-05-25 10:15:50', NULL, '2025-05-25 02:15:50', NULL, NULL, NULL),
(193, 2, 'Logout', '2025-05-26 22:20:59', '2025-05-26 22:29:51', '2025-05-26 14:20:59', NULL, NULL, NULL),
(195, 2, 'Login', '2025-05-26 22:42:46', NULL, '2025-05-26 14:42:46', NULL, NULL, NULL),
(196, 2, 'Login', '2025-05-27 11:12:43', NULL, '2025-05-27 03:12:43', NULL, NULL, NULL),
(198, 2, 'Login', '2025-06-17 13:39:29', NULL, '2025-06-17 05:39:29', NULL, NULL, NULL),
(199, 2, 'Login', '2025-06-17 14:39:20', NULL, '2025-06-17 06:39:20', NULL, NULL, NULL),
(200, 2, 'Logout', '2025-06-18 13:05:12', '2025-06-18 13:47:00', '2025-06-18 05:05:12', NULL, NULL, NULL),
(201, 11, 'Login', '2025-06-19 15:53:04', NULL, '2025-06-19 07:53:04', NULL, NULL, NULL),
(202, 2, 'Logout', '2025-06-20 13:54:54', '2025-06-20 15:04:01', '2025-06-20 05:54:54', NULL, NULL, NULL),
(203, 11, 'Logout', '2025-06-20 22:10:26', '2025-06-20 22:10:54', '2025-06-20 14:10:26', NULL, NULL, NULL),
(204, 5, 'Logout', '2025-06-20 22:10:41', '2025-06-20 22:12:12', '2025-06-20 14:10:41', NULL, NULL, NULL),
(205, 1, 'Login', '2025-06-20 22:11:07', NULL, '2025-06-20 14:11:07', NULL, NULL, NULL),
(206, 11, 'Login', '2025-06-20 22:31:49', NULL, '2025-06-20 14:31:49', NULL, NULL, NULL),
(207, 2, 'Login', '2025-06-20 23:15:19', NULL, '2025-06-20 15:15:19', NULL, NULL, NULL),
(208, 11, 'Logout', '2025-06-20 23:58:24', '2025-06-20 23:58:43', '2025-06-20 15:58:24', NULL, NULL, NULL),
(209, 2, 'Logout', '2025-06-20 23:59:13', '2025-06-20 23:59:34', '2025-06-20 15:59:13', NULL, NULL, NULL),
(210, 11, 'Login', '2025-06-20 23:59:24', NULL, '2025-06-20 15:59:24', NULL, NULL, NULL),
(211, 2, 'Logout', '2025-06-21 00:03:26', '2025-06-21 11:27:05', '2025-06-20 16:03:26', NULL, NULL, NULL),
(212, 2, 'Login', '2025-06-21 12:27:32', NULL, '2025-06-21 04:27:32', NULL, NULL, NULL),
(213, 2, 'Login', '2025-07-02 09:16:02', NULL, '2025-07-02 01:16:02', NULL, NULL, NULL),
(214, 12, 'Login', '2025-07-02 10:26:14', NULL, '2025-07-02 02:26:14', NULL, NULL, NULL),
(215, 2, 'Login', '2025-07-02 14:36:12', NULL, '2025-07-02 06:36:12', NULL, NULL, NULL),
(216, 22, 'Logout', '2025-08-07 11:38:57', '2025-08-07 11:39:10', '2025-08-07 03:38:57', NULL, NULL, NULL),
(217, 22, 'Login', '2025-08-07 11:39:16', NULL, '2025-08-07 03:39:16', NULL, NULL, NULL),
(218, 21, 'Login', '2025-08-07 11:39:30', NULL, '2025-08-07 03:39:30', NULL, NULL, NULL),
(219, 19, 'Login', '2025-08-07 11:40:05', NULL, '2025-08-07 03:40:05', NULL, NULL, NULL),
(220, 2, 'Logout', '2025-08-07 14:12:23', '2025-08-07 14:44:25', '2025-08-07 06:12:23', NULL, NULL, NULL),
(221, 22, 'Logout', '2025-08-07 14:20:39', '2025-08-07 14:46:23', '2025-08-07 06:20:39', 7, 8, 2025),
(222, 1, 'Login', '2025-08-07 14:38:13', NULL, '2025-08-07 06:38:13', NULL, NULL, NULL),
(223, 10, 'Login', '2025-08-07 14:45:24', NULL, '2025-08-07 06:45:24', 7, 8, 2025),
(224, 23, 'Logout', '2025-08-07 14:45:44', '2025-08-09 10:02:54', '2025-08-07 06:45:44', 7, 8, 2025),
(225, 2, 'Logout', '2025-08-09 10:00:58', '2025-08-09 10:19:23', '2025-08-09 02:00:58', NULL, NULL, NULL),
(226, 23, 'Login', '2025-08-09 10:03:33', NULL, '2025-08-09 02:03:33', NULL, NULL, NULL),
(227, 1, 'Login', '2025-08-09 10:19:49', NULL, '2025-08-09 02:19:49', NULL, NULL, NULL),
(228, 2, 'Login', '2025-08-09 10:20:03', NULL, '2025-08-09 02:20:03', NULL, NULL, NULL),
(229, 16, 'Login', '2025-08-09 10:20:34', NULL, '2025-08-09 02:20:34', NULL, NULL, NULL),
(230, 15, 'Logout', '2025-08-09 13:17:36', '2025-08-09 13:18:03', '2025-08-09 05:17:36', NULL, NULL, NULL),
(231, 2, 'Login', '2025-08-09 13:36:57', NULL, '2025-08-09 05:36:57', NULL, NULL, NULL),
(232, 10, 'Login', '2025-08-09 13:37:30', NULL, '2025-08-09 05:37:30', NULL, NULL, NULL),
(233, 11, 'Login', '2025-08-09 13:37:49', NULL, '2025-08-09 05:37:49', NULL, NULL, NULL),
(234, 12, 'Login', '2025-08-09 13:38:09', NULL, '2025-08-09 05:38:09', NULL, NULL, NULL),
(235, 2, 'Login', '2025-08-09 15:16:54', NULL, '2025-08-09 07:16:54', NULL, NULL, NULL),
(236, 2, 'Login', '2025-08-09 17:21:34', NULL, '2025-08-09 09:21:34', NULL, NULL, NULL),
(237, 22, 'Login', '2025-08-09 17:24:30', NULL, '2025-08-09 09:24:30', NULL, NULL, NULL),
(238, 2, 'Login', '2025-08-12 12:02:26', NULL, '2025-08-12 04:02:26', NULL, NULL, NULL),
(239, 2, 'Login', '2025-08-25 15:37:25', NULL, '2025-08-25 07:37:25', NULL, NULL, NULL),
(240, 2, 'Login', '2025-08-28 14:48:33', NULL, '2025-08-28 06:48:33', NULL, NULL, NULL),
(241, 2, 'Logout', '2025-08-28 18:51:25', '2025-08-28 18:52:17', '2025-08-28 10:51:25', NULL, NULL, NULL),
(242, 2, 'Login', '2025-08-29 13:50:57', NULL, '2025-08-29 05:50:57', NULL, NULL, NULL),
(243, 2, 'Login', '2025-08-31 15:30:13', NULL, '2025-08-31 07:30:13', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attendance_folders`
--

CREATE TABLE `attendance_folders` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(10) NOT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `equipment_name` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `photo`, `equipment_name`, `type`, `status`, `description`, `image_path`, `created_at`, `updated_at`) VALUES
(4, 'uploads/equip_68512083953819.05242117.jpg', 'Treadmill', 'aa', 'In Use', 'cutieeeee', NULL, '2025-06-17 08:00:03', '2025-06-17 08:07:46'),
(5, 'uploads/equip_6851249e499d79.70913114.jpg', 'Dumbbell', 'aa', 'Maintenance', 'gggggg', NULL, '2025-06-17 08:17:34', NULL),
(6, 'uploads/equip_68551fe2212509.44551219.webp', 'ithhitjgr', 'nrkhfrjfk', 'Maintenance', 'er3irj3w', NULL, '2025-06-20 08:46:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

CREATE TABLE `memberships` (
  `id` int(11) NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `memberships`
--

INSERT INTO `memberships` (`id`, `plan_name`, `duration`, `price`, `description`, `created_at`, `user_id`, `start_date`, `end_date`) VALUES
(9, 'Personal Trainer', '1 Day', 50.00, '', '2025-08-07 13:30:21', 20, '2025-08-07', '2025-08-08'),
(10, 'Gym Access', '1 Day', 50.00, '', '2025-08-07 13:32:12', 23, '2025-08-28', '2025-08-29');

-- --------------------------------------------------------

--
-- Table structure for table `monthly_attendance`
--

CREATE TABLE `monthly_attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `days_present` int(11) DEFAULT 0,
  `days_absent` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `membership_end` date NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `course` varchar(50) DEFAULT NULL,
  `section` varchar(50) DEFAULT NULL,
  `customer_id` varchar(50) DEFAULT NULL,
  `payment_plan` varchar(50) DEFAULT NULL,
  `services` text DEFAULT NULL,
  `faculty_id` varchar(50) DEFAULT NULL,
  `faculty_dept` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_otp` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `age`, `gender`, `email`, `phone`, `membership_end`, `role`, `student_id`, `course`, `section`, `customer_id`, `payment_plan`, `services`, `faculty_id`, `faculty_dept`, `created_at`, `reset_otp`, `otp_expiry`) VALUES
(1, 'Sharies', 'Esoto', 21, 'Female', 'shariesesoto@gmail.com', '09615273069', '2025-01-01', 'Faculty', NULL, NULL, NULL, NULL, '', '', '2212121', 'COT', '2025-05-07 04:52:31', NULL, NULL),
(2, 'Yosef', 'Esoto', 5, 'Male', 'shariesesoto1@gmail.com', '09615273061', '2025-01-01', 'Student', '3221852', 'BSIT', '3B DAY', NULL, '', '', NULL, NULL, '2025-05-07 04:55:36', NULL, NULL),
(4, 'Ethel', 'Esoto', 23, 'Female', 'shariesesotoaa@gmail.com', '09615273064', '2025-01-01', 'Faculty', NULL, NULL, NULL, NULL, '', '', '1111111', 'COE', '2025-05-07 05:04:46', NULL, NULL),
(5, 'Sharies', 'sharies', 11, 'Male', 'shariesesoto111@gmail.com', '09615273111', '2025-01-01', 'Student', '3221811', 'BSIT', '3B DAY', NULL, '', '', NULL, NULL, '2025-05-07 05:15:04', NULL, NULL),
(6, 'Sharies', 'sharies', 1111, 'Female', 'shariesesoto0@gmail.com', '09615273067', '2025-01-01', 'Faculty', NULL, NULL, NULL, NULL, '', '', '1111111', 'COT', '2025-05-07 05:16:22', NULL, NULL),
(10, 'Selyn', 'Esoto', 30, 'Female', 'shariesesto@gmail.com', '0961527069', '2025-01-01', 'Customer', NULL, NULL, NULL, '7285640', '1 Week', 'Gym Access', NULL, NULL, '2025-05-26 14:38:34', NULL, NULL),
(11, 'Drake', 'Ladeke', 21, 'Male', 'drake@gmail.com', '09615273000', '2025-01-01', 'Customer', NULL, NULL, NULL, '6784595', '1 Week', 'Gym Access', NULL, NULL, '2025-06-19 07:50:55', NULL, NULL),
(12, 'chi', 'chang', 22, 'Male', 'ariesesoto@gmail.com', '09115273069', '2025-01-01', 'Customer', NULL, NULL, NULL, '5295400', '1 Week', 'Gym Access', NULL, NULL, '2025-07-02 02:25:25', NULL, NULL),
(15, 'yvonne', 'batucan', 111, 'Female', 'shariese@gmail.com', '096152711169', '2025-01-01', 'Student', '23232323', 'BSIT', '3B DAY', NULL, '', '', NULL, NULL, '2025-07-02 06:19:20', NULL, NULL),
(16, 'qqq', 'aaa', 111, 'Male', '1ariesesoto@gmail.com', '09625273069', '2025-01-01', 'Customer', NULL, NULL, NULL, '7255896', '30 Days', 'Gym Access', NULL, NULL, '2025-07-02 06:24:46', NULL, NULL),
(17, 'aaaaaaaaaaaaa', 'aaaaaaaaaaa', 11, 'Female', 'sharieaasesoto@gmail.com', '096115273069', '2025-01-01', 'Customer', NULL, NULL, NULL, '0758696', '1 Week', 'Gym Access', NULL, NULL, '2025-07-02 06:33:17', NULL, NULL),
(18, 'Shar', 'aaa', 21, 'Female', 'saariesesoto@gmail.com', '09615271069', '2025-01-01', 'Customer', NULL, NULL, NULL, '7616090', '1 Week', 'Gym Access', NULL, NULL, '2025-07-02 07:05:39', NULL, NULL),
(19, 'heufga', 'asjagdj', 11, 'Male', 'shaqqriesesoto@gmail.com', '09615273009', '2025-01-01', 'Customer', NULL, NULL, NULL, '3687601', '30 Days', 'Gym Access', NULL, NULL, '2025-07-02 07:35:54', NULL, NULL),
(21, 'aaa', 'xxxx', 11, 'Male', 'qqqq1qqqq@gmail.com', '09015273069', '2025-01-01', 'Customer', NULL, NULL, NULL, '7042400', '30 Days', 'Gym Access', NULL, NULL, '2025-08-07 03:18:38', NULL, NULL),
(22, 'kabang', 'aaa', 111, 'Male', 'bang@gmail.com', '09005273069', '2025-01-01', 'Customer', NULL, NULL, NULL, '1572517', '1 Week', 'Gym Access', NULL, NULL, '2025-08-07 03:38:31', NULL, NULL),
(23, 'shxriez', 'Esxtx', 22, 'Female', 'shariesesoto03@gmail.com', '09325305178', '2025-01-01', 'Customer', NULL, NULL, NULL, '2887741', '1 Week', 'Gym Access', NULL, NULL, '2025-08-07 05:31:54', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_memberships`
--

CREATE TABLE `user_memberships` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `membership_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `expiry_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `yearly_attendance`
--

CREATE TABLE `yearly_attendance` (
  `id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_logins` int(11) DEFAULT 0,
  `total_logouts` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `alerts_log`
--
ALTER TABLE `alerts_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_membership_id` (`user_membership_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `attendance_folders`
--
ALTER TABLE `attendance_folders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monthly_attendance`
--
ALTER TABLE `monthly_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_memberships`
--
ALTER TABLE `user_memberships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `membership_id` (`membership_id`);

--
-- Indexes for table `yearly_attendance`
--
ALTER TABLE `yearly_attendance`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `alerts_log`
--
ALTER TABLE `alerts_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=244;

--
-- AUTO_INCREMENT for table `attendance_folders`
--
ALTER TABLE `attendance_folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `memberships`
--
ALTER TABLE `memberships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `monthly_attendance`
--
ALTER TABLE `monthly_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `user_memberships`
--
ALTER TABLE `user_memberships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `yearly_attendance`
--
ALTER TABLE `yearly_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alerts_log`
--
ALTER TABLE `alerts_log`
  ADD CONSTRAINT `alerts_log_ibfk_1` FOREIGN KEY (`user_membership_id`) REFERENCES `user_memberships` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_folders`
--
ALTER TABLE `attendance_folders`
  ADD CONSTRAINT `attendance_folders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `monthly_attendance`
--
ALTER TABLE `monthly_attendance`
  ADD CONSTRAINT `monthly_attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_memberships`
--
ALTER TABLE `user_memberships`
  ADD CONSTRAINT `user_memberships_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_memberships_ibfk_2` FOREIGN KEY (`membership_id`) REFERENCES `memberships` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
