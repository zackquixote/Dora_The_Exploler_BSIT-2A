<?php

namespace App\Controllers;

use App\Models\ResidentModel;

class TestResidentDebug extends BaseController
{
    public function manualInsert()
    {
        $residentModel = new ResidentModel();
        
        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'birthdate' => '2000-01-15',
            'sex' => 'male',
            'sitio' => 'Purok Malipayon',
            'status' => 'active',
            'registered_by' => 1,
        ];
        
        try {
            $residentModel->insert($data);
            $insertId = $residentModel->getInsertID();
            echo "SUCCESS! Resident created with ID: " . $insertId;
            
            // Verify it was inserted
            $resident = $residentModel->find($insertId);
            echo "<pre>";
            print_r($resident);
            echo "</pre>";
        } catch (\Exception $e) {
            echo "ERROR: " . $e->getMessage();
            echo "<pre>";
            print_r($e);
            echo "</pre>";
        }
    }
}
