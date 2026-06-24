<?php

namespace App\Controllers;

use App\Services\IdCardService;
use App\Exceptions\ResidentNotFoundException;

class IdGenerator extends BaseController
{
    protected IdCardService $service;

    public function __construct()
    {
        $this->service = new IdCardService();
    }

    /**
     * Display printable ID card for a resident.
     */
    public function print($id)
    {
        try {
            $resident = $this->service->getResident((int) $id);
        } catch (ResidentNotFoundException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        // Generate QR URL for view
        $qrUrl = $this->service->getQrUrl((int) $id);
        return view('id_generator/print', [
            'title'    => 'Print Barangay ID',
            'resident' => $resident,
            'qr_url'   => $qrUrl,
        ]);
    }

    /**
     * Public verification endpoint for QR code scans with JWT validation.
     */
     public function verify($id, $jwt = null)
     {
         try {
             $resident = $this->service->getResident((int) $id);
         } catch (ResidentNotFoundException $e) {
             return view('id_generator/verify', [
                 'title'   => 'Verification Failed',
                 'status'  => 'error',
                 'message' => $e->getMessage(),
             ]);
         }

         // Validate JWT if provided
        if ($jwt && !$this->service->validateJwtToken($jwt, (int) $id)) {
             return view('id_generator/verify', [
                 'title'   => 'Verification Failed',
                 'status'  => 'error',
                 'message' => 'Invalid or expired verification token.',
             ]);
         }

         if ($resident['status'] !== 'active') {
             return view('id_generator/verify', [
                 'title'   => 'Verification Failed',
                 'status'  => 'error',
                 'message' => 'This ID is inactive.',
             ]);
         }

         return view('id_generator/verify', [
             'title'    => 'Resident Verification',
             'status'   => 'success',
             'resident' => $resident,
         ]);
     }
}
