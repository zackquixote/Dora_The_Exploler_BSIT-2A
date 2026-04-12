<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class HouseholdSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'household_no' => 'HH-001',
                'sitio' => 'Sitio Uno',
                'street_address' => '123 Main Street',
                'house_type' => 'concrete',
                'head_resident_id' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'household_no' => 'HH-002',
                'sitio' => 'Sitio Dos',
                'street_address' => '456 Oak Avenue',
                'house_type' => 'wooden',
                'head_resident_id' => 2,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'household_no' => 'HH-003',
                'sitio' => 'Sitio Tres',
                'street_address' => '789 Pine Road',
                'house_type' => 'mixed',
                'head_resident_id' => 3,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'household_no' => 'HH-004',
                'sitio' => 'Sitio Cuatro',
                'street_address' => '321 Elm Street',
                'house_type' => 'bamboo',
                'head_resident_id' => 4,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'household_no' => 'HH-005',
                'sitio' => 'Sitio Cinco',
                'street_address' => '654 Maple Drive',
                'house_type' => 'concrete',
                'head_resident_id' => 5,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('households')->insertBatch($data);
    }
}
