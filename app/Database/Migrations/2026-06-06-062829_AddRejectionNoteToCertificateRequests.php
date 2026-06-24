<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRejectionNoteToCertificateRequests extends Migration
{
    public function up()
    {
        $this->forge->addColumn('certificate_requests', [
            'rejection_note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('certificate_requests', 'rejection_note');
    }
}
