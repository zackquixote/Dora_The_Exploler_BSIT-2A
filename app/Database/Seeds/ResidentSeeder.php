<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ResidentSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'household_id' => 1,
                'first_name' => 'Juan',
                'middle_name' => 'Dela',
                'last_name' => 'Cruz',
                'birthdate' => '1980-05-15',
                'sex' => 'male',
                'civil_status' => 'married',
                'contact_number' => '09123456789',
                'relationship_to_head' => 'head',
                'is_voter' => 1,
                'is_senior_citizen' => 0,
                'is_pwd' => 0,
                'status' => 'active',
                'registered_by' => 1,
                'registered_at' => date('Y-m-d H:i:s')
            ],
            [
                'household_id' => 2,
                'first_name' => 'Maria',
                'middle_name' => 'Santos',
                'last_name' => 'Garcia',
                'birthdate' => '1975-08-22',
                'sex' => 'female',
                'civil_status' => 'married',
                'contact_number' => '09198765432',
                'relationship_to_head' => 'head',
                'is_voter' => 1,
                'is_senior_citizen' => 0,
                'is_pwd' => 0,
                'status' => 'active',
                'registered_by' => 1,
                'registered_at' => date('Y-m-d H:i:s')
            ],
            [
                'household_id' => 3,
                'first_name' => 'Pedro',
                'middle_name' => 'Reyes',
                'last_name' => 'Mendoza',
                'birthdate' => '1990-12-10',
                'sex' => 'male',
                'civil_status' => 'single',
                'contact_number' => '09234567890',
                'relationship_to_head' => 'head',
                'is_voter' => 1,
                'is_senior_citizen' => 0,
                'is_pwd' => 0,
                'status' => 'active',
                'registered_by' => 1,
                'registered_at' => date('Y-m-d H:i:s')
            ],
            [
                'household_id' => 4,
                'first_name' => 'Ana',
                'middle_name' => 'Lopez',
                'last_name' => 'Torres',
                'birthdate' => '1965-03-28',
                'sex' => 'female',
                'civil_status' => 'widowed',
                'contact_number' => '09345678901',
                'relationship_to_head' => 'head',
                'is_voter' => 1,
                'is_senior_citizen' => 1,
                'is_pwd' => 0,
                'status' => 'active',
                'registered_by' => 1,
                'registered_at' => date('Y-m-d H:i:s')
            ],
            [
                'household_id' => 5,
                'first_name' => 'Carlos',
                'middle_name' => 'Flores',
                'last_name' => 'Ramos',
                'birthdate' => '1985-07-14',
                'sex' => 'male',
                'civil_status' => 'married',
                'contact_number' => '09456789012',
                'relationship_to_head' => 'head',
                'is_voter' => 1,
                'is_senior_citizen' => 0,
                'is_pwd' => 0,
                'status' => 'active',
                'registered_by' => 1,
                'registered_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('residents')->insertBatch($data);
    }
}
