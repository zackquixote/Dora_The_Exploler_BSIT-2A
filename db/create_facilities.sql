-- Facilities Table
CREATE TABLE IF NOT EXISTS `facilities` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `type` ENUM('Venue','Equipment','Vehicle','Other') NOT NULL DEFAULT 'Venue',
  `description` TEXT NULL,
  `capacity` INT(11) NULL COMMENT 'Max capacity for venues, quantity for equipment',
  `status` ENUM('Available','Maintenance','Unavailable') NOT NULL DEFAULT 'Available',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Facility Bookings Table
CREATE TABLE IF NOT EXISTS `facility_bookings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `facility_id` INT(11) UNSIGNED NOT NULL,
  `resident_id` INT(11) NOT NULL,
  `start_datetime` DATETIME NOT NULL,
  `end_datetime` DATETIME NOT NULL,
  `purpose` TEXT NOT NULL,
  `status` ENUM('Pending','Approved','Rejected','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `remarks` TEXT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_booking_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed some sample facilities
INSERT INTO `facilities` (`name`, `type`, `description`, `capacity`, `status`, `created_at`) VALUES
('Covered Court', 'Venue', 'Multi-purpose covered court for sports and community events.', 500, 'Available', NOW()),
('Barangay Hall Function Room', 'Venue', 'Air-conditioned function room for meetings and small gatherings.', 50, 'Available', NOW()),
('Barangay Ambulance', 'Vehicle', 'Emergency ambulance for medical transport.', 1, 'Available', NOW()),
('Tents & Chairs Set (50 pax)', 'Equipment', 'Set of tents and 50 monoblock chairs for events.', 3, 'Available', NOW()),
('Barangay Patrol Vehicle', 'Vehicle', 'Multi-purpose patrol vehicle for official use.', 1, 'Available', NOW());
