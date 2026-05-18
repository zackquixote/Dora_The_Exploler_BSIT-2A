<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBloodTypeAndAuditLogs extends Migration
{
    public function up()
    {
        // Add blood_type column to residents
        $fields = [
            'blood_type' => [
                'type' => 'VARCHAR',
                'constraint' => 3,
                'null' => true,
                'after' => 'sex',
            ],
        ];
        $this->forge->addColumn('residents', $fields);

        // Create audit_logs table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'null' => false,
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'entity' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'entity_id' => [
                'type' => 'INT',
                'null' => false,
            ],
            'old_data' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'new_data' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('audit_logs');
    }

    public function down()
    {
        // Remove blood_type column
        $this->forge->dropColumn('residents', 'blood_type');
        // Drop audit_logs table
        $this->forge->dropTable('audit_logs');
    }
}
?>
