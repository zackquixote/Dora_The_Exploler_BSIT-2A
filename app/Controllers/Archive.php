<?php

namespace App\Controllers;

use App\Models\ResidentModel;
use App\Models\HouseholdModel;
use App\Models\LogModel;

class Archive extends BaseController
{
    protected $residentModel;
    protected $householdModel;
    protected $logModel;
    protected $db;

    public function __construct()
    {
        $this->residentModel  = new ResidentModel();
        $this->householdModel = new HouseholdModel();
        $this->logModel       = new LogModel();
        $this->db             = \Config\Database::connect();
    }

    private function requireLogin()
    {
        if (! session()->get('logged_in')) {
            return $this->respondLoginRequired();
        }

        return null;
    }

    public function index()
    {
        if ($r = $this->requireLogin()) return $r;

        // Get soft deleted residents
        $archivedResidents = $this->residentModel->onlyDeleted()
            ->select('residents.*, households.household_no')
            ->join('households', 'households.id = residents.household_id', 'left')
            ->orderBy('deleted_at', 'DESC')
            ->findAll();

        // Get soft deleted households
        $archivedHouseholds = $this->householdModel->onlyDeleted()
            ->orderBy('deleted_at', 'DESC')
            ->findAll();

        return view('archive/index', [
            'title' => 'Archive / Recently Deleted',
            'archivedResidents' => $archivedResidents,
            'archivedHouseholds' => $archivedHouseholds,
        ]);
    }

    public function restoreResident($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $resident = $this->db->table('residents')->where('id', $id)->get()->getRowArray();
        
        if (!$resident) {
            return redirect()->back()->with('error', 'Resident not found in archive.');
        }

        $this->db->table('residents')->where('id', $id)->update(['deleted_at' => null]);

        $fullName = $resident['first_name'] . ' ' . $resident['last_name'];
        $this->logModel->addLog('Restored Resident ' . $fullName);

        return redirect()->back()->with('success', 'Resident restored successfully.');
    }

    public function forceDeleteResident($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $resident = $this->db->table('residents')->where('id', $id)->get()->getRowArray();
        
        if (!$resident) {
            return redirect()->back()->with('error', 'Resident not found in archive.');
        }

        $this->db->table('residents')->where('id', $id)->delete();

        $fullName = $resident['first_name'] . ' ' . $resident['last_name'];
        $this->logModel->addLog('Permanently Deleted Resident ' . $fullName);

        return redirect()->back()->with('success', 'Resident permanently deleted.');
    }

    public function restoreHousehold($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $household = $this->db->table('households')->where('id', $id)->get()->getRowArray();
        
        if (!$household) {
            return redirect()->back()->with('error', 'Household not found in archive.');
        }

        $this->db->table('households')->where('id', $id)->update(['deleted_at' => null]);

        $this->logModel->addLog('Restored Household ' . $household['household_no'], 'household');

        return redirect()->back()->with('success', 'Household restored successfully.');
    }

    public function forceDeleteHousehold($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $household = $this->db->table('households')->where('id', $id)->get()->getRowArray();
        
        if (!$household) {
            return redirect()->back()->with('error', 'Household not found in archive.');
        }

        $this->db->table('households')->where('id', $id)->delete();

        $this->logModel->addLog('Permanently Deleted Household ' . $household['household_no'], 'household');

        return redirect()->back()->with('success', 'Household permanently deleted.');
    }
}
