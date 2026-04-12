<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToResidents extends Migration
{
    public function up()
    {
        $this->forge->addColumn('residents', [
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'after' => 'registered_at'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('residents', 'updated_at');
    }
}
