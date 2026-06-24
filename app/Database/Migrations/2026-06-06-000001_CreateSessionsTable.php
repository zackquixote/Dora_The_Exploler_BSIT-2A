<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSessionsTable extends Migration
{
    public function up()
    {
        // Create sessions table for DatabaseHandler
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
                'null' => false,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => false,
            ],
            'timestamp' => [
                'type' => 'INT',
                'null' => false,
            ],
            'data' => [
                'type' => 'BLOB',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('timestamp');
        $this->forge->addKey('ip_address');

        $this->forge->createTable('sessions');
    }

    public function down()
    {
        $this->forge->dropTable('sessions', true);
    }
}
