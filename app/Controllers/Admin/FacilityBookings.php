<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FacilityBookingModel;
use App\Models\NotificationModel;

class FacilityBookings extends BaseController
{
    protected $bookingModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->bookingModel = new FacilityBookingModel();
        $this->notificationModel = new NotificationModel();
    }

    public function index()
    {
        $bookings = $this->bookingModel->getBookingsWithDetails();

        return view('Admin/facility_bookings/index', [
            'bookings' => $bookings
        ]);
    }

    public function approve($id)
    {
        $booking = $this->bookingModel->find($id);
        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found.');
        }

        if ($this->bookingModel->checkConflict($booking['facility_id'], $booking['start_datetime'], $booking['end_datetime'])) {
             return redirect()->back()->with('error', 'Cannot approve: There is already an approved booking for this facility that overlaps with this time.');
        }

        $this->bookingModel->update($id, ['status' => 'Approved']);

        // Notify resident
        if (!empty($booking['resident_id'])) {
            $this->notificationModel->insert([
                'resident_id' => $booking['resident_id'],
                'type'        => 'booking',
                'title'       => 'Facility Booking Approved',
                'message'     => 'Your request to book the facility has been approved.',
                'status'      => 'sent'
            ]);
        }

        return redirect()->back()->with('success', 'Booking approved successfully.');
    }

    public function reject($id)
    {
        $booking = $this->bookingModel->find($id);
        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found.');
        }

        $remarks = $this->request->getPost('remarks');

        $this->bookingModel->update($id, [
            'status' => 'Rejected',
            'remarks' => $remarks
        ]);

        // Notify resident
        if (!empty($booking['resident_id'])) {
            $this->notificationModel->insert([
                'resident_id' => $booking['resident_id'],
                'type'        => 'booking',
                'title'       => 'Facility Booking Rejected',
                'message'     => 'Your request to book the facility has been rejected. Reason: ' . ($remarks ?: 'None provided'),
                'status'      => 'sent'
            ]);
        }

        return redirect()->back()->with('success', 'Booking rejected successfully.');
    }
}
