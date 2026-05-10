-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2026 at 02:55 AM
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
  `captain_id` int(11) DEFAULT NULL,
  `secretary_id` int(11) DEFAULT NULL,
  `treasurer_id` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangay_settings`
--

INSERT INTO `barangay_settings` (`id`, `barangay_name`, `municipality`, `province`, `contact_number`, `captain_id`, `secretary_id`, `treasurer_id`, `updated_at`, `created_at`) VALUES
(1, 'himamaylan', 'Ilog City', 'Negros Occidental', '09633003878', 1, 2, 3, '2026-05-08 06:58:19', '2026-04-29 18:22:30');

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

--
-- Dumping data for table `blotter_hearings`
--

INSERT INTO `blotter_hearings` (`id`, `blotter_id`, `hearing_date`, `hearing_time`, `venue`, `presiding_officer`, `notes`, `outcome`, `status`, `notification_sent`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 2, '2026-05-06', '12:12:00', 'baranggay hall', 'helloo', 'hello trhis is just a test', NULL, 'Scheduled', 0, 11, '2026-05-01 11:44:49', '2026-05-01 11:44:49');

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

--
-- Dumping data for table `blotter_records`
--

INSERT INTO `blotter_records` (`id`, `case_number`, `incident_type`, `incident_date`, `incident_location`, `purok`, `details`, `status`, `action_taken`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(2, 'BLT-2026-0001', '', '0000-00-00', NULL, NULL, '', 'Pending', 'imissyouusomuch baby\r\nmay gn ', 11, 11, '2026-05-01 10:46:57', '2026-05-09 18:52:58');

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

--
-- Dumping data for table `blotter_timeline`
--

INSERT INTO `blotter_timeline` (`id`, `blotter_id`, `old_status`, `new_status`, `remarks`, `created_by`, `created_at`) VALUES
(1, 2, 'Pending', 'Investigating', 'imissyouusomuch baby\r\n', 11, '2026-05-01 11:46:27'),
(2, 2, 'Investigating', 'Pending', 'imissyouusomuch baby\r\nmay gn ', 11, '2026-05-06 18:19:04');

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
(1, 'RESID-2026-0001', 8, 'Certificate of Residency', 'sasas', NULL, '2026-04-29 12:02:13', '2026-05-01 12:24:39'),
(2, 'INDIG-2026-0002', 4, 'Certificate of Indigency', 'sdadsada', 11, '2026-04-29 12:11:16', '2026-05-01 12:24:39'),
(3, 'INDIG-2026-0003', 4, 'Certificate of Indigency', 'sadsa', 11, '2026-04-29 12:11:39', '2026-05-01 12:24:39'),
(4, 'INDIG-2026-0004', 7, 'Certificate of Indigency', 'lourdia', 11, '2026-04-29 12:17:20', '2026-05-01 12:24:39'),
(5, 'INDIG-2026-0005', 7, 'Certificate of Indigency', 'sada', 11, '2026-04-29 12:26:34', '2026-05-01 12:24:39'),
(6, 'INDIG-2026-0006', 2, 'Certificate of Indigency', 'lourdian\r\n', 11, '2026-04-29 12:52:41', '2026-05-01 12:24:39'),
(7, 'CLEAR-2026-0007', 7, 'Barangay Clearance', 'employment', 11, '2026-04-29 18:27:01', '2026-05-01 12:24:39'),
(8, 'RESID-2026-0002', 7, 'Certificate of Residency', 'enrollment', 11, '2026-05-05 09:59:06', NULL),
(11, 'CLEAR-2026-0002', 4, 'Barangay Clearance', 'school', 11, '2026-05-09 18:40:26', NULL),
(12, 'CLEAR-2026-0003', 5, 'Barangay Clearance', 'employment', 11, '2026-05-09 18:42:36', NULL);

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
(1, 'Barangay Clearance', 'CLEAR', '<div style=\"font-family: \'Times New Roman\', serif; text-align: center; padding: 50px;\">\r\n    <h2>BARANGAY CLEARANCE</h2>\r\n    <p style=\"text-align: right;\">{date_issued}</p>\r\n    <br>\r\n    <p>TO WHOM IT MAY CONCERN:</p>\r\n    <p>This is to certify that <strong>{resident_name}</strong>, <strong>{age}</strong> years old, <strong>{civil_status}</strong>, is a bonafide resident of <strong>{barangay_name}</strong>, {municipality}.</p>\r\n    <p>This certification is issued upon the request of the interested party for <strong>{purpose}</strong> purposes.</p>\r\n    <br><br>\r\n    <p>Issued this <strong>{date_issued}</strong> at {barangay_name}.</p>\r\n    <br><br><br>\r\n    <p><strong>{captain_name}</strong></p>\r\n    <p>Punong Barangay</p>\r\n</div>', 1, '2026-04-29 12:28:50', '2026-04-29 12:28:50'),
(5, 'Certificate of Indigency', 'INDIG', '<div style=\'font-family: Times New Roman, serif; text-align: center; padding: 50px;\'>\r\n    <h2>CERTIFICATE OF INDIGENCY</h2>\r\n    <p style=\'text-align: right;\'>{date_issued}</p>\r\n    <br>\r\n    <p>TO WHOM IT MAY CONCERN:</p>\r\n    <p>This is to certify that <strong>{resident_name}</strong>, <strong>{age}</strong> years old, <strong>{civil_status}</strong>, is a resident of <strong>{barangay_name}</strong>, {municipality}.</p>\r\n    <p>Based on available records, certification is hereby issued to <strong>{resident_name}</strong> that they are indigent and of limited financial capability.</p>\r\n    <p>This certification is issued upon the request of the interested party for <strong>{purpose}</strong> purposes.</p>\r\n    <br><br>\r\n    <p>Issued this <strong>{date_issued}</strong> at {barangay_name}.</p>\r\n    <br><br><br>\r\n    <p><strong>{captain_name}</strong></p>\r\n    <p>Punong Barangay</p>\r\n</div>', 1, '2026-04-29 12:39:43', '2026-04-29 12:39:43'),
(8, 'Solo Parent', NULL, '<div style=\'font-family: Times New Roman, serif; text-align: center; padding: 50px;\'>\r\n    <h2>CERTIFICATE OF SOLO PARENT</h2>\r\n    <p style=\'text-align: right;\'>{date_issued}</p>\r\n    <br>\r\n    <p>TO WHOM IT MAY CONCERN:</p>\r\n    <p>This is to certify that <strong>{resident_name}</strong>, <strong>{age}</strong> years old, <strong>{civil_status}</strong>, is a resident of <strong>{barangay_name}</strong>, {municipality}.</p>\r\n    <p>That he/she is a Solo Parent as defined under RA 8972 (Solo Parents\' Welfare Act of 2000) and is entitled to benefits provided under the law.</p>\r\n    <p>This certification is issued upon the request of the interested party for <strong>{purpose}</strong> purposes.</p>\r\n    <br><br>\r\n    <p>Issued this <strong>{date_issued}</strong> at {barangay_name}.</p>\r\n    <br><br><br>\r\n    <p><strong>{captain_name}</strong></p>\r\n    <p>Punong Barangay</p>\r\n</div>', 1, '2026-04-29 12:39:43', '2026-04-29 12:39:43'),
(9, 'Business Permit', NULL, '<div style=\'font-family: Times New Roman, serif; text-align: center; padding: 50px;\'>\r\n    <h2>BARANGAY BUSINESS PERMIT</h2>\r\n    <p style=\'text-align: right;\'>{date_issued}</p>\r\n    <br>\r\n    <p>OWNER: <strong>{resident_name}</strong></p>\r\n    <p>ADDRESS: <strong>{address}, {barangay_name}, {municipality}</strong></p>\r\n    <br>\r\n    <p>This is to certify that the above-named person is hereby authorized to operate a business within the jurisdiction of this Barangay.</p>\r\n    <p>Location/Type of Business: <strong>{purpose}</strong></p>\r\n    <br><br>\r\n    <p>Issued this <strong>{date_issued}</strong> at {barangay_name}.</p>\r\n    <br><br><br>\r\n    <p><strong>{captain_name}</strong></p>\r\n    <p>Punong Barangay</p>\r\n</div>', 1, '2026-04-29 12:42:39', '2026-04-29 12:42:39'),
(10, 'Certificate of Residency', NULL, '<div style=\'font-family: Times New Roman, serif; text-align: center; padding: 50px;\'>\r\n    <h2>CERTIFICATE OF RESIDENCY</h2>\r\n    <p style=\'text-align: right;\'>{date_issued}</p>\r\n    <br>\r\n    <p>TO WHOM IT MAY CONCERN:</p>\r\n    <p>This is to certify that <strong>{resident_name}</strong>, <strong>{age}</strong> years old, <strong>{civil_status}</strong>, is a bona fide resident of <strong>{barangay_name}</strong>, {municipality}.</p>\r\n    <p>This certification is issued upon the request of the interested party for <strong>{purpose}</strong> purposes.</p>\r\n    <br><br>\r\n    <p>Issued this <strong>{date_issued}</strong> at {barangay_name}.</p>\r\n    <br><br><br>\r\n    <p><strong>{captain_name}</strong></p>\r\n    <p>Punong Barangay</p>\r\n</div>', 1, '2026-04-29 12:42:40', '2026-04-29 12:42:40');

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
  `house_type` enum('Concrete','Semi-Concrete','Wood','Light Materials') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `households`
--

INSERT INTO `households` (`id`, `household_no`, `address`, `street_address`, `head_resident_id`, `created_at`, `sitio`, `house_type`) VALUES
(77, 'HH-2026-001', '', 'Near Chapel', 1, '2026-04-21 05:24:08', 'Purok Malipayon', 'Concrete'),
(82, 'HH-2026-002', 'Purok Malipayon, , Barangay Tabu, Negros Occidental', '', 2, '2026-05-05 23:55:09', 'Purok Malipayon', 'Semi-Concrete');

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

--
-- Dumping data for table `officials`
--

INSERT INTO `officials` (`id`, `resident_id`, `position`, `full_name`, `contact_number`, `photo`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Punong Barangay', 'Lourd dela cruz', NULL, NULL, 1, '2026-05-01 11:49:27', '2026-05-08 14:58:19'),
(2, 2, 'Secretary', 'maria dela cruz', NULL, NULL, 1, '2026-05-01 11:49:27', '2026-05-08 14:58:20'),
(3, 3, 'Treasurer', 'luffy monkey', NULL, NULL, 1, '2026-05-01 11:49:27', '2026-05-08 14:58:20');

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
  `registered_by` int(11) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`id`, `household_id`, `is_household_head`, `joined_household_date`, `left_household_date`, `first_name`, `middle_name`, `last_name`, `birthdate`, `sex`, `civil_status`, `sitio`, `contact_number`, `occupation`, `citizenship`, `profile_picture`, `relationship_to_head`, `is_voter`, `is_senior_citizen`, `is_pwd`, `status`, `registered_by`, `registered_at`, `updated_at`, `deleted_at`, `created_at`) VALUES
(1, NULL, 0, NULL, '2026-04-26', 'Lourd', 'arroyo', 'dela cruz', '1982-02-24', 'male', '', 'Purok Malipayon', NULL, 'teacher', 'Filipino', 'purok_malipayon/1776746495_d562a2a2005cb8a8ad45.jpg', 'Head', 1, 1, 1, 'active', 12, '2026-04-21 04:41:35', '2026-05-06 02:00:31', NULL, '2026-04-21 12:41:35'),
(2, 82, 1, '2026-05-06', '2026-04-26', 'maria', 'golez', 'dela cruz', '1982-12-12', 'female', 'married', 'Purok Malipayon', '93948209', 'house wife', 'Filipino', 'purok_malipayon/1776746579_a4dd4e95476441d30dad.jpg', 'Mother', 1, 1, 0, 'active', 12, '2026-04-21 04:42:59', '2026-05-05 23:55:09', NULL, '2026-04-21 12:42:59'),
(3, 77, 0, NULL, '2026-04-26', 'luffy', 'd', 'monkey', '2005-11-11', 'male', 'single', 'Purok Malipayon', '93948209', 'student', 'Filipino', 'purok_malipayon/1776751759_16f114754ac8250dae2e.jpg', 'Son', 1, 0, 0, 'active', 12, '2026-04-21 06:09:19', '2026-05-06 01:18:43', NULL, '2026-04-21 14:09:19'),
(4, 82, 0, '2026-05-06', '2026-04-26', 'maria', 'rentino', 'akol', '2005-11-02', 'female', 'single', 'Purok Malipayon', '93948209', 'student', 'Filipino', 'purok_malipayon/1776754904_a604f0dd294813862746.jpg', 'Head', 1, 0, 0, 'active', 12, '2026-04-21 07:01:44', '2026-05-05 23:55:09', NULL, '2026-04-21 15:01:44'),
(5, 77, 0, NULL, '2026-04-26', 'luffy', 'woah', 'monkey', '2005-12-12', 'male', 'single', 'Purok Malipayon', '93948209', 'student', 'Filipino', 'purok_malipayon/1776755172_384cf6dd9fc7e69f241b.jpg', 'Son-in-law', 1, 0, 0, 'active', 12, '2026-04-21 07:06:12', '2026-04-26 14:03:39', NULL, '2026-04-21 15:06:12'),
(6, 77, 0, NULL, '2026-04-26', 'luffy', 'woah', 'monkey', '2005-12-12', 'male', 'single', 'Purok Malipayon', '93948209', 'student', 'Filipino', 'purok_malipayon/1776756109_28a8be27c5cbda89e849.jpg', 'Daughter-in-law', 1, 1, 1, 'active', 12, '2026-04-21 07:21:49', '2026-05-02 11:35:37', NULL, '2026-04-21 15:21:49'),
(7, 77, 0, NULL, NULL, 'maria ', 'wowowin', 'akol', '2000-11-12', 'female', 'married', 'Purok Kawayan', NULL, 'nurse', 'Filipino', 'purok_kawayan/1776927359_4cd902733561f3e07814.jpg', 'Daughter', 1, 0, 0, 'active', 12, '2026-04-23 06:55:59', '2026-05-06 03:46:42', NULL, '2026-04-23 14:55:59'),
(8, 77, 0, NULL, NULL, 'luffy', 'woah', 'monkey', '2005-12-12', 'male', 'single', 'Purok Malipayon', NULL, 'house wife', 'Filipino', NULL, 'Niece', 1, 0, 0, 'active', 11, '2026-04-28 18:37:04', '2026-04-29 07:50:21', '2026-04-29 07:50:21', '2026-04-29 02:37:04'),
(9, 77, 0, NULL, NULL, 'luffy', 'woah', 'monkey', '2005-12-12', 'male', 'single', 'Purok Malipayon', NULL, 'student', 'Filipino', NULL, 'Head', 0, 1, 0, 'active', 12, '2026-04-28 18:55:25', '2026-04-29 08:06:30', '2026-04-29 08:06:30', '2026-04-29 02:55:25'),
(10, 77, 0, NULL, NULL, 'luffy', 'woah', 'monkey', '2005-12-12', 'male', 'single', 'Purok Malipayon', NULL, 'student', 'Filipino', NULL, 'Head', 0, 1, 0, 'active', 12, '2026-04-28 18:56:51', '2026-04-29 08:06:20', '2026-04-29 08:06:20', '2026-04-29 02:56:51'),
(11, 77, 0, NULL, NULL, 'luffy', 'woah', 'monkey', '2005-12-12', 'male', 'single', 'Purok Malipayon', NULL, 'student', 'Filipino', NULL, 'Head', 0, 1, 0, 'active', 12, '2026-04-28 18:56:54', '2026-04-29 08:06:25', '2026-04-29 08:06:25', '2026-04-29 02:56:54'),
(12, 77, 0, NULL, NULL, 'luffy', 'woah', 'monkey', '2005-12-12', 'male', 'single', 'Purok Malipayon', NULL, 'student', 'Filipino', NULL, 'Head', 0, 1, 0, 'active', 12, '2026-04-28 19:01:36', '2026-04-29 08:06:15', '2026-04-29 08:06:15', '2026-04-29 03:01:36'),
(13, 77, 0, NULL, NULL, 'luffy', 'woah', 'monkey', '2005-12-12', 'male', 'single', 'Purok Malipayon', NULL, 'student', 'Filipino', NULL, 'Head', 0, 1, 0, 'active', 12, '2026-04-28 19:04:37', '2026-04-29 07:50:15', '2026-04-29 07:50:15', '2026-04-29 03:04:37'),
(14, NULL, 0, NULL, NULL, 'luffy', 'rentino', 'monkey', '2005-12-12', 'male', 'single', 'Purok Malipayon', NULL, 'student', 'Filipino', NULL, 'Daughter-in-law', 1, 0, 0, 'active', 11, '2026-04-29 07:49:57', '2026-04-29 07:50:09', '2026-04-29 07:50:09', '2026-04-29 15:49:57');

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
(1, '1', 'New User has been apdated: Glenn Azuelo', '2025-07-21 00:00:00', '2020-11-13 00:00:00', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', NULL, 'UPDATED'),
(2, '1', 'Logout', '2025-07-21 00:00:00', '2020-12-03 00:00:00', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', NULL, 'LOGOUT'),
(3, '1', 'Login: Glenn Azuelo', '2025-07-21 00:00:00', '2020-12-16 00:00:00', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', NULL, 'LOGIN'),
(4, '1', 'Logout', '2025-07-21 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', NULL, 'LOGOUT'),
(5, '10', 'Login: Cherry Ann Grandia', '2025-07-21 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', NULL, 'LOGIN'),
(6, '10', 'New User has been apdated: Glenn Azuelo', '2025-07-21 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '10', 'UPDATED'),
(7, '10', 'New User has been apdated: Cherry Ann Grandia', '2025-07-21 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', NULL, 'UPDATED'),
(8, '10', 'Logout', '2025-07-21 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', NULL, 'LOGOUT'),
(9, '1', 'Login: Glenn Azuelo', '2025-07-21 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(10, '1', 'Logout', '2025-07-21 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGOUT'),
(11, '1', 'Login: Glenn Azuelo', '2025-07-21 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(12, '1', 'New User has been added: xxx', '2025-07-21 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(13, '1', 'Delete user', '2025-07-21 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETED'),
(14, '1', 'Login: Glenn Azuelo', '2026-02-17 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(15, '1', 'New Person has been added: lourd ian', '2026-02-17 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(16, '1', 'New Person has been added: joshua garia ', '2026-02-17 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(17, '1', 'Delete user', '2026-02-17 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETED'),
(18, '1', 'New Person has been added: joshua garia ', '2026-02-17 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(19, '1', 'New Person has been added: joshua garia ', '2026-02-17 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(20, '1', 'New Person has been added: joshua garia ', '2026-02-17 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(21, '1', 'Login: Glenn Azuelo', '2026-02-17 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(22, '1', 'Login: Glenn Azuelo', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(23, '1', 'New Person has been added: aagapito', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(24, '1', 'New Person has been updated: joshua ramos', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'UPDATED'),
(25, '1', 'New Person has been updated: joshua garnica', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'UPDATED'),
(26, '1', 'New Person has been updated: joshua boang', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'UPDATED'),
(27, '1', 'New Person has been updated: joshua marcos', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', '2003-12-12UPDATED'),
(28, '1', 'New Person has been updated: bioangmarcos', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', '2000-12-15UPDATED'),
(29, '1', 'Login: Glenn Azuelo', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(30, '1', 'Person deleted: 0', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(31, '1', 'Person deleted: 0', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(32, '1', 'Person deleted: 0', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(33, '1', 'Person deleted: 0', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(34, '1', 'Person deleted: 0', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(35, '1', 'Person deleted: 0', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(36, '1', 'Person deleted: 0', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(37, '1', 'Person deleted: 0', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(38, '1', 'Person deleted: 0', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(39, '1', 'Person deleted: 0', '2026-02-20 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(40, '1', 'Login: Glenn Azuelo', '2026-02-21 00:00:00', '2017-04-30 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(41, '1', 'Login: Glenn Azuelo', '2026-02-22 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(42, '1', 'Login: Glenn Azuelo', '2026-02-22 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(43, '1', 'New Person has been added: aagapito', '2026-02-22 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(44, '1', 'New Person has been added: lourd ian', '2026-02-22 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(45, '1', 'Person deleted: 1', '2026-02-22 00:00:00', '2016-01-17 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'DELETE'),
(46, '1', 'Person updated: lourd ian akol', '2026-02-22 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'UPDATED'),
(47, '1', 'New Person has been added: joshua ramos', '2026-02-22 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(48, '1', 'Login: Glenn Azuelo', '2026-02-23 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(49, '1', 'Login: Glenn Azuelo', '2026-03-02 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(50, '1', 'Person updated: lourd ian akol lo', '2026-03-02 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'UPDATED'),
(51, '1', 'Login: Glenn Azuelo', '2026-03-02 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(52, '1', 'New Person has been added: aagapito', '2026-03-02 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Glenn Azuelo', 'ADD'),
(53, '1', 'Login: Glenn Azuelo', '2026-03-31 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(54, '1', 'Logout', '2026-03-31 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGOUT'),
(55, '1', 'Login: Glenn Azuelo', '2026-03-31 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(56, '1', 'Logout', '2026-03-31 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGOUT'),
(57, '1', 'Login: Glenn Azuelo', '2026-03-31 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(58, '1', 'Logout', '2026-03-31 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGOUT'),
(59, '1', 'Login: Glenn Azuelo as User', '2026-04-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Glenn Azuelo', 'LOGIN'),
(60, '11', 'Login: Admin User', '2026-04-09 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Admin User', 'LOGIN'),
(61, '11', 'Logout', '2026-04-09 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Admin User', 'LOGOUT'),
(62, '12', 'Login: Staff User', '2026-04-09 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(63, '11', 'Login: Admin User', '2026-04-09 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'Admin User', 'LOGIN'),
(64, '12', 'Logout', '2026-04-09 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGOUT'),
(65, '11', 'Login: Admin User', '2026-04-09 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Admin User', 'LOGIN'),
(66, '12', 'Login: Staff User', '2026-04-09 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(67, '12', 'Login: Staff User', '2026-04-10 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(68, '12', 'Logout', '2026-04-10 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGOUT'),
(69, '11', 'Login: Admin User', '2026-04-10 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Admin User', 'LOGIN'),
(70, '11', 'Logout', '2026-04-10 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Admin User', 'LOGOUT'),
(71, NULL, 'Logout', '2026-04-10 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, 'LOGOUT'),
(72, '12', 'Login: Staff User', '2026-04-10 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(73, '11', 'Login: Admin User', '2026-04-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Admin User', 'LOGIN'),
(74, '12', 'Login: Staff User', '2026-04-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(75, '12', 'Login: Staff User', '2026-04-11 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(76, '12', 'Person deleted: 2', '2026-04-11 00:00:00', '2020-05-24 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'DELETE'),
(77, '12', 'Login: Staff User', '2026-04-12 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(78, '12', 'Login: Staff User', '2026-04-12 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(79, '12', 'Login: Staff User', '2026-04-12 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(80, '12', 'Login: Staff User', '2026-04-12 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(81, '12', 'New Person has been added: joshua garia ', '2026-04-12 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'ADD'),
(82, '12', 'Login: Staff User', '2026-04-13 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(83, '12', 'Login: Staff User', '2026-04-13 00:00:00', '2018-04-20 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(84, '12', 'Login: Staff User', '2026-04-16 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(85, '12', 'Login: Staff User', '2026-04-16 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(86, '12', 'Login: Staff User', '2026-04-17 00:00:00', '2000-08-24 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(87, '12', 'Login: Staff User', '2026-04-17 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(88, '12', 'Login: Staff User', '2026-04-17 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(89, '12', 'Login: Staff User', '2026-04-17 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(90, '12', 'Login: Staff User', '2026-04-18 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(91, '12', 'Login: Staff User', '2026-04-18 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', 'LOGIN'),
(92, '11', 'User Logged Out', '2026-04-29 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(93, '12', 'User Logged In', '2026-04-29 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', ''),
(94, '12', 'Added Resident luffy monkey', '2026-04-29 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', ''),
(95, '11', 'User Logged In', '2026-04-29 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(96, '11', 'User Logged In', '2026-04-29 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(97, '11', 'Generated Certificate of Residency for luffy monkey', '2026-04-29 00:00:00', '2012-02-13 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(98, '11', 'Generated Certificate of Indigency for maria akol', '2026-04-29 00:00:00', '2012-11-16 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(99, '11', 'Generated Certificate of Indigency for maria akol', '2026-04-29 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(100, '11', 'Generated Certificate of Indigency for maria  akol', '2026-04-29 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(101, '11', 'Generated Certificate of Indigency for maria  akol', '2026-04-29 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(102, '11', 'Generated Certificate of Indigency for maria dela cruz', '2026-04-29 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(103, '11', 'Added Resident luffy monkey', '2026-04-29 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(104, '11', 'Generated Barangay Clearance for maria  akol', '2026-04-29 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(105, '12', 'User Logged In', '2026-04-30 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', ''),
(106, '12', 'User Logged Out', '2026-04-30 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', ''),
(107, '11', 'User Logged In', '2026-04-30 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(108, '12', 'User Logged In', '2026-04-30 00:00:00', '2009-07-27 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', ''),
(109, '11', 'User Logged In', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(110, '11', 'User Logged Out', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(111, '11', 'User Logged In', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(112, '11', 'User Logged Out', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(113, '11', 'User Logged In', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(114, '11', 'Deleted blotter case ', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(115, '11', 'Created blotter case BLT-2026-0001', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(116, '11', 'Updated blotter case BLT-2026-0001', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(117, '11', 'Added hearing for case BLT-2026-0001', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(118, '11', 'Updated blotter case BLT-2026-0001', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(119, '11', 'Updated status of fernando dela cruz to inactive', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(120, '11', 'Updated status of fernando dela cruz to deceased', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(121, '11', 'Updated status of fernando dela cruz to inactive', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(122, '11', 'Updated status of fernando dela cruz to active', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(123, '11', 'Updated status of fernando dela cruz to active', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(124, '11', 'Updated membership status of fernando dela cruz to Active', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(125, '11', 'User Logged Out', '2026-05-01 00:00:00', '2014-04-14 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(126, '11', 'User Logged In', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(127, '11', 'User Logged Out', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(128, '11', 'User Logged In', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(129, '11', 'User Logged Out', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(130, '12', 'User Logged In', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', ''),
(131, '12', 'User Logged Out', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', ''),
(132, '11', 'User Logged In', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(133, '11', 'User Logged Out', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(134, '12', 'User Logged In', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', ''),
(135, '12', 'User Logged Out', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', ''),
(136, '11', 'User Logged In', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(137, '11', 'User Logged Out', '2026-05-01 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(138, '11', 'User Logged In', '2026-05-02 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(139, '12', 'User Logged In', '2026-05-02 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', ''),
(140, '11', 'Updated membership status of luffy monkey to Transferred', '2026-05-02 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(141, '11', 'Updated membership status of luffy monkey to Active', '2026-05-02 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(142, '11', 'User Logged Out', '2026-05-02 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(143, '11', 'User Logged In', '2026-05-05 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(144, '11', 'User Logged Out', '2026-05-05 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(145, '11', 'User Logged In', '2026-05-05 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(146, '11', 'Generated Certificate of Residency (RESID-2026-0002) for maria  akol', '2026-05-05 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(147, '11', 'Updated blotter case BLT-2026-0001', '2026-05-05 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(148, '11', 'User Logged Out', '2026-05-05 00:00:00', '2010-04-23 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(149, '11', 'User Logged In', '2026-05-05 00:00:00', '2010-05-11 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(150, '11', 'Updated membership status of maria dela cruz to Deceased', '2026-05-05 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(151, '11', 'User Logged In', '2026-05-05 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(152, '11', 'User Logged Out', '2026-05-05 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(153, '12', 'User Logged In', '2026-05-05 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', ''),
(154, '12', 'User Logged Out', '2026-05-05 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Staff User', ''),
(155, '11', 'User Logged In', '2026-05-05 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(156, '11', 'User Logged Out', '2026-05-05 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(157, '11', 'User Logged In', '2026-05-05 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(158, '11', 'User Logged In', '2026-05-05 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(159, '11', 'User Logged In', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'Admin User', ''),
(160, '11', 'Updated status of maria  akol to inactive', '2026-05-06 00:00:00', '2008-02-28 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(161, '11', 'Updated status of maria  akol to deceased', '2026-05-06 00:00:00', '2008-04-28 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(162, '11', 'Updated status of fernando dela cruz to inactive', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(163, '11', 'Updated membership status of luffy monkey to Transferred', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(164, '11', 'Updated membership status of luffy monkey to Active', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(165, '11', 'Updated status of fernando dela cruz to active', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Admin User', ''),
(166, '12', 'User Logged In', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(167, '11', 'User Logged Out', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(168, '12', 'User Logged In', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(169, '12', 'User Logged Out', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(170, '11', 'User Logged In', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(171, '11', 'User Logged Out', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(172, '11', 'User Logged In', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(173, '11', 'Updated status of maria  akol to active', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(174, '11', 'Updated membership status of maria  akol to Transferred', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(175, '11', 'Updated status of maria  akol to active', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(176, '11', 'User Logged In', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(177, '11', 'Updated blotter case BLT-2026-0001', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(178, '11', 'User Logged Out', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(179, '11', 'User Logged In', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(180, '11', 'User Logged In', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(181, '11', 'User Logged Out', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(182, '11', 'User Logged In', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(183, '11', 'User Logged In', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(184, '11', 'User Logged Out', '2026-05-06 00:00:00', '2020-11-04 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(185, '11', 'User Logged In', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(186, '11', 'User Logged Out', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(187, '12', 'User Logged In', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(188, '12', 'User Logged Out', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(189, '11', 'User Logged In', '2026-05-06 00:00:00', '2021-06-07 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(190, '11', 'User Logged In', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(191, '11', 'User Logged Out', '2026-05-06 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(192, '11', 'Login: Admin User', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', 'LOGIN'),
(193, '11', 'Logout', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', 'LOGOUT'),
(194, '11', 'Login: Admin User', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', 'LOGIN'),
(195, '11', 'Logout', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', 'LOGOUT'),
(196, '11', 'Login: Admin User', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', 'LOGIN'),
(197, '11', 'User Logged In', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(198, '11', 'User Logged In', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(199, '11', 'User Logged Out', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(200, '11', 'User Logged In', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(201, '11', 'User Logged Out', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(202, '12', 'User Logged In', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(203, '12', 'User Logged Out', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Staff User', ''),
(204, '11', 'User Logged In', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(205, '11', 'User Logged Out', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(206, '11', 'User Logged In', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(207, '11', 'User Logged In', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(208, '11', 'User Logged In', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.8328', 'Admin User', ''),
(209, '11', 'User Logged In', '2026-05-07 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.8328', 'Admin User', ''),
(210, '11', 'User Logged In', '2026-05-08 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(211, '11', 'User Logged Out', '2026-05-08 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(212, '11', 'User Logged In', '2026-05-09 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(213, '11', 'User Logged In', '2026-05-09 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(214, '11', 'User Logged In', '2026-05-09 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(215, '11', 'Generated Barangay Clearance (CLEAR-2026-0002) for maria akol', '2026-05-09 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(216, '11', 'Generated Barangay Clearance (CLEAR-2026-0003) for luffy monkey', '2026-05-09 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(217, '11', 'Updated blotter case BLT-2026-0001', '2026-05-09 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(218, '11', 'User Logged Out', '2026-05-09 00:00:00', '2019-09-04 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(219, '11', 'User Logged In', '2026-05-09 00:00:00', '2019-09-17 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(220, '11', 'User Logged In', '2026-05-09 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(221, '11', 'User Logged In', '2026-05-09 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', ''),
(222, '11', 'User Logged In', '2026-05-10 00:00:00', '0000-00-00 00:00:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Admin User', '');

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
(12, '5cf9cf4f-33c3-11f1-b689-bf1b47148d8a', 'staff@bmis.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', 'active', 'Staff User', NULL, '2026-04-09 03:22:39', '2026-04-09 03:26:46', NULL),
(14, NULL, 'lourdianrentinoakol123@gmail.com', '$2y$10$3EGfU0svcZI/8yTsgUo6G.UU8pH8S3PFcLE.pqke4OtFxEQkc0v2S', 'admin', 'inactive', 'luffy monkey', '93948209', '2026-04-26 12:15:38', '2026-05-07 12:47:08', '2026-05-07 12:47:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barangay_settings`
--
ALTER TABLE `barangay_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_brgy_captain` (`captain_id`),
  ADD KEY `fk_brgy_secretary` (`secretary_id`),
  ADD KEY `fk_brgy_treasurer` (`treasurer_id`);

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
  ADD KEY `fk_blotter_updatedby` (`updated_by`);

