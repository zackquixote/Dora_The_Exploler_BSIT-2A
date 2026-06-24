<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRejectionReasonToResidentAccounts extends Migration
{
    public function up()
    {
        $this->forge->addColumn('resident_accounts', [
            'rejection_reason' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'status',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('resident_accounts', 'rejection_reason');
    }
}
