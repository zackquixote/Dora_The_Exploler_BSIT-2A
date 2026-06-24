<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSourceToBlotterRecords extends Migration
{
    public function up()
    {
        $fields = [
            'source' => [
                'type'       => 'ENUM',
                'constraint' => ['Walk-in', 'Online'],
                'default'    => 'Walk-in',
                'after'      => 'status',
            ],
        ];
        
        $this->forge->addColumn('blotter_records', $fields);
        
        // Let's also default all existing pending ones to Online IF they don't have an action_taken, just as a heuristic.
        // Actually, no data manipulation needed right now.
    }

    public function down()
    {
        $this->forge->dropColumn('blotter_records', 'source');
    }
}
