<?php

namespace App\Models;

use CodeIgniter\Model;

class CertificateModel extends Model
{
    protected $table      = 'certificates';
    protected $primaryKey = 'id';
    
    // Strictly matching your simplified SQL columns
    protected $allowedFields = [
        'resident_id', 
        'certificate_type', 
        'purpose', 
        'created_by',
        'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // No updated_at column in simplified SQL

    /**
     * Get all certificates with Resident Name and Creator Name (For Recent Activity)
     */
    public function getRecentActivity($limit = 10)
    {
        return $this->select('certificates.*, 
                             CONCAT(residents.first_name, " ", residents.last_name) as resident_name,
                             users.username as created_by_name')
                    ->join('residents', 'residents.id = certificates.resident_id')
                    ->join('users', 'users.id = certificates.created_by', 'left') // Left join in case user is deleted
                    ->orderBy('certificates.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get specific certificate details for printing
     */
    public function getCertificateForPrint($id)
    {
        return $this->select('certificates.*, residents.*, certificates.id as cert_id')
                    ->join('residents', 'residents.id = certificates.resident_id')
                    ->where('certificates.id', $id)
                    ->first();
    }

    /**
     * Helper to get Barangay Settings (Captain Name, etc.)
     * TODO: Move this to a SettingsModel later
     */    // Helper to get Barangay Settings (Captain Name, etc.)
    public function getBarangaySettings()
    {
        // Updated to match your Potia document
        return (object)[
            'province'       => 'NEGROS OCCIDENTAL',
            'municipality'   => 'ILOG', // Formerly Potia municipality
            'barangay_name'  => 'TABU',
            'captain_name'   => 'SI POGI', 
            'logo_url'       => base_url('assets/img/tabu.jpg') // Your logo path
        ];
    }
}