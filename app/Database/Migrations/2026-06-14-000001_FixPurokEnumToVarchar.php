<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Fix Purok ENUM → VARCHAR
 *
 * The `residents.sitio` column was already converted to VARCHAR(255),
 * but `households.sitio` and `blotter_records.purok` still use the old
 * 5-value ENUM, preventing new purok values from being saved.
 *
 * This migration converts both columns to VARCHAR(255) to match
 * the dynamic `puroks` lookup table.
 */
class FixPurokEnumToVarchar extends Migration
{
    public function up()
    {
        // Convert households.sitio ENUM → VARCHAR(255)
        $this->db->query("ALTER TABLE `households` MODIFY COLUMN `sitio` VARCHAR(255) DEFAULT NULL");

        // Convert blotter_records.purok ENUM → VARCHAR(255)
        $this->db->query("ALTER TABLE `blotter_records` MODIFY COLUMN `purok` VARCHAR(255) DEFAULT NULL");
    }

    public function down()
    {
        // Revert to original ENUM (only safe if data still fits the original values)
        $this->db->query("ALTER TABLE `households` MODIFY COLUMN `sitio` ENUM('Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um') DEFAULT NULL");
        $this->db->query("ALTER TABLE `blotter_records` MODIFY COLUMN `purok` ENUM('Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um') DEFAULT NULL");
    }
}
