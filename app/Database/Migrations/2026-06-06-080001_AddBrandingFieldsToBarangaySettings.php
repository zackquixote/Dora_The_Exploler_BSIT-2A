<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBrandingFieldsToBarangaySettings extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Optional "photo" used for sidebar/branding (separate from logo).
        try {
            $db->query("ALTER TABLE barangay_settings ADD COLUMN photo VARCHAR(255) NULL AFTER logo");
        } catch (\Throwable $e) {
            // Column may already exist
        }

        // Adjustable logo size (in pixels) used on login/sidebar.
        try {
            $db->query("ALTER TABLE barangay_settings ADD COLUMN logo_size INT NULL AFTER photo");
        } catch (\Throwable $e) {
            // Column may already exist
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        try {
            $db->query("ALTER TABLE barangay_settings DROP COLUMN logo_size");
        } catch (\Throwable $e) {
        }

        try {
            $db->query("ALTER TABLE barangay_settings DROP COLUMN photo");
        } catch (\Throwable $e) {
        }
    }
}

