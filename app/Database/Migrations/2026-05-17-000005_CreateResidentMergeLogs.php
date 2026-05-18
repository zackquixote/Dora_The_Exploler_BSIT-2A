<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Phase 3.2 - Data Quality
 * Track resident merges for audit/traceability.
 */
class CreateResidentMergeLogs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'primary_resident_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'merged_resident_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'merged_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'merged_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'impact_json' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'before_primary_json' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'before_merged_json' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'after_primary_json' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('primary_resident_id');
        $this->forge->addKey('merged_resident_id');
        $this->forge->addKey('merged_at');

        $this->forge->createTable('resident_merge_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('resident_merge_logs', true);
    }
}

