<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropAuditLogsTable extends Migration
{
    public function up()
    {
        $this->forge->dropTable('audit_logs', true);
    }

    public function down()
    {
        // Re-create the table if rolled back (simplified schema)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'entity' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'entity_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'old_data' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'new_data' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('audit_logs');
    }
}
