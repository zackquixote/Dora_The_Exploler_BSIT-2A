<?php

namespace App\Models;

use CodeIgniter\Model;

class FacilityBookingModel extends Model
{
    protected $table            = 'facility_bookings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'facility_id',
        'resident_id',
        'start_datetime',
        'end_datetime',
        'purpose',
        'status',
        'remarks',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getBookingsWithDetails($residentId = null)
    {
        $builder = $this->select('facility_bookings.*, facilities.name as facility_name, facilities.type as facility_type, residents.first_name, residents.last_name')
                        ->join('facilities', 'facilities.id = facility_bookings.facility_id')
                        ->join('residents', 'residents.id = facility_bookings.resident_id', 'left')
                        ->orderBy('facility_bookings.start_datetime', 'DESC');
                        
        if ($residentId) {
            $builder->where('facility_bookings.resident_id', $residentId);
        }

        return $builder->findAll();
    }

    public function checkConflict($facilityId, $start, $end)
    {
        // Find any Approved booking that overlaps with the requested time
        return $this->where('facility_id', $facilityId)
                    ->where('status', 'Approved')
                    ->groupStart()
                        ->groupStart()
                            ->where('start_datetime <=', $start)
                            ->where('end_datetime >', $start)
                        ->groupEnd()
                        ->orGroupStart()
                            ->where('start_datetime <', $end)
                            ->where('end_datetime >=', $end)
                        ->groupEnd()
                        ->orGroupStart()
                            ->where('start_datetime >=', $start)
                            ->where('end_datetime <=', $end)
                        ->groupEnd()
                    ->groupEnd()
                    ->countAllResults() > 0;
    }
}
