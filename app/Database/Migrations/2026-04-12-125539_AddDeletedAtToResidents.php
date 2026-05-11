<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtToResidents extends Migration
{
    public function up()
    {
        $this->forge->addColumn('residents', [
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                // `registered_at` was renamed to `created_at` in the current schema
                'after' => 'created_at'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('residents', 'deleted_at');
    }
}
