<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResidentTransferHistory extends Migration
{
    public function up()
    {
        // ── 1. resident_transfer_history ─────────────────────────────
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'resident_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'from_household_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => null],
            'to_household_id'   => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => null],
            'from_household_no' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'to_household_no'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'reason'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'transferred_by'    => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'transferred_at'    => ['type' => 'DATETIME', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('resident_id');
        $this->forge->createTable('resident_transfer_history', true);

        // ── 2. Add term columns to officials ─────────────────────────
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('officials');

        if (!in_array('term_start', $fields)) {
            $this->forge->addColumn('officials', [
                'term_start' => ['type' => 'DATE', 'null' => true, 'default' => null, 'after' => 'is_active'],
            ]);
        }
        if (!in_array('term_end', $fields)) {
            $this->forge->addColumn('officials', [
                'term_end' => ['type' => 'DATE', 'null' => true, 'default' => null, 'after' => 'term_start'],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('resident_transfer_history', true);

        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('officials');
        if (in_array('term_start', $fields)) {
            $this->forge->dropColumn('officials', 'term_start');
        }
        if (in_array('term_end', $fields)) {
            $this->forge->dropColumn('officials', 'term_end');
        }
    }
}
