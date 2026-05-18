<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ResidentModel
 * 
 * Core model for barangay residents.
 * 
 * RECENT ENHANCEMENTS:
 * - Added getWithAge() for automatic age display.
 * - Duplicate protection via DB unique index.
 */
class ResidentModel extends Model
{
    protected $table      = 'residents';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'household_id',
        'is_household_head',
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'sex',
        'civil_status',
        'sitio',
        'contact_number',
        'occupation',
        'citizenship',
        'profile_picture',
        'relationship_to_head',
        'is_voter',
        'is_senior_citizen',
        'is_pwd',
        'status',
        'registered_by',
        'joined_household_date',
        'left_household_date',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    protected $validationRules = [
        'first_name'   => 'required|min_length[2]|max_length[80]|regex_match[/^[a-zA-ZÀ-ÿ\s\'\-\.]+$/]',
        'last_name'    => 'required|min_length[2]|max_length[80]|regex_match[/^[a-zA-ZÀ-ÿ\s\'\-\.]+$/]',
        'birthdate'    => 'required|valid_date',
        'sex'          => 'required|in_list[male,female]',
        'civil_status' => 'permit_empty|in_list[single,married,widowed,separated]',
    ];

    protected $validationMessages = [
        'first_name' => [
            'regex_match' => 'First name must contain letters only — no numbers or special characters.',
        ],
        'last_name' => [
            'regex_match' => 'Last name must contain letters only — no numbers or special characters.',
        ],
    ];

    /**
     * Fetch residents with auto‑computed age.
     */
    public function getWithAge()
    {
        return $this->select('residents.*, TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) as age');
    }

    /**
     * Fetch residents with household info and age.
     */
    public function getResidentsWithHousehold($selectedPurok = 'all')
    {
        $builder = $this->select('residents.*, households.household_no, TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) as age')
                        ->join('households', 'households.id = residents.household_id', 'left');

        if ($selectedPurok !== 'all') {
            if ($selectedPurok === 'Unassigned') {
                $builder->groupStart()
                        ->where('residents.sitio', null)
                        ->orWhere('residents.sitio', '')
                        ->groupEnd();
            } else {
                $builder->where('residents.sitio', $selectedPurok);
            }
        }

        return $builder->orderBy('residents.id', 'DESC')->findAll();
    }

    /**
     * Get counts of residents per purok.
     */
    public function getPurokCounts()
    {
        $allResidents = $this->select('sitio')->findAll();
        $purokCounts = [];
        foreach ($allResidents as $r) {
            $sitio = !empty($r['sitio']) ? $r['sitio'] : 'Unassigned';
            $purokCounts[$sitio] = ($purokCounts[$sitio] ?? 0) + 1;
        }
        return $purokCounts;
    }

    /**
     * Fetch a single resident's details with household address.
     */
    public function getDetailsWithHousehold($id)
    {
        return $this->select('residents.*, households.household_no, households.street_address as household_address, TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) as age')
                    ->join('households', 'households.id = residents.household_id', 'left')
                    ->where('residents.id', $id)
                    ->first();
    }

    /**
     * Search residents for household assignment.
     */
    public function searchForAssignment($keyword = '', $filterPurok = '', $filterHouseId = '', $filterStatus = 'no_household')
    {
        $builder = $this->select('residents.id, residents.first_name, residents.last_name, residents.sitio, residents.household_id, residents.profile_picture, residents.relationship_to_head')
                        ->where('residents.status', 'active');

        if (!empty($keyword)) {
            $builder->groupStart()
                    ->like('residents.first_name', $keyword)
                    ->orLike('residents.last_name', $keyword)
                    ->groupEnd();
        }
        if (!empty($filterPurok)) {
            $builder->where('residents.sitio', $filterPurok);
        }
        if (!empty($filterHouseId)) {
            $builder->where('residents.household_id', $filterHouseId);
        }
        if ($filterStatus === 'no_household') {
            $builder->where('residents.household_id', null);
        }

        return $builder->orderBy('residents.last_name', 'ASC')->findAll();
    }
}