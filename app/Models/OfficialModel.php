<?php

namespace App\Models;

use CodeIgniter\Model;

class OfficialModel extends Model
{
    protected $table      = 'officials';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'position', 
        'full_name', 
        'contact_number', 
        'photo', 
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get only active officials
     */
    public function getActiveOfficials()
    {
        return $this->where('is_active', 1)->findAll();
    }
}