<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyAuditLogsColumns extends Migration
{
    public function up()
    {
        $fields = [
            'old_data' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'new_data' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('audit_logs', $fields);
    }

    public function down()
    {
        $fields = [
            'old_data' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'new_data' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('audit_logs', $fields);
    }
}
