-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2026 at 05:32 AM
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
-- Database: `crud_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `households`
--

CREATE TABLE `households` (
  `id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `head_resident_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` datetime NOT NULL,
  `user_agent` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `email`, `ip_address`, `attempt_time`, `user_agent`) VALUES
(36, 'glennazuelo1@gmail.com', '::142432432', '2025-04-15 13:15:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE `person` (
  `id` int(11) NOT NULL,
  `name` varchar(55) NOT NULL,
  `address` varchar(100) NOT NULL,
  `bday` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `person`
--

INSERT INTO `person` (`id`, `name`, `address`, `bday`) VALUES
(3, 'joshua ramos', '', '2005-12-12'),
(4, 'aagapito', '', '2005-12-12');

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `id` int(11) NOT NULL,
  `household_id` int(11) NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `middle_name` varchar(80) DEFAULT NULL,
  `last_name` varchar(80) NOT NULL,
  `birthdate` date NOT NULL,
  `sex` enum('male','female') NOT NULL,
  `civil_status` enum('single','married','widowed','separated') NOT NULL DEFAULT 'single',
  `contact_number` varchar(20) DEFAULT NULL,
  `relationship_to_head` varchar(50) DEFAULT NULL,
  `is_voter` tinyint(1) NOT NULL DEFAULT 0,
  `is_senior_citizen` tinyint(1) NOT NULL DEFAULT 0,
  `is_pwd` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `registered_by` int(11) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_logs`
--

CREATE TABLE `tbl_logs` (
  `LOGID` int(11) NOT NULL,
  `USERID` varchar(30) DEFAULT NULL,
  `ACTION` text DEFAULT NULL,
  `DATELOG` varchar(30) DEFAULT NULL,
  `TIMELOG` varchar(30) DEFAULT NULL,
  `user_ip_address` text DEFAULT NULL,
  `device_used` text DEFAULT NULL,
  `USER_NAME` varchar(100) DEFAULT NULL,
  `identifier` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_logs`
--

INSERT INTO `tbl_logs` (`LOGID`, `USERID`, `ACTION`, `DATELOG`, `TIMELOG`, `user_ip_address`, `device_used`, `USER_NAME`, `identifier`) VALUES
(1, '1', 'New User has been apdated: Glenn Azuelo', '2025-07-21', '20:11:13', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', NULL, 'UPDATED'),
(2, '1', 'Logout', '2025-07-21', '20:12:03', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', NULL, 'LOGOUT'),
(3, '1', 'Login: Glenn Azuelo', '2025-07-21', '20:12:16', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', NULL, 'LOGIN'),
(4, '1', 'Logout', '2025-07-21', '20:14:42', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', NULL, 'LOGOUT'),
(5, '10', 'Login: Cherry Ann Grandia', '2025-07-21', '20:14:47', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', NULL, 'LOGIN'),
(6, '10', 'New User has been apdated: Glenn Azuelo', '2025-07-21', '20:18:03', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '10', 'UPDATED'),
(7, '10', 'New User has been apdated: Cherry Ann Grandia', '2025-07-21', '20:19:17', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', NULL, 'UPDATED'),
(8, '10', 'Logout', '2025-07-21', '20:19:18', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', NULL, 'LOGOUT'),
(9, '1', 'Login: Glenn Azuelo', '2025-07-21', '20:19:23', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(10, '1', 'Logout', '2025-07-21', '20:19:56', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGOUT'),
(11, '1', 'Login: Glenn Azuelo', '2025-07-21', '20:21:27', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(12, '1', 'New User has been added: xxx', '2025-07-21', '20:32:39', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(13, '1', 'Delete user', '2025-07-21', '20:32:44', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETED'),
(14, '1', 'Login: Glenn Azuelo', '2026-02-17', '14:58:41', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(15, '1', 'New Person has been added: lourd ian', '2026-02-17', '15:41:42', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(16, '1', 'New Person has been added: joshua garia ', '2026-02-17', '15:45:42', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(17, '1', 'Delete user', '2026-02-17', '15:48:17', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETED'),
(18, '1', 'New Person has been added: joshua garia ', '2026-02-17', '15:49:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(19, '1', 'New Person has been added: joshua garia ', '2026-02-17', '15:52:46', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(20, '1', 'New Person has been added: joshua garia ', '2026-02-17', '15:52:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(21, '1', 'Login: Glenn Azuelo', '2026-02-17', '19:54:06', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(22, '1', 'Login: Glenn Azuelo', '2026-02-20', '09:20:21', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(23, '1', 'New Person has been added: aagapito', '2026-02-20', '09:20:58', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(24, '1', 'New Person has been updated: joshua ramos', '2026-02-20', '09:21:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'UPDATED'),
(25, '1', 'New Person has been updated: joshua garnica', '2026-02-20', '09:22:21', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'UPDATED'),
(26, '1', 'New Person has been updated: joshua boang', '2026-02-20', '09:29:52', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'UPDATED'),
(27, '1', 'New Person has been updated: joshua marcos', '2026-02-20', '09:35:35', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', '2003-12-12UPDATED'),
(28, '1', 'New Person has been updated: bioangmarcos', '2026-02-20', '09:37:28', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', '2000-12-15UPDATED'),
(29, '1', 'Login: Glenn Azuelo', '2026-02-20', '11:13:28', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(30, '1', 'Person deleted: 0', '2026-02-20', '11:24:14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(31, '1', 'Person deleted: 0', '2026-02-20', '11:24:20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(32, '1', 'Person deleted: 0', '2026-02-20', '11:28:05', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(33, '1', 'Person deleted: 0', '2026-02-20', '11:28:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(34, '1', 'Person deleted: 0', '2026-02-20', '11:28:16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(35, '1', 'Person deleted: 0', '2026-02-20', '11:28:25', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(36, '1', 'Person deleted: 0', '2026-02-20', '11:32:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(37, '1', 'Person deleted: 0', '2026-02-20', '11:33:05', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(38, '1', 'Person deleted: 0', '2026-02-20', '11:44:36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(39, '1', 'Person deleted: 0', '2026-02-20', '11:44:44', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(40, '1', 'Login: Glenn Azuelo', '2026-02-21', '17:04:30', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(41, '1', 'Login: Glenn Azuelo', '2026-02-22', '11:20:45', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(42, '1', 'Login: Glenn Azuelo', '2026-02-22', '15:58:49', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(43, '1', 'New Person has been added: aagapito', '2026-02-22', '15:59:17', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(44, '1', 'New Person has been added: lourd ian', '2026-02-22', '16:00:42', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(45, '1', 'Person deleted: 1', '2026-02-22', '16:01:17', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(46, '1', 'Person updated: lourd ian akol', '2026-02-22', '17:46:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'UPDATED'),
(47, '1', 'New Person has been added: joshua ramos', '2026-02-22', '18:18:35', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(48, '1', 'Login: Glenn Azuelo', '2026-02-23', '09:24:28', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(49, '1', 'Login: Glenn Azuelo', '2026-03-02', '05:25:06', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(50, '1', 'Person updated: lourd ian akol lo', '2026-03-02', '05:25:51', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'UPDATED'),
(51, '1', 'Login: Glenn Azuelo', '2026-03-02', '08:09:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(52, '1', 'New Person has been added: aagapito', '2026-03-02', '09:27:03', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(53, '1', 'Login: Glenn Azuelo', '2026-03-31', '18:53:34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(54, '1', 'Logout', '2026-03-31', '18:53:46', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGOUT'),
(55, '1', 'Login: Glenn Azuelo', '2026-03-31', '18:54:30', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(56, '1', 'Logout', '2026-03-31', '18:54:42', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGOUT'),
(57, '1', 'Login: Glenn Azuelo', '2026-03-31', '20:11:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(58, '1', 'Logout', '2026-03-31', '20:58:25', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGOUT'),
(59, '1', 'Login: Glenn Azuelo as User', '2026-04-07', '12:38:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(60, '11', 'Login: Admin User', '2026-04-09', '11:27:11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Admin User', 'LOGIN'),
(61, '11', 'Logout', '2026-04-09', '11:41:30', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Admin User', 'LOGOUT'),
(62, '12', 'Login: Staff User', '2026-04-09', '11:42:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(63, '11', 'Login: Admin User', '2026-04-09', '11:50:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'Admin User', 'LOGIN'),
(64, '12', 'Logout', '2026-04-09', '13:00:06', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGOUT'),
(65, '11', 'Login: Admin User', '2026-04-09', '13:00:14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Admin User', 'LOGIN'),
(66, '12', 'Login: Staff User', '2026-04-09', '19:36:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(67, '12', 'Login: Staff User', '2026-04-10', '20:50:43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(68, '12', 'Logout', '2026-04-10', '21:24:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGOUT'),
(69, '11', 'Login: Admin User', '2026-04-10', '21:33:02', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Admin User', 'LOGIN'),
(70, '11', 'Logout', '2026-04-10', '22:42:23', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Admin User', 'LOGOUT'),
(71, NULL, 'Logout', '2026-04-10', '22:42:23', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, 'LOGOUT'),
(72, '12', 'Login: Staff User', '2026-04-10', '22:42:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(73, '11', 'Login: Admin User', '2026-04-11', '11:33:05', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Admin User', 'LOGIN'),
(74, '12', 'Login: Staff User', '2026-04-11', '14:19:50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(75, '12', 'Login: Staff User', '2026-04-11', '18:58:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(76, '12', 'Person deleted: 2', '2026-04-11', '20:05:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'DELETE');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(100) DEFAULT 'user',
  `status` varchar(100) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uuid`, `email`, `password`, `role`, `status`, `name`, `phone`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'lourdianrentinoakol@gmail.com', '$2y$10$hzTTQ5YAb0QDzshQ.GbmvOWi73cPaNIojcXGXwTB.O9lmzITca6GC', 'User', 'Active', 'Glenn Azuelo', '09125110476', '2025-04-17 05:31:01', '2026-02-22 07:58:38', '2025-07-20 20:18:03'),
(9, NULL, 'glennazuelo1@gmail.comd', '$2y$10$Xv57FAvSxnip8apDXF3rmutrLIESHcAHYVzQMKgMf2tu6GknL4Plm', 'Admin', 'Active', 'Glenn Azuelo', '09125110476', '2025-05-23 23:00:28', '2025-05-23 15:00:28', '2025-05-23 15:00:28'),
(10, NULL, 'glennazuelo1@gmail.com1', '$2y$10$PxNNhaa76.SAbFFelJU9xOZRajcVMCZkeToZ09l1FR5ll13saXu4q', 'Admin', 'Active', 'Cherry Ann Grandia', '09125110476', '2025-05-23 23:00:50', '2025-07-20 20:19:17', '2025-07-20 20:19:17'),
(11, '5cf17985-33c3-11f1-b689-bf1b47148d8a', 'admin@bmis.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', 'Admin User', NULL, '2026-04-09 03:22:39', '2026-04-09 03:26:46', NULL),
(12, '5cf9cf4f-33c3-11f1-b689-bf1b47148d8a', 'staff@bmis.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', 'active', 'Staff User', NULL, '2026-04-09 03:22:39', '2026-04-09 03:26:46', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `households`
--
ALTER TABLE `households`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registered_by` (`registered_by`),
  ADD KEY `idx_household` (`household_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `tbl_logs`
--
ALTER TABLE `tbl_logs`
  ADD PRIMARY KEY (`LOGID`),
  ADD KEY `USERID` (`USERID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `households`
--
ALTER TABLE `households`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `person`
--
ALTER TABLE `person`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_logs`
--
ALTER TABLE `tbl_logs`
  MODIFY `LOGID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `residents`
--
ALTER TABLE `residents`
  ADD CONSTRAINT `residents_ibfk_1` FOREIGN KEY (`household_id`) REFERENCES `households` (`id`),
  ADD CONSTRAINT `residents_ibfk_2` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
