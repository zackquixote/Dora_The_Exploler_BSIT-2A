<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * CertificateModel
 * 
 * Manages certificate issuance.
 * 
 * TABLE: certificates
 * - id, resident_id, certificate_type, purpose, created_by, created_at
 * - NO updated_at column → timestamps disabled.
 * 
 * METHODS:
 * - getCertificateForPrint(): Returns certificate data with resident details.
 *   (Officials' names are omitted – they are not stored directly in barangay_settings)
 */
class CertificateModel extends Model
{
    protected $table            = 'certificates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'resident_id',
        'certificate_type',
        'purpose',
        'created_by',
    ];

    // Disabled because table lacks 'updated_at'
    protected $useTimestamps = false;

    protected $validationRules = [
        'resident_id'      => 'required|integer',
        'certificate_type' => 'required|in_list[Barangay Clearance,Certificate of Indigency,Certificate of Residency,Business Permit,Solo Parent]',
        'purpose'          => 'required',
    ];

    public static function getTypes(): array
    {
        return [
            'Barangay Clearance',
            'Certificate of Indigency',
            'Certificate of Residency',
            'Business Permit',
            'Solo Parent',
        ];
    }

    /**
     * Get certificate data for printing.
     * NOTE: Officials' names are NOT directly available in barangay_settings.
     * You must join residents table using captain_id etc. if needed.
     */
    public function getCertificateForPrint(int $id): ?array
    {
        $result = $this->db->table('certificates c')
            ->select([
                'c.id',
                'c.certificate_type',
                'c.purpose',
                'c.created_at',
                'r.first_name',
                'r.last_name',
                'r.middle_name',
                'TIMESTAMPDIFF(YEAR, r.birthdate, CURDATE()) as age',
                'r.civil_status',
                'r.sex as gender',
                'r.sitio as address',
                'ct.content AS template_content',
                'bs.barangay_name',
                'bs.municipality',
                'bs.province',
                // Removed captain_name, secretary_name, treasurer_name (they don't exist)
            ])
            ->join('residents r',          'r.id = c.resident_id',          'left')
            ->join('certificate_types ct', 'ct.name = c.certificate_type',  'left')
            ->join('barangay_settings bs', 'bs.id = 1',                     'left')
            ->where('c.id', $id)
            ->get()
            ->getRowArray();

        return $result ?: null;
    }

    public function getBarangaySettings(): ?array
    {
        return $this->db->table('barangay_settings')
            ->where('id', 1)
            ->get()
            ->getRowArray() ?: null;
    }
}