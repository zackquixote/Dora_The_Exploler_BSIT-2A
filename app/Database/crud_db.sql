-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2026 at 11:16 AM
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
  `purok` enum('Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um') DEFAULT NULL,
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

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `certificate_number`, `resident_id`, `certificate_type`, `purpose`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'INDIG-2026-0001', 3, 'Certificate of Indigency', 'business', 122, '2026-05-17 16:57:23', NULL);

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
  `sitio` enum('Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um') DEFAULT NULL,
  `house_type` enum('Concrete','Semi-Concrete','Wood','Light Materials') DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `households`
--

INSERT INTO `households` (`id`, `household_no`, `address`, `street_address`, `head_resident_id`, `created_at`, `sitio`, `house_type`, `deleted_at`) VALUES
(1, 'HH-2026-001', '123 Main Street, Purok Masagana, , Barangay Tabu, Negros Occidental', '123 Main Street', NULL, '2026-05-10 04:55:23', 'Purok Masagana', 'Concrete', NULL),
(2, 'HH-2026-002', 'bangga patyo, Purok Malipayon, , Barangay Tabu, Negros Occidental', 'bangga patyo', 1, '2026-05-14 12:02:07', 'Purok Malipayon', 'Concrete', NULL),
(3, 'HH-2026-003', 'Purok Malipayon, , Barangay Tabu, Negros Occidental', '', NULL, '2026-05-14 12:02:25', 'Purok Malipayon', '', NULL),
(4, 'HH-2026-004', 'Purok Malipayon, , Barangay Tabu, Negros Occidental', '', 5, '2026-05-17 08:58:55', 'Purok Malipayon', '', NULL);

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
(3, '2026-04-12-133922', 'App\\Database\\Migrations\\AddUpdatedAtToResidents', 'default', 'App', 1776001177, 3),
(4, '2026-05-12-000001', 'App\\Database\\Migrations\\CreateResidentTransferHistory', 'default', 'App', 1778759460, 4),
(5, '2026-05-14-020653', 'App\\Database\\Migrations\\AddDeletedAtToHouseholds', 'default', 'App', 1778759460, 4);

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
  `term_start` date DEFAULT NULL,
  `term_end` date DEFAULT NULL,
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
  `sitio` enum('Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um') DEFAULT NULL,
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

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`id`, `household_id`, `is_household_head`, `joined_household_date`, `left_household_date`, `first_name`, `middle_name`, `last_name`, `birthdate`, `sex`, `civil_status`, `sitio`, `contact_number`, `occupation`, `citizenship`, `profile_picture`, `relationship_to_head`, `is_voter`, `is_senior_citizen`, `is_pwd`, `status`, `registered_by`, `registered_at`, `updated_at`, `deleted_at`, `created_at`) VALUES
(1, 2, 1, '2026-05-14', NULL, 'zack', 'aquino', 'tulfo', '2005-11-11', 'male', 'single', 'Purok Malipayon', '09633009878', 'student ', 'Filipino', 'purok_malipayon/1778759981_e779df51d323ba636c50.jpg', 'Head', 1, 0, 0, 'active', 11, '2026-05-14 11:59:41', '2026-05-14 12:15:19', NULL, '2026-05-14 19:59:41'),
(2, 2, 0, NULL, NULL, 'maria ', 'santos', 'aquino', '2005-11-11', 'male', 'single', 'Purok Malipayon', '0963303878', 'housewife', 'Filipino', 'purok_malipayon/1778760719_71a37ef152ebb89333b3.jpg', 'Head', 1, 0, 0, 'active', 11, '2026-05-14 12:11:59', '2026-05-14 12:11:59', NULL, '2026-05-14 20:11:59'),
(3, 2, 0, NULL, NULL, 'francisco', 'ejercito', 'tulfo', '1982-11-11', 'male', 'married', 'Purok Malipayon', '3003818', 'seaman', 'Filipino', 'purok_malipayon/1778760856_f88c6c0bf2cc50bbd1e6.jpg', 'Father', 1, 0, 0, 'active', 11, '2026-05-14 12:14:16', '2026-05-14 12:14:16', NULL, '2026-05-14 20:14:16'),
(4, 2, 0, NULL, NULL, 'glaiza', 'aquino', 'tulfo', '2012-12-07', 'male', 'single', 'Purok Malipayon', '93948209', 'student ', 'Filipino', 'purok_malipayon/1778761026_aee7589c3130b22fddb9.jpg', 'Sister', 1, 0, 0, 'active', 11, '2026-05-14 12:17:06', '2026-05-14 12:17:06', NULL, '2026-05-14 20:17:06'),
(5, 4, 1, '2026-05-17', NULL, 'Juan', 'Perez', 'Dela Cruz', '1990-01-01', 'male', 'married', 'Purok Malipayon', '09123456789', 'Farmer', NULL, NULL, 'Head', 0, 0, 0, 'active', 15, '2026-05-14 23:06:26', '2026-05-17 08:58:55', NULL, '2026-05-15 07:06:26');

