-- ============================================================================
-- BMIS Database Integrity Fixes
-- Run this SQL on the `crud_db` database to apply all fixes.
-- Date: 2026-05-10
-- ============================================================================

-- ─────────────────────────────────────────────────────────────────────────────
-- FIX #1: Add missing FK on certificates.resident_id → residents.id
-- Without this, deleting a resident orphans their certificate records.
-- ─────────────────────────────────────────────────────────────────────────────
ALTER TABLE `certificates`
  ADD KEY `idx_cert_resident` (`resident_id`);

ALTER TABLE `certificates`
  ADD CONSTRAINT `fk_cert_resident`
  FOREIGN KEY (`resident_id`) REFERENCES `residents`(`id`)
  ON DELETE CASCADE;


-- ─────────────────────────────────────────────────────────────────────────────
-- FIX #2: Change residents.household_id FK from RESTRICT to SET NULL
-- Without this, deleting a household fails with a DB error.
-- ─────────────────────────────────────────────────────────────────────────────
ALTER TABLE `residents` DROP FOREIGN KEY `residents_ibfk_1`;
ALTER TABLE `residents`
  ADD CONSTRAINT `residents_ibfk_1`
  FOREIGN KEY (`household_id`) REFERENCES `households`(`id`)
  ON DELETE SET NULL;


-- ─────────────────────────────────────────────────────────────────────────────
-- FIX #3: Change residents.registered_by FK from RESTRICT to SET NULL
-- Without this, deleting a user who registered residents will fail.
-- ─────────────────────────────────────────────────────────────────────────────
ALTER TABLE `residents` MODIFY `registered_by` int(11) DEFAULT NULL;
ALTER TABLE `residents` DROP FOREIGN KEY `residents_ibfk_2`;
ALTER TABLE `residents`
  ADD CONSTRAINT `residents_ibfk_2`
  FOREIGN KEY (`registered_by`) REFERENCES `users`(`id`)
  ON DELETE SET NULL;


-- ─────────────────────────────────────────────────────────────────────────────
-- FIX #4: Add missing FK on blotter_timeline.created_by → users.id
-- ─────────────────────────────────────────────────────────────────────────────
ALTER TABLE `blotter_timeline`
  ADD KEY `fk_timeline_createdby` (`created_by`);

ALTER TABLE `blotter_timeline`
  ADD CONSTRAINT `fk_timeline_createdby`
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
  ON DELETE SET NULL;


-- ─────────────────────────────────────────────────────────────────────────────
-- FIX #5: Add missing indexes on frequently queried columns
-- ─────────────────────────────────────────────────────────────────────────────
ALTER TABLE `residents` ADD INDEX `idx_sitio` (`sitio`);
ALTER TABLE `blotter_records` ADD INDEX `idx_status` (`status`);
ALTER TABLE `blotter_records` ADD INDEX `idx_incident_date` (`incident_date`);


-- ─────────────────────────────────────────────────────────────────────────────
-- FIX #6: Remove duplicate index on residents.household_id
-- idx_household and idx_household_members are both on the same column.
-- ─────────────────────────────────────────────────────────────────────────────
ALTER TABLE `residents` DROP INDEX `idx_household_members`;
