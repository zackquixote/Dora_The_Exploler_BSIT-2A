-- ============================================================================
-- SAFE MIGRATION: Run each block one at a time in phpMyAdmin
-- If a block gives "Duplicate column" error, just SKIP it and run the next one
-- ============================================================================


-- ========== BLOCK 1: Households - Add household_no ==========
ALTER TABLE `households` ADD COLUMN `household_no` VARCHAR(50) NOT NULL AFTER `id`;
ALTER TABLE `households` ADD UNIQUE KEY `household_no` (`household_no`);

-- ========== BLOCK 2: Households - Add sitio ==========
ALTER TABLE `households` ADD COLUMN `sitio` VARCHAR(100) DEFAULT NULL AFTER `address`;

-- ========== BLOCK 3: Households - Add street_address ==========
ALTER TABLE `households` ADD COLUMN `street_address` VARCHAR(255) DEFAULT NULL AFTER `sitio`;

-- ========== BLOCK 4: Households - Add house_type ==========
ALTER TABLE `households` ADD COLUMN `house_type` VARCHAR(50) DEFAULT NULL AFTER `street_address`;


-- ========== BLOCK 5: Residents - Drop foreign keys ==========
ALTER TABLE `residents` DROP FOREIGN KEY `residents_ibfk_1`;

-- ========== BLOCK 6: Residents - Drop FK 2 ==========
ALTER TABLE `residents` DROP FOREIGN KEY `residents_ibfk_2`;

-- ========== BLOCK 7: Residents - Make household_id nullable ==========
ALTER TABLE `residents` MODIFY COLUMN `household_id` INT(11) DEFAULT NULL;

-- ========== BLOCK 8: Residents - Add sitio ==========
ALTER TABLE `residents` ADD COLUMN `sitio` VARCHAR(100) DEFAULT NULL AFTER `contact_number`;

-- ========== BLOCK 9: Residents - Add occupation ==========
ALTER TABLE `residents` ADD COLUMN `occupation` VARCHAR(100) DEFAULT NULL AFTER `sitio`;

-- ========== BLOCK 10: Residents - Add citizenship ==========
ALTER TABLE `residents` ADD COLUMN `citizenship` VARCHAR(50) DEFAULT 'Filipino' AFTER `occupation`;

-- ========== BLOCK 11: Residents - Add street_address ==========
ALTER TABLE `residents` ADD COLUMN `street_address` VARCHAR(255) DEFAULT NULL AFTER `citizenship`;

-- ========== BLOCK 12: Residents - Add profile_picture ==========
ALTER TABLE `residents` ADD COLUMN `profile_picture` VARCHAR(255) DEFAULT NULL AFTER `street_address`;

-- ========== BLOCK 13: Residents - Add is_household_head ==========
ALTER TABLE `residents` ADD COLUMN `is_household_head` TINYINT(1) NOT NULL DEFAULT 0 AFTER `profile_picture`;

-- ========== BLOCK 14: Residents - Add joined_household_date ==========
ALTER TABLE `residents` ADD COLUMN `joined_household_date` DATE DEFAULT NULL AFTER `is_household_head`;

-- ========== BLOCK 15: Residents - Add left_household_date ==========
ALTER TABLE `residents` ADD COLUMN `left_household_date` DATE DEFAULT NULL AFTER `joined_household_date`;

-- ========== BLOCK 16: Residents - Fix civil_status type ==========
ALTER TABLE `residents` MODIFY COLUMN `civil_status` VARCHAR(20) DEFAULT 'single';

-- ========== BLOCK 17: Residents - Fix status type ==========
ALTER TABLE `residents` MODIFY COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'active';

-- ========== BLOCK 18: Residents - Rename registered_at to created_at ==========
ALTER TABLE `residents` CHANGE COLUMN `registered_at` `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP();

-- ========== BLOCK 19: Residents - Add updated_at ==========
ALTER TABLE `residents` ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`;

-- ========== BLOCK 20: Residents - Add deleted_at ==========
ALTER TABLE `residents` ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `updated_at`;

-- ========== BLOCK 21: Residents - Re-add foreign keys ==========
ALTER TABLE `residents` ADD CONSTRAINT `residents_ibfk_1` FOREIGN KEY (`household_id`) REFERENCES `households` (`id`) ON DELETE SET NULL;
ALTER TABLE `residents` ADD CONSTRAINT `residents_ibfk_2` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`);

-- ========== BLOCK 22: Residents - Add indexes ==========
ALTER TABLE `residents` ADD KEY `idx_sitio` (`sitio`);
ALTER TABLE `residents` ADD KEY `idx_deleted_at` (`deleted_at`);

-- ========== DONE! ==========