--
-- Indexes for table `blotter_timeline`
--
ALTER TABLE `blotter_timeline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_blotter` (`blotter_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_cert_number` (`certificate_number`),
  ADD KEY `fk_cert_createdby` (`created_by`);

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
  ADD KEY `registered_by` (`registered_by`),
  ADD KEY `idx_household` (`household_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_household_members` (`household_id`),
  ADD KEY `idx_relationship` (`relationship_to_head`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blotter_parties`
--
ALTER TABLE `blotter_parties`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `blotter_records`
--
ALTER TABLE `blotter_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blotter_timeline`
--
ALTER TABLE `blotter_timeline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `certificate_types`
--
ALTER TABLE `certificate_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `households`
--
ALTER TABLE `households`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `officials`
--
ALTER TABLE `officials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbl_logs`
--
ALTER TABLE `tbl_logs`
  MODIFY `LOGID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barangay_settings`
--
ALTER TABLE `barangay_settings`
  ADD CONSTRAINT `fk_brgy_captain` FOREIGN KEY (`captain_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_brgy_secretary` FOREIGN KEY (`secretary_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_brgy_treasurer` FOREIGN KEY (`treasurer_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `fk_timeline_blotter` FOREIGN KEY (`blotter_id`) REFERENCES `blotter_records` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `fk_cert_createdby` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
