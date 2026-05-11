<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * UserModel
 * 
 * Manages system user accounts, authentication, and roles.
 * 
 * TABLE: users
 * - Stores login credentials and profile information for system users.
 * 
 * FIELDS:
 * - uuid: Unique identifier (for API/public ID usage)
 * - email: Login email (must be unique)
 * - password: Hashed password
 * - role: User role (e.g., admin, secretary, treasurer, captain)
 * - status: Account status (active, inactive, etc.)
 * - name: Display name
 * - phone: Contact number
 * 
 * SOFT DELETE:
 * - Uses soft deletes via `deleted_at` field.
 * 
 * TIMESTAMPS: created_at, updated_at, deleted_at
 * 
 * METHODS:
 * - getRecords($start, $length, $searchValue): DataTables pagination + search.
 *   Returns paginated users and total filtered count.
 * 
 * @package App\Models
 */
class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid', 'email', 'password', 'role', 
        'status', 'name', 'phone'
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
        $start = (int) ($start ?? 0);
        $length = (int) ($length ?? 10);
        $searchValue = trim((string) ($searchValue ?? ''));

        // Build a single base query that respects soft deletes (Model methods do).
        $base = $this->select('id, name, email, role, status, phone, created_at');

        if ($searchValue !== '') {
            $base->groupStart()
                ->like('name', $searchValue)
                ->orLike('email', $searchValue)
                ->orLike('role', $searchValue)
                ->orLike('phone', $searchValue)
                ->groupEnd();
        }

        // Count (filtered) without mutating the data query
        $filteredCount = (clone $base)->countAllResults();

        // Data page
        $data = $base
            ->orderBy('id', 'DESC')
            ->findAll($length, $start);

        return ['data' => $data, 'filtered' => $filteredCount];
    }
}