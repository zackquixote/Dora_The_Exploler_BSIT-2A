<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHouseholdColumns extends Migration
{
    public function up()
    {
        $this->forge->addColumn('households', [
            'household_no' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'after' => 'id'
            ],
            'sitio' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'address'
            ],
            'street_address' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'sitio'
            ],
            'house_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'street_address'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('households', ['household_no', 'sitio', 'street_address', 'house_type']);
    }
}
