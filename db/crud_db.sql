-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2026 at 10:21 AM
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
-- Table structure for table `barangay_settings`
--

CREATE TABLE `barangay_settings` (
  `id` int(11) NOT NULL,
  `barangay_name` varchar(100) DEFAULT NULL,
  `municipality` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangay_settings`
--

INSERT INTO `barangay_settings` (`id`, `barangay_name`, `municipality`, `province`, `contact_number`, `updated_at`, `created_at`) VALUES
(1, 'tabu', 'Ilog City', 'Negros Occidental', '09633003878', '2026-05-10 02:56:21', '2026-04-29 18:22:30');

-- --------------------------------------------------------

--
-- Table structure for table `blotter_hearings`
--

CREATE TABLE `blotter_hearings` (
  `id` int(11) NOT NULL,
  `blotter_id` int(11) NOT NULL COMMENT 'FK to blotter_records.id',
  `hearing_date` date NOT NULL,
  `hearing_time` time DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `presiding_officer` varchar(150) DEFAULT NULL COMMENT 'Name of the official',
  `notes` text DEFAULT NULL,
  `outcome` varchar(255) DEFAULT NULL,
  `status` enum('Scheduled','In Progress','Completed','Cancelled') DEFAULT 'Scheduled',
  `notification_sent` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL COMMENT 'FK to users.id',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blotter_parties`
--

CREATE TABLE `blotter_parties` (
  `id` int(10) UNSIGNED NOT NULL,
  `blotter_id` int(11) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `outsider_name` varchar(150) DEFAULT NULL,
  `outsider_address` varchar(255) DEFAULT NULL,
  `role` enum('complainant','respondent','witness') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blotter_records`
--

CREATE TABLE `blotter_records` (
  `id` int(11) NOT NULL,
  `case_number` varchar(20) DEFAULT NULL,
  `incident_type` varchar(50) NOT NULL,
  `incident_date` date NOT NULL,
  `incident_location` varchar(255) DEFAULT NULL,
  `purok` varchar(255) DEFAULT NULL,
  `details` text NOT NULL,
  `status` enum('Pending','Investigating','Ongoing','For Hearing','Settled','Dismissed','Referred','Unsettled') DEFAULT 'Pending',
  `action_taken` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blotter_timeline`
--

CREATE TABLE `blotter_timeline` (
  `id` int(11) NOT NULL,
  `blotter_id` int(11) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `certificate_number` varchar(30) DEFAULT NULL,
  `resident_id` int(11) NOT NULL DEFAULT 0,
  `certificate_type` enum('Barangay Clearance','Certificate of Indigency','Certificate of Residency','Business Permit','Solo Parent') NOT NULL DEFAULT 'Barangay Clearance',
  `purpose` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificate_types`
--

CREATE TABLE `certificate_types` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL COMMENT 'Must match enum value in certificates table',
  `prefix` varchar(10) DEFAULT NULL,
  `content` text DEFAULT NULL COMMENT 'HTML Template Content',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificate_types`
--

INSERT INTO `certificate_types` (`id`, `name`, `prefix`, `content`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Barangay Clearance', 'CLEAR', '<div style=\"font-family: \'Times New Roman\', serif; text-align: justify; padding: 20px 50px;\">\n    <p style=\"text-align: right;\">{date_issued}</p>\n    <br>\n    <p>TO WHOM IT MAY CONCERN:</p>\n    <p style=\"text-indent: 50px;\">This is to certify that <strong>{resident_name}</strong>, <strong>{age}</strong> years old, <strong>{civil_status}</strong>, is a bonafide resident of <strong>{barangay_name}</strong>, {municipality}.</p>\n    <p style=\"text-indent: 50px;\">This certification is issued upon the request of the interested party for <strong>{purpose}</strong> purposes.</p>\n    <br><br>\n    <p>Issued this <strong>{date_issued}</strong> at {barangay_name}.</p>\n</div>', 1, '2026-04-29 12:28:50', '2026-04-29 12:28:50'),
(5, 'Certificate of Indigency', 'INDIG', '<div style=\"font-family: Times New Roman, serif; text-align: justify; padding: 20px 50px;\">\n    <p style=\"text-align: right;\">{date_issued}</p>\n    <br>\n    <p>TO WHOM IT MAY CONCERN:</p>\n    <p style=\"text-indent: 50px;\">This is to certify that <strong>{resident_name}</strong>, <strong>{age}</strong> years old, <strong>{civil_status}</strong>, is a resident of <strong>{barangay_name}</strong>, {municipality}.</p>\n    <p style=\"text-indent: 50px;\">Based on available records, certification is hereby issued to <strong>{resident_name}</strong> that they are indigent and of limited financial capability.</p>\n    <p style=\"text-indent: 50px;\">This certification is issued upon the request of the interested party for <strong>{purpose}</strong> purposes.</p>\n    <br><br>\n    <p>Issued this <strong>{date_issued}</strong> at {barangay_name}.</p>\n</div>', 1, '2026-04-29 12:39:43', '2026-04-29 12:39:43'),
(8, 'Solo Parent', NULL, '<div style=\"font-family: Times New Roman, serif; text-align: justify; padding: 20px 50px;\">\n    <p style=\"text-align: right;\">{date_issued}</p>\n    <br>\n    <p>TO WHOM IT MAY CONCERN:</p>\n    <p style=\"text-indent: 50px;\">This is to certify that <strong>{resident_name}</strong>, <strong>{age}</strong> years old, <strong>{civil_status}</strong>, is a resident of <strong>{barangay_name}</strong>, {municipality}.</p>\n    <p style=\"text-indent: 50px;\">That he/she is a Solo Parent as defined under RA 8972 (Solo Parents\' Welfare Act of 2000) and is entitled to benefits provided under the law.</p>\n    <p style=\"text-indent: 50px;\">This certification is issued upon the request of the interested party for <strong>{purpose}</strong> purposes.</p>\n    <br><br>\n    <p>Issued this <strong>{date_issued}</strong> at {barangay_name}.</p>\n</div>', 1, '2026-04-29 12:39:43', '2026-04-29 12:39:43'),
(9, 'Business Permit', NULL, '<div style=\"font-family: Times New Roman, serif; text-align: justify; padding: 20px 50px;\">\n    <p style=\"text-align: right;\">{date_issued}</p>\n    <br>\n    <p>OWNER: <strong>{resident_name}</strong></p>\n    <p>ADDRESS: <strong>{address}, {barangay_name}, {municipality}</strong></p>\n    <br>\n    <p style=\"text-indent: 50px;\">This is to certify that the above-named person is hereby authorized to operate a business within the jurisdiction of this Barangay.</p>\n    <p style=\"text-indent: 50px;\">Location/Type of Business: <strong>{purpose}</strong></p>\n    <br><br>\n    <p>Issued this <strong>{date_issued}</strong> at {barangay_name}.</p>\n</div>', 1, '2026-04-29 12:42:39', '2026-04-29 12:42:39'),
(10, 'Certificate of Residency', NULL, '<div style=\"font-family: Times New Roman, serif; text-align: justify; padding: 20px 50px;\">\n    <p style=\"text-align: right;\">{date_issued}</p>\n    <br>\n    <p>TO WHOM IT MAY CONCERN:</p>\n    <p style=\"text-indent: 50px;\">This is to certify that <strong>{resident_name}</strong>, <strong>{age}</strong> years old, <strong>{civil_status}</strong>, is a bona fide resident of <strong>{barangay_name}</strong>, {municipality}.</p>\n    <p style=\"text-indent: 50px;\">This certification is issued upon the request of the interested party for <strong>{purpose}</strong> purposes.</p>\n    <br><br>\n    <p>Issued this <strong>{date_issued}</strong> at {barangay_name}.</p>\n</div>', 1, '2026-04-29 12:42:40', '2026-04-29 12:42:40');

-- --------------------------------------------------------

--
-- Table structure for table `households`
--

CREATE TABLE `households` (
  `id` int(11) NOT NULL,
  `household_no` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `head_resident_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sitio` varchar(255) DEFAULT NULL,
  `house_type` enum('Concrete','Semi-Concrete','Wood','Light Materials') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `households`
--

INSERT INTO `households` (`id`, `household_no`, `address`, `street_address`, `head_resident_id`, `created_at`, `sitio`, `house_type`) VALUES
(1, 'HH-2026-001', '123 Main Street, Purok Masagana, , Barangay Tabu, Negros Occidental', '123 Main Street', NULL, '2026-05-10 04:55:23', 'Purok Masagana', 'Concrete');

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

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-04-12-125446', 'App\\Database\\Migrations\\AddHouseholdColumns', 'default', 'App', 1775998509, 1),
(2, '2026-04-12-125539', 'App\\Database\\Migrations\\AddDeletedAtToResidents', 'default', 'App', 1775998890, 2),
(3, '2026-04-12-133922', 'App\\Database\\Migrations\\AddUpdatedAtToResidents', 'default', 'App', 1776001177, 3);

-- --------------------------------------------------------

--
-- Table structure for table `officials`
--

CREATE TABLE `officials` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `position` varchar(100) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `id` int(11) NOT NULL,
  `household_id` int(11) DEFAULT NULL,
  `is_household_head` tinyint(1) DEFAULT 0 COMMENT 'Is this person the head of household?',
  `joined_household_date` date DEFAULT NULL COMMENT 'Date when joined this household',
  `left_household_date` date DEFAULT NULL COMMENT 'Date when left this household',
  `first_name` varchar(80) NOT NULL,
  `middle_name` varchar(80) DEFAULT NULL,
  `last_name` varchar(80) NOT NULL,
  `birthdate` date NOT NULL,
  `sex` enum('male','female') NOT NULL,
  `civil_status` enum('single','married','widowed','separated') NOT NULL DEFAULT 'single',
  `sitio` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `citizenship` varchar(100) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `relationship_to_head` varchar(100) DEFAULT NULL COMMENT 'Relationship to household head',
  `is_voter` tinyint(1) NOT NULL DEFAULT 0,
  `is_senior_citizen` tinyint(1) NOT NULL DEFAULT 0,
  `is_pwd` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive','transferred','deceased') NOT NULL DEFAULT 'active',
  `registered_by` int(11) DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_logs`
--

CREATE TABLE `tbl_logs` (
  `LOGID` int(11) NOT NULL,
  `USERID` varchar(30) DEFAULT NULL,
  `ACTION` text DEFAULT NULL,
  `DATELOG` datetime DEFAULT NULL,
  `TIMELOG` datetime DEFAULT NULL,
  `user_ip_address` text DEFAULT NULL,
  `device_used` text DEFAULT NULL,
  `USER_NAME` varchar(100) DEFAULT NULL,
  `identifier` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_logs`
--

INSERT INTO `tbl_logs` (`LOGID`, `USERID`, `ACTION`, `DATELOG`, `TIMELOG`, `user_ip_address`, `device_used`, `USER_NAME`, `identifier`) VALUES
(1, '11', 'User Logged Out', '2026-05-10 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(2, '11', 'User Logged In', '2026-05-10 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(3, '11', 'User Logged In', '2026-05-10 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(4, '11', 'Updated Household HH-2026-001 with 0 active member(s)', '2026-05-10 00:00:00', '2014-08-19 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', 'household'),
(5, '11', 'User Logged Out', '2026-05-10 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(6, '11', 'User Logged In', '2026-05-10 00:00:00', '2015-10-11 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(7, '11', 'User Logged Out', '2026-05-10 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(8, '12', 'User Logged In', '2026-05-10 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(9, '12', 'User Logged Out', '2026-05-10 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(10, '11', 'User Logged In', '2026-05-10 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Admin User', ''),
(11, '11', 'User Logged In', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(12, '11', 'User Logged Out', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(13, '12', 'User Logged In', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(14, '12', 'User Logged Out', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(15, '11', 'User Logged In', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(16, '11', 'User Logged Out', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(17, '12', 'User Logged In', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(18, '12', 'User Logged Out', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(19, '11', 'User Logged In', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(20, '11', 'User Logged In', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(21, '11', 'Added Resident Lourd ian akol', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(22, '11', 'Added Resident luffy monkey', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Admin User', ''),
(23, '11', 'Added Resident lourd ian akol', '2026-05-11 00:00:00', '2014-02-08 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(24, '11', 'User Logged In', '2026-05-11 00:00:00', '2014-12-19 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(25, '11', 'Added Resident zackie wowie', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(26, '11', 'Added Resident luffy monkey', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(27, '11', 'Added Resident luffy monkey', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(28, '11', 'Added Resident luffy monkey', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(29, '11', 'User Logged In', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(30, '11', 'Added Resident luffy monkey', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(31, '11', 'User Logged In', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(32, '11', 'Added Resident luffy monkey', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(33, '11', 'Added Resident luffy monkey', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', '');

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
(9, NULL, 'glennazuelo1@gmail.comd', '$2y$10$Xv57FAvSxnip8apDXF3rmutrLIESHcAHYVzQMKgMf2tu6GknL4Plm', 'Admin', 'Active', 'Glenn Azuelo', '09125110476', '2025-05-23 23:00:28', '2025-05-23 15:00:28', '2025-05-23 15:00:28'),
(10, NULL, 'barabidaomsim@gmail.com1', '$2y$10$EeYLyV6ZWCjC9DBTwTzVZeHAvcbXIcLYLdvX.rowQdYnQ/DHIt41C', 'staff', 'active', 'Cherry Ann Grandia', '09125110476', '2025-05-23 23:00:50', '2026-05-11 06:59:41', '2025-07-20 20:19:17'),
(11, '5cf17985-33c3-11f1-b689-bf1b47148d8a', 'admin@bmis.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', 'Admin User', NULL, '2026-04-09 03:22:39', '2026-04-09 03:26:46', NULL),
(12, '5cf9cf4f-33c3-11f1-b689-bf1b47148d8a', 'staff@bmis.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', 'active', 'Staff User', NULL, '2026-04-09 03:22:39', '2026-04-09 03:26:46', NULL),
(14, NULL, 'lourdianrentinoakol123@gmail.com', '$2y$10$3EGfU0svcZI/8yTsgUo6G.UU8pH8S3PFcLE.pqke4OtFxEQkc0v2S', 'admin', 'inactive', 'luffy monkey', '93948209', '2026-04-26 12:15:38', '2026-05-07 12:47:08', '2026-05-07 12:47:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barangay_settings`
--
ALTER TABLE `barangay_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blotter_hearings`
--
ALTER TABLE `blotter_hearings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_blotter` (`blotter_id`),
  ADD KEY `fk_hearing_createdby` (`created_by`);

--
-- Indexes for table `blotter_parties`
--
ALTER TABLE `blotter_parties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_blotter` (`blotter_id`),
  ADD KEY `idx_resident` (`resident_id`);

--
-- Indexes for table `blotter_records`
--
ALTER TABLE `blotter_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_case_number` (`case_number`),
  ADD KEY `fk_blotter_createdby` (`created_by`),
  ADD KEY `fk_blotter_updatedby` (`updated_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_incident_date` (`incident_date`);

--
-- Indexes for table `blotter_timeline`
--
ALTER TABLE `blotter_timeline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_blotter` (`blotter_id`),
  ADD KEY `fk_timeline_createdby` (`created_by`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_cert_number` (`certificate_number`),
  ADD KEY `fk_cert_createdby` (`created_by`),
  ADD KEY `idx_cert_resident` (`resident_id`);

--
-- Indexes for table `certificate_types`
--
ALTER TABLE `certificate_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `households`
--
ALTER TABLE `households`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `household_no` (`household_no`),
  ADD KEY `fk_head_resident` (`head_resident_id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `officials`
--
ALTER TABLE `officials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_resident` (`resident_id`);

--
-- Indexes for table `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_household` (`household_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_relationship` (`relationship_to_head`),
  ADD KEY `residents_ibfk_2` (`registered_by`),
  ADD KEY `idx_sitio` (`sitio`);

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
-- AUTO_INCREMENT for table `barangay_settings`
--
ALTER TABLE `barangay_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blotter_hearings`
--
ALTER TABLE `blotter_hearings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blotter_parties`
--
ALTER TABLE `blotter_parties`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blotter_records`
--
ALTER TABLE `blotter_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blotter_timeline`
--
ALTER TABLE `blotter_timeline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificate_types`
--
ALTER TABLE `certificate_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `households`
--
ALTER TABLE `households`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `officials`
--
ALTER TABLE `officials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_logs`
--
ALTER TABLE `tbl_logs`
  MODIFY `LOGID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blotter_hearings`
--
ALTER TABLE `blotter_hearings`
  ADD CONSTRAINT `fk_hearing_createdby` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_hearings_blotter` FOREIGN KEY (`blotter_id`) REFERENCES `blotter_records` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blotter_parties`
--
ALTER TABLE `blotter_parties`
  ADD CONSTRAINT `fk_blotter_parties_blotter` FOREIGN KEY (`blotter_id`) REFERENCES `blotter_records` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_blotter_parties_resident` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `blotter_records`
--
ALTER TABLE `blotter_records`
  ADD CONSTRAINT `fk_blotter_createdby` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_blotter_updatedby` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `blotter_timeline`
--
ALTER TABLE `blotter_timeline`
  ADD CONSTRAINT `fk_timeline_blotter` FOREIGN KEY (`blotter_id`) REFERENCES `blotter_records` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_timeline_createdby` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `fk_cert_createdby` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_cert_resident` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `households`
--
ALTER TABLE `households`
  ADD CONSTRAINT `fk_head_resident` FOREIGN KEY (`head_resident_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `officials`
--
ALTER TABLE `officials`
  ADD CONSTRAINT `fk_officials_resident` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
