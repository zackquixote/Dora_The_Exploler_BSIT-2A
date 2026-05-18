<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCertificateRequestsTable extends Migration
{
    public function up()
    {
        // Certificate Requests Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'resident_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'certificate_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'purpose' => [
                'type'       => 'TEXT',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'Processing', 'Ready for Pickup', 'Released', 'Rejected', 'Cancelled'],
                'default'    => 'Pending',
            ],
            'remarks' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('resident_id');
        $this->forge->addKey('status');
        $this->forge->createTable('certificate_requests', true);
    }

    public function down()
    {
        $this->forge->dropTable('certificate_requests', true);
    }
}
