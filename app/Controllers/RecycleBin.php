<?php

namespace App\Controllers;

use App\Models\ResidentModel;
use App\Models\HouseholdModel;
use App\Models\LogModel;

class RecycleBin extends BaseController
{
    protected $residentModel;
    protected $householdModel;
    protected $logModel;

    public function __construct()
    {
        $this->residentModel = new ResidentModel();
        $this->householdModel = new HouseholdModel();
        $this->logModel = new LogModel();
    }

    public function index()
    {
        if ($r = $this->requireLogin()) return $r;

        $deletedResidents = $this->residentModel->onlyDeleted()->findAll();
        $deletedHouseholds = $this->householdModel->onlyDeleted()->findAll();

        return view('recycle_bin/index', [
            'title' => 'Recycle Bin',
            'deletedResidents' => $deletedResidents,
            'deletedHouseholds' => $deletedHouseholds
        ]);
    }

    public function restoreResident($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $this->residentModel->update($id, ['deleted_at' => null]);
        $this->logModel->addLog("Restored Resident ID: {$id}");

        return redirect()->back()->with('success', 'Resident restored successfully.');
    }

    public function forceDeleteResident($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $this->residentModel->delete($id, true);
        $this->logModel->addLog("Permanently Deleted Resident ID: {$id}");

        return redirect()->back()->with('success', 'Resident permanently deleted.');
    }

    public function restoreHousehold($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $this->householdModel->update($id, ['deleted_at' => null]);
        $this->logModel->addLog("Restored Household ID: {$id}");

        return redirect()->back()->with('success', 'Household restored successfully.');
    }

    public function forceDeleteHousehold($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $this->householdModel->delete($id, true);
        $this->logModel->addLog("Permanently Deleted Household ID: {$id}");

        return redirect()->back()->with('success', 'Household permanently deleted.');
    }
}
