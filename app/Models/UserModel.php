<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid', 'email', 'password', 'role', 
        'status', 'name', 'phone', 'created_at', 
        'updated_at', 'deleted_at'
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    /**
     * Get paginated records for DataTables
     */
    public function getRecords($start, $length, $searchValue)
    {
        $builder = $this->builder();
        
        // Apply search filter if provided
        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('name', $searchValue)
                    ->orLike('email', $searchValue)
                    ->orLike('role', $searchValue)
                    ->orLike('phone', $searchValue)
                    ->groupEnd();
        }
        
        // Get total filtered count
        $filteredCount = $builder->countAllResults(false);
        
        // Get paginated results - FIXED: Use $this->findAll() not $builder->findAll()
        $data = $this->findAll($length, $start);
        
        return [
            'data' => $data,
            'filtered' => $filteredCount
        ];
    }
}