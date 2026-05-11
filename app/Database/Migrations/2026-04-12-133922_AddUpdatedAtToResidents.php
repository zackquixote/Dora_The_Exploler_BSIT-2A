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
                // `registered_at` was renamed to `created_at` in the current schema
                'after' => 'created_at'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('residents', 'updated_at');
    }
}