-- --------------------------------------------------------

--
-- Table structure for table `resident_transfer_history`
--

CREATE TABLE `resident_transfer_history` (
  `id` int(11) UNSIGNED NOT NULL,
  `resident_id` int(11) NOT NULL,
  `from_household_id` int(11) DEFAULT NULL,
  `to_household_id` int(11) DEFAULT NULL,
  `from_household_no` varchar(50) DEFAULT NULL,
  `to_household_no` varchar(50) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `transferred_by` int(11) DEFAULT NULL,
  `transferred_at` datetime DEFAULT NULL
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
(33, '11', 'Added Resident luffy monkey', '2026-05-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(34, '11', 'User Logged In', '2026-05-14 00:00:00', '2026-05-14 19:39:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(35, '11', 'Deleted Household HH-2026-001', '2026-05-14 00:00:00', '2026-05-14 19:58:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', 'household'),
(36, '11', 'Added Resident lourd ian  akol', '2026-05-14 00:00:00', '2026-05-14 19:59:41', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(37, '11', 'Created Household HH-2026-002', '2026-05-14 00:00:00', '2026-05-14 20:02:07', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', 'household'),
(38, '11', 'Created Household HH-2026-003', '2026-05-14 00:00:00', '2026-05-14 20:02:25', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', 'household'),
(39, '11', 'Assigned 1 resident(s) to household #2', '2026-05-14 00:00:00', '2026-05-14 20:05:02', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(40, '11', 'Assigned 1 resident(s) to household #2', '2026-05-14 00:00:00', '2026-05-14 20:05:02', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(41, '11', 'Restored Household HH-2026-001', '2026-05-14 00:00:00', '2026-05-14 20:07:13', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', 'household'),
(42, '11', 'Added Resident maria  aquino', '2026-05-14 00:00:00', '2026-05-14 20:11:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(43, '11', 'Added Resident francisco tulfo', '2026-05-14 00:00:00', '2026-05-14 20:14:16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(44, '11', 'Updated Resident zack tulfo', '2026-05-14 00:00:00', '2026-05-14 20:15:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(45, '11', 'Added Resident glaiza tulfo', '2026-05-14 00:00:00', '2026-05-14 20:17:06', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(46, '11', 'User Logged Out', '2026-05-14 00:00:00', '2026-05-14 20:20:51', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(47, '12', 'User Logged In', '2026-05-14 00:00:00', '2026-05-14 21:00:50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(48, '12', 'User Logged Out', '2026-05-14 00:00:00', '2026-05-14 21:01:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(49, '15', 'User Logged In', '2026-05-14 00:00:00', '2026-05-14 21:01:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'zackquixote', ''),
(50, '15', 'User Logged In', '2026-05-15 00:00:00', '2026-05-15 06:09:52', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'zackquixote', ''),
(51, '15', 'User Logged Out', '2026-05-15 00:00:00', '2026-05-15 06:31:23', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'zackquixote', ''),
(52, '12', 'User Logged In', '2026-05-15 00:00:00', '2026-05-15 06:31:34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(53, '12', 'User Logged Out', '2026-05-15 00:00:00', '2026-05-15 06:31:53', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(54, '15', 'User Logged In', '2026-05-15 00:00:00', '2026-05-15 06:32:07', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'zackquixote', ''),
(55, '15', 'User Logged In', '2026-05-15 00:00:00', '2026-05-15 06:33:05', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'zackquixote', ''),
(56, '15', 'Bulk Uploaded Residents: 1 inserted, 0 skipped.', '2026-05-15 00:00:00', '2026-05-15 07:06:26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'zackquixote', ''),
(57, '15', 'Bulk Uploaded Residents: 0 inserted, 1 skipped.', '2026-05-15 00:00:00', '2026-05-15 07:07:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'zackquixote', ''),
(58, '12', 'User Logged In', '2026-05-17 00:00:00', '2026-05-17 16:46:49', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(59, '12', 'User Logged Out', '2026-05-17 00:00:00', '2026-05-17 16:47:38', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(60, '122', 'User Logged In', '2026-05-17 00:00:00', '2026-05-17 16:52:41', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, ''),
(61, '122', 'User Logged Out', '2026-05-17 00:00:00', '2026-05-17 16:53:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, ''),
(62, '122', 'User Logged In', '2026-05-17 00:00:00', '2026-05-17 16:55:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, ''),
(63, '122', 'Generated Certificate of Indigency (INDIG-2026-0001) for francisco tulfo', '2026-05-17 00:00:00', '2026-05-17 16:57:23', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, ''),
(64, '122', 'Created Household HH-2026-004 with 1 member(s)', '2026-05-17 00:00:00', '2026-05-17 16:58:55', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, 'household');

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
(9, NULL, '', '$2y$10$Xv57FAvSxnip8apDXF3rmutrLIESHcAHYVzQMKgMf2tu6GknL4Plm', 'Admin', 'Active', 'Glenn Azuelo', '09125110476', '2025-05-23 23:00:28', '2026-05-17 08:51:40', '2025-05-23 15:00:28'),
(11, '5cf17985-33c3-11f1-b689-bf1b47148d8a', 'admin@bmis.com', '$2y$10$6CIUIddpj8cV/B.s47Wj0.tyWLP21Hxq/UCekNFZ4D.tgnS512e3C', 'admin', 'active', 'Admin User', NULL, '2026-04-09 03:22:39', '2026-05-17 08:50:00', '2026-05-14 12:03:45'),
(12, '5cf9cf4f-33c3-11f1-b689-bf1b47148d8a', 'staff@bmis.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', 'active', 'Staff User', NULL, '2026-04-09 03:22:39', '2026-04-09 03:26:46', NULL),
(122, '1212', 'lourdianrentinoakol@gmail.com', '$2y$10$DUW8WahXAEEnwiBIcOUQau/Iy0eh/whA3cRbYPyWXeGNZsb09.Z7u', 'admin', 'active', 'johndoe', '', '2026-05-17 08:52:32', '2026-05-17 08:56:36', NULL);

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
-- Indexes for table `resident_transfer_history`
--
ALTER TABLE `resident_transfer_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `certificate_types`
--
ALTER TABLE `certificate_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `households`
--
ALTER TABLE `households`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `officials`
--
ALTER TABLE `officials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `resident_transfer_history`
--
ALTER TABLE `resident_transfer_history`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_logs`
--
ALTER TABLE `tbl_logs`
  MODIFY `LOGID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

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
  ADD CONSTRAINT `fk_cert_resident` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_certificates_resident` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE;

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
