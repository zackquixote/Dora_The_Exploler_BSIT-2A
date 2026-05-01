<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * CertificateModel
 * 
 * Manages certificate issuance with automatic sequential numbering
 * per certificate type and year (e.g., CLEAR-2026-0001).
 */
class CertificateModel extends Model
{
    protected $table            = 'certificates';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'certificate_number',   // NEW
        'resident_id',
        'certificate_type',
        'purpose',
        'created_by',
    ];

    // No updated_at column, so timestamps disabled
    protected $useTimestamps = false;

    protected $validationRules = [
        'resident_id'      => 'required|integer',
        'certificate_type' => 'required|in_list[Barangay Clearance,Certificate of Indigency,Certificate of Residency,Business Permit,Solo Parent]',
        'purpose'          => 'required',
    ];

    /**
     * Mapping of certificate types to short prefixes.
     */
    protected $typePrefixes = [
        'Barangay Clearance'        => 'CLEAR',
        'Certificate of Indigency'  => 'INDIG',
        'Certificate of Residency'  => 'RESID',
        'Business Permit'           => 'BUSPR',
        'Solo Parent'               => 'SOLOP',
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
     * Generate the next certificate number for a given type and year.
     *
     * Example: CLEAR-2026-0001
     */
    public function generateCertificateNumber(string $type, string $year = null): string
    {
        $year = $year ?? date('Y');
        $prefix = $this->typePrefixes[$type] ?? 'CERT';

        // Count how many of this type already exist for the year
        $count = $this->where('certificate_type', $type)
                      ->where('YEAR(created_at)', $year)
                      ->countAllResults();

        $next = $count + 1;
        return $prefix . '-' . $year . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get certificate data for printing, including official names.
     */
    public function getCertificateForPrint(int $id): ?array
    {
        // Fetch active officials
        $officials = $this->db->table('officials')
            ->select('position, full_name')
            ->where('is_active', 1)
            ->get()
            ->getResultArray();

        $captainName   = '';
        $secretaryName = '';
        $treasurerName = '';
        foreach ($officials as $off) {
            switch ($off['position']) {
                case 'Punong Barangay':
                    $captainName = $off['full_name'];
                    break;
                case 'Secretary':
                    $secretaryName = $off['full_name'];
                    break;
                case 'Treasurer':
                    $treasurerName = $off['full_name'];
                    break;
            }
        }

        $result = $this->db->table('certificates c')
            ->select([
                'c.id',
                'c.certificate_number',   // Now included
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
            ])
            ->join('residents r',          'r.id = c.resident_id',          'left')
            ->join('certificate_types ct', 'ct.name = c.certificate_type',  'left')
            ->join('barangay_settings bs', 'bs.id = 1',                     'left')
            ->where('c.id', $id)
            ->get()
            ->getRowArray();

        if ($result) {
            $result['captain_name']   = $captainName;
            $result['secretary_name'] = $secretaryName;
            $result['treasurer_name'] = $treasurerName;
        }

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