<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDatabaseIndexes extends Migration
{
    public function up()
    {
        // Residents
        $this->db->query('ALTER TABLE `residents` ADD INDEX IF NOT EXISTS `idx_last_name` (`last_name`)');
        $this->db->query('ALTER TABLE `residents` ADD INDEX IF NOT EXISTS `idx_status` (`status`)');
        $this->db->query('ALTER TABLE `residents` ADD INDEX IF NOT EXISTS `idx_sitio` (`sitio`)');
        $this->db->query('ALTER TABLE `residents` ADD INDEX IF NOT EXISTS `idx_deleted_at` (`deleted_at`)');

        // Blotter
        $this->db->query('ALTER TABLE `blotter_records` ADD INDEX IF NOT EXISTS `idx_case_number` (`case_number`)');
        $this->db->query('ALTER TABLE `blotter_records` ADD INDEX IF NOT EXISTS `idx_status` (`status`)');
        $this->db->query('ALTER TABLE `blotter_records` ADD INDEX IF NOT EXISTS `idx_incident_date` (`incident_date`)');

        // Certificates
        $this->db->query('ALTER TABLE `certificates` ADD INDEX IF NOT EXISTS `idx_resident_id` (`resident_id`)');
        $this->db->query('ALTER TABLE `certificates` ADD INDEX IF NOT EXISTS `idx_cert_type` (`certificate_type`)');

        // Audit logs
        $this->db->query('ALTER TABLE `audit_logs` ADD INDEX IF NOT EXISTS `idx_entity` (`entity`)');
        $this->db->query('ALTER TABLE `audit_logs` ADD INDEX IF NOT EXISTS `idx_created_at` (`created_at`)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `residents` DROP INDEX IF EXISTS `idx_last_name`');
        $this->db->query('ALTER TABLE `residents` DROP INDEX IF EXISTS `idx_status`');
        $this->db->query('ALTER TABLE `residents` DROP INDEX IF EXISTS `idx_sitio`');
        $this->db->query('ALTER TABLE `residents` DROP INDEX IF EXISTS `idx_deleted_at`');
        $this->db->query('ALTER TABLE `blotter_records` DROP INDEX IF EXISTS `idx_case_number`');
        $this->db->query('ALTER TABLE `blotter_records` DROP INDEX IF EXISTS `idx_status`');
        $this->db->query('ALTER TABLE `blotter_records` DROP INDEX IF EXISTS `idx_incident_date`');
        $this->db->query('ALTER TABLE `certificates` DROP INDEX IF EXISTS `idx_resident_id`');
        $this->db->query('ALTER TABLE `certificates` DROP INDEX IF EXISTS `idx_cert_type`');
        $this->db->query('ALTER TABLE `audit_logs` DROP INDEX IF EXISTS `idx_entity`');
        $this->db->query('ALTER TABLE `audit_logs` DROP INDEX IF EXISTS `idx_created_at`');
    }
}
