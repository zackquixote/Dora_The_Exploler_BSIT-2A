<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddResetTokenToResidentAccounts extends Migration
{
    public function up()
    {
        $this->forge->addColumn('resident_accounts', [
            'reset_token' => [
                'type'       => 'VARCHAR',
                'constraint' => '64',
                'null'       => true,
            ],
            'reset_token_expiry' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('resident_accounts', 'reset_token');
        $this->forge->dropColumn('resident_accounts', 'reset_token_expiry');
    }
}
