<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFacilitiesTables extends Migration
{
    public function up()
    {
        // 1. Facilities Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['Venue', 'Equipment', 'Vehicle', 'Other'],
                'default'    => 'Venue',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'capacity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'Max capacity for venues, quantity for equipment',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Available', 'Maintenance', 'Unavailable'],
                'default'    => 'Available',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('facilities');

        // 2. Facility Bookings Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'facility_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'resident_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'start_datetime' => [
                'type' => 'DATETIME',
            ],
            'end_datetime' => [
                'type' => 'DATETIME',
            ],
            'purpose' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'Approved', 'Rejected', 'Completed', 'Cancelled'],
                'default'    => 'Pending',
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('facility_id', 'facilities', 'id', 'CASCADE', 'CASCADE');
        // resident_id references `residents.id` (not adding strict FK constraint here to match existing DB style)
        $this->forge->createTable('facility_bookings');
    }

    public function down()
    {
        $this->forge->dropTable('facility_bookings');
        $this->forge->dropTable('facilities');
    }
}
