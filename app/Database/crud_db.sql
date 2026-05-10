-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: crud_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `barangay_settings`
--

DROP TABLE IF EXISTS `barangay_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `barangay_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barangay_name` varchar(100) DEFAULT NULL,
  `municipality` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barangay_settings`
--

LOCK TABLES `barangay_settings` WRITE;
/*!40000 ALTER TABLE `barangay_settings` DISABLE KEYS */;
INSERT INTO `barangay_settings` VALUES (1,'tabu','Ilog City','Negros Occidental','09633003878','2026-05-10 02:56:21','2026-04-29 18:22:30');
/*!40000 ALTER TABLE `barangay_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blotter_hearings`
--

DROP TABLE IF EXISTS `blotter_hearings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blotter_hearings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_blotter` (`blotter_id`),
  KEY `fk_hearing_createdby` (`created_by`),
  CONSTRAINT `fk_hearing_createdby` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_hearings_blotter` FOREIGN KEY (`blotter_id`) REFERENCES `blotter_records` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blotter_hearings`
--

LOCK TABLES `blotter_hearings` WRITE;
/*!40000 ALTER TABLE `blotter_hearings` DISABLE KEYS */;
/*!40000 ALTER TABLE `blotter_hearings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blotter_parties`
--

DROP TABLE IF EXISTS `blotter_parties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blotter_parties` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `blotter_id` int(11) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `outsider_name` varchar(150) DEFAULT NULL,
  `outsider_address` varchar(255) DEFAULT NULL,
  `role` enum('complainant','respondent','witness') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_blotter` (`blotter_id`),
  KEY `idx_resident` (`resident_id`),
  CONSTRAINT `fk_blotter_parties_blotter` FOREIGN KEY (`blotter_id`) REFERENCES `blotter_records` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_blotter_parties_resident` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blotter_parties`
--

LOCK TABLES `blotter_parties` WRITE;
/*!40000 ALTER TABLE `blotter_parties` DISABLE KEYS */;
/*!40000 ALTER TABLE `blotter_parties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blotter_records`
--

DROP TABLE IF EXISTS `blotter_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blotter_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_case_number` (`case_number`),
  KEY `fk_blotter_createdby` (`created_by`),
  KEY `fk_blotter_updatedby` (`updated_by`),
  KEY `idx_status` (`status`),
  KEY `idx_incident_date` (`incident_date`),
  CONSTRAINT `fk_blotter_createdby` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_blotter_updatedby` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blotter_records`
--

LOCK TABLES `blotter_records` WRITE;
/*!40000 ALTER TABLE `blotter_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `blotter_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blotter_timeline`
--

DROP TABLE IF EXISTS `blotter_timeline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blotter_timeline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blotter_id` int(11) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_blotter` (`blotter_id`),
  KEY `fk_timeline_createdby` (`created_by`),
  CONSTRAINT `fk_timeline_blotter` FOREIGN KEY (`blotter_id`) REFERENCES `blotter_records` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_timeline_createdby` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blotter_timeline`
--

LOCK TABLES `blotter_timeline` WRITE;
/*!40000 ALTER TABLE `blotter_timeline` DISABLE KEYS */;
/*!40000 ALTER TABLE `blotter_timeline` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `certificate_types`
--

DROP TABLE IF EXISTS `certificate_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `certificate_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL COMMENT 'Must match enum value in certificates table',
  `prefix` varchar(10) DEFAULT NULL,
  `content` text DEFAULT NULL COMMENT 'HTML Template Content',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `certificate_types`
--

LOCK TABLES `certificate_types` WRITE;
/*!40000 ALTER TABLE `certificate_types` DISABLE KEYS */;
INSERT INTO `certificate_types` VALUES (1,'Barangay Clearance','CLEAR','<div style=\"font-family: \'Times New Roman\', serif; text-align: justify; padding: 20px 50px;\">\n    <p style=\"text-align: right;\">{date_issued}</p>\n    <br>\n    <p>TO WHOM IT MAY CONCERN:</p>\n    <p style=\"text-indent: 50px;\">This is to certify that <strong>{resident_name}</strong>, <strong>{age}</strong> years old, <strong>{civil_status}</strong>, is a bonafide resident of <strong>{barangay_name}</strong>, {municipality}.</p>\n    <p style=\"text-indent: 50px;\">This certification is issued upon the request of the interested party for <strong>{purpose}</strong> purposes.</p>\n    <br><br>\n    <p>Issued this <strong>{date_issued}</strong> at {barangay_name}.</p>\n</div>',1,'2026-04-29 12:28:50','2026-04-29 12:28:50'),(5,'Certificate of Indigency','INDIG','<div style=\"font-family: Times New Roman, serif; text-align: justify; padding: 20px 50px;\">\n    <p style=\"text-align: right;\">{date_issued}</p>\n    <br>\n    <p>TO WHOM IT MAY CONCERN:</p>\n    <p style=\"text-indent: 50px;\">This is to certify that <strong>{resident_name}</strong>, <strong>{age}</strong> years old, <strong>{civil_status}</strong>, is a resident of <strong>{barangay_name}</strong>, {municipality}.</p>\n    <p style=\"text-indent: 50px;\">Based on available records, certification is hereby issued to <strong>{resident_name}</strong> that they are indigent and of limited financial capability.</p>\n    <p style=\"text-indent: 50px;\">This certification is issued upon the request of the interested party for <strong>{purpose}</strong> purposes.</p>\n    <br><br>\n    <p>Issued this <strong>{date_issued}</strong> at {barangay_name}.</p>\n</div>',1,'2026-04-29 12:39:43','2026-04-29 12:39:43'),(8,'Solo Parent',NULL,'<div style=\"font-family: Times New Roman, serif; text-align: justify; padding: 20px 50px;\">\n    <p style=\"text-align: right;\">{date_issued}</p>\n    <br>\n    <p>TO WHOM IT MAY CONCERN:</p>\n    <p style=\"text-indent: 50px;\">This is to certify that <strong>{resident_name}</strong>, <strong>{age}</strong> years old, <strong>{civil_status}</strong>, is a resident of <strong>{barangay_name}</strong>, {municipality}.</p>\n    <p style=\"text-indent: 50px;\">That he/she is a Solo Parent as defined under RA 8972 (Solo Parents\' Welfare Act of 2000) and is entitled to benefits provided under the law.</p>\n    <p style=\"text-indent: 50px;\">This certification is issued upon the request of the interested party for <strong>{purpose}</strong> purposes.</p>\n    <br><br>\n    <p>Issued this <strong>{date_issued}</strong> at {barangay_name}.</p>\n</div>',1,'2026-04-29 12:39:43','2026-04-29 12:39:43'),(9,'Business Permit',NULL,'<div style=\"font-family: Times New Roman, serif; text-align: justify; padding: 20px 50px;\">\n    <p style=\"text-align: right;\">{date_issued}</p>\n    <br>\n    <p>OWNER: <strong>{resident_name}</strong></p>\n    <p>ADDRESS: <strong>{address}, {barangay_name}, {municipality}</strong></p>\n    <br>\n    <p style=\"text-indent: 50px;\">This is to certify that the above-named person is hereby authorized to operate a business within the jurisdiction of this Barangay.</p>\n    <p style=\"text-indent: 50px;\">Location/Type of Business: <strong>{purpose}</strong></p>\n    <br><br>\n    <p>Issued this <strong>{date_issued}</strong> at {barangay_name}.</p>\n</div>',1,'2026-04-29 12:42:39','2026-04-29 12:42:39'),(10,'Certificate of Residency',NULL,'<div style=\"font-family: Times New Roman, serif; text-align: justify; padding: 20px 50px;\">\n    <p style=\"text-align: right;\">{date_issued}</p>\n    <br>\n    <p>TO WHOM IT MAY CONCERN:</p>\n    <p style=\"text-indent: 50px;\">This is to certify that <strong>{resident_name}</strong>, <strong>{age}</strong> years old, <strong>{civil_status}</strong>, is a bona fide resident of <strong>{barangay_name}</strong>, {municipality}.</p>\n    <p style=\"text-indent: 50px;\">This certification is issued upon the request of the interested party for <strong>{purpose}</strong> purposes.</p>\n    <br><br>\n    <p>Issued this <strong>{date_issued}</strong> at {barangay_name}.</p>\n</div>',1,'2026-04-29 12:42:40','2026-04-29 12:42:40');
/*!40000 ALTER TABLE `certificate_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `certificates`
--

DROP TABLE IF EXISTS `certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `certificates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `certificate_number` varchar(30) DEFAULT NULL,
  `resident_id` int(11) NOT NULL DEFAULT 0,
  `certificate_type` enum('Barangay Clearance','Certificate of Indigency','Certificate of Residency','Business Permit','Solo Parent') NOT NULL DEFAULT 'Barangay Clearance',
  `purpose` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_cert_number` (`certificate_number`),
  KEY `fk_cert_createdby` (`created_by`),
  KEY `idx_cert_resident` (`resident_id`),
  CONSTRAINT `fk_cert_createdby` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_cert_resident` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_certificates_resident` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `certificates`
--

LOCK TABLES `certificates` WRITE;
/*!40000 ALTER TABLE `certificates` DISABLE KEYS */;
/*!40000 ALTER TABLE `certificates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `households`
--

DROP TABLE IF EXISTS `households`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `households` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `household_no` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `head_resident_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sitio` enum('Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um') DEFAULT NULL,
  `house_type` enum('Concrete','Semi-Concrete','Wood','Light Materials') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_head_resident` (`head_resident_id`),
  CONSTRAINT `fk_head_resident` FOREIGN KEY (`head_resident_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `households`
--

LOCK TABLES `households` WRITE;
/*!40000 ALTER TABLE `households` DISABLE KEYS */;
/*!40000 ALTER TABLE `households` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` datetime NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026-04-12-125446','App\\Database\\Migrations\\AddHouseholdColumns','default','App',1775998509,1),(2,'2026-04-12-125539','App\\Database\\Migrations\\AddDeletedAtToResidents','default','App',1775998890,2),(3,'2026-04-12-133922','App\\Database\\Migrations\\AddUpdatedAtToResidents','default','App',1776001177,3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `officials`
--

DROP TABLE IF EXISTS `officials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `officials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resident_id` int(11) DEFAULT NULL,
  `position` varchar(100) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_resident` (`resident_id`),
  CONSTRAINT `fk_officials_resident` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `officials`
--

LOCK TABLES `officials` WRITE;
/*!40000 ALTER TABLE `officials` DISABLE KEYS */;
/*!40000 ALTER TABLE `officials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `residents`
--

DROP TABLE IF EXISTS `residents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `residents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_household` (`household_id`),
  KEY `idx_status` (`status`),
  KEY `idx_relationship` (`relationship_to_head`),
  KEY `residents_ibfk_2` (`registered_by`),
  KEY `idx_sitio` (`sitio`),
  CONSTRAINT `residents_ibfk_1` FOREIGN KEY (`household_id`) REFERENCES `households` (`id`) ON DELETE SET NULL,
  CONSTRAINT `residents_ibfk_2` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `residents`
--

LOCK TABLES `residents` WRITE;
/*!40000 ALTER TABLE `residents` DISABLE KEYS */;
/*!40000 ALTER TABLE `residents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_logs`
--

DROP TABLE IF EXISTS `tbl_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_logs` (
  `LOGID` int(11) NOT NULL AUTO_INCREMENT,
  `USERID` varchar(30) DEFAULT NULL,
  `ACTION` text DEFAULT NULL,
  `DATELOG` datetime DEFAULT NULL,
  `TIMELOG` datetime DEFAULT NULL,
  `user_ip_address` text DEFAULT NULL,
  `device_used` text DEFAULT NULL,
  `USER_NAME` varchar(100) DEFAULT NULL,
  `identifier` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`LOGID`),
  KEY `USERID` (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_logs`
--

LOCK TABLES `tbl_logs` WRITE;
/*!40000 ALTER TABLE `tbl_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(100) DEFAULT 'user',
  `status` varchar(100) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (9,NULL,'glennazuelo1@gmail.comd','$2y$10$Xv57FAvSxnip8apDXF3rmutrLIESHcAHYVzQMKgMf2tu6GknL4Plm','Admin','Active','Glenn Azuelo','09125110476','2025-05-23 23:00:28','2025-05-23 15:00:28','2025-05-23 15:00:28'),(10,NULL,'glennazuelo1@gmail.com1','$2y$10$PxNNhaa76.SAbFFelJU9xOZRajcVMCZkeToZ09l1FR5ll13saXu4q','Admin','Active','Cherry Ann Grandia','09125110476','2025-05-23 23:00:50','2025-07-20 20:19:17','2025-07-20 20:19:17'),(11,'5cf17985-33c3-11f1-b689-bf1b47148d8a','admin@bmis.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','active','Admin User',NULL,'2026-04-09 03:22:39','2026-04-09 03:26:46',NULL),(12,'5cf9cf4f-33c3-11f1-b689-bf1b47148d8a','staff@bmis.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','staff','active','Staff User',NULL,'2026-04-09 03:22:39','2026-04-09 03:26:46',NULL),(14,NULL,'lourdianrentinoakol123@gmail.com','$2y$10$3EGfU0svcZI/8yTsgUo6G.UU8pH8S3PFcLE.pqke4OtFxEQkc0v2S','admin','inactive','luffy monkey','93948209','2026-04-26 12:15:38','2026-05-07 12:47:08','2026-05-07 12:47:08');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-10 11:56:13
