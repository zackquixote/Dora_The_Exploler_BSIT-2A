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
                'after' => 'registered_at'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('residents', 'deleted_at');
    }
}
