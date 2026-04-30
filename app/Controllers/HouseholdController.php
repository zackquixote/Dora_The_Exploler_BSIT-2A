<?php

namespace App\Controllers;

use App\Models\HouseholdModel;
use App\Models\ResidentModel;

/**
 * Household Controller
 * 
 * Manages households: listing, creating, editing, viewing, and deleting.
 * Also handles AJAX endpoints for residents by sitio, household number checks, etc.
 * 
 * METHODS (key):
 * - index(): Lists households with optional purok filter.
 * - create(): Shows form with auto-generated household number.
 * - store(): Saves new household, assigns members, logs activity.
 * - edit($id): Displays edit form with current members.
 * - update($id): Updates household and member assignments.
 * - view($id): Shows household details and member list.
 * - delete($id): AJAX delete with force option to transfer residents.
 * 
 * AJAX METHODS:
 * - getMembers($householdId)
 * - getResidentsBySitio()
 * - getHouseholdsBySitio()
 * - getNextHouseholdNo()
 * - checkHouseholdNo()
 * - getDetails($id)
 * 
 * DEPENDENCIES:
 * - HouseholdModel, ResidentModel, Database connection
 * 
 * @package App\Controllers
 */
class HouseholdController extends BaseController
{
    protected $householdModel;
    protected $residentModel;
    protected $db;

    public function __construct()
    {
        $this->householdModel = new HouseholdModel();
        $this->residentModel  = new ResidentModel();
        $this->db             = \Config\Database::connect();
    }

    // ── Auth helper ───────────────────────────────────────────────────
    private function requireLogin()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
        return null;
    }

    // ── Index ─────────────────────────────────────────────────────────
    public function index()
    {
        if ($r = $this->requireLogin()) return $r;

        $selectedPurok = $this->request->getGet('purok') ?? 'all';

        $builder = $this->db->table('households')->select('*');
        if ($selectedPurok !== 'all') {
            $builder->where('sitio', $selectedPurok);
        }
        $builder->orderBy('household_no', 'ASC');
        $households = $builder->get()->getResultArray();

        $householdsData = [];
        foreach ($households as $h) {
            $residentCount = $this->db->table('residents')
                ->where('household_id', $h['id'])
                ->where('deleted_at', null)
                ->countAllResults();

            $headName = 'Not assigned';
            if (!empty($h['head_resident_id'])) {
                $head = $this->residentModel->find($h['head_resident_id']);
                if ($head) {
                    $headName = $head['first_name'] . ' ' . $head['last_name'];
                }
            }

            $householdsData[] = [
                'id'             => $h['id'],
                'household_no'   => $h['household_no'],
                'sitio'          => $h['sitio'] ?? 'Unassigned',
                'address'        => $h['address'] ?? '',
                'street_address' => $h['street_address'] ?? '',
                'head_name'      => $headName,
                'resident_count' => $residentCount,
                'house_type'     => $h['house_type'] ?? 'N/A',
            ];
        }

        $totalHouseholds = count($householdsData);
        $totalResidents  = $this->db->table('residents')->where('deleted_at', null)->countAllResults();
        $avgPerHousehold = $totalHouseholds > 0 ? round($totalResidents / $totalHouseholds, 1) : 0;

        return view('households/index', [
            'title'           => 'Households',
            'households'      => $householdsData,
            'selectedPurok'   => $selectedPurok,
            'totalHouseholds' => $totalHouseholds,
            'totalResidents'  => $totalResidents,
            'avgPerHousehold' => $avgPerHousehold,
        ]);
    }

    // ── Create ────────────────────────────────────────────────────────
    public function create()
    {
        if ($r = $this->requireLogin()) return $r;

        $year  = date('Y');
        $count = $this->householdModel->like('household_no', "HH-{$year}-%")->countAllResults();

        $nextNumber           = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        $generatedHouseholdNo = "HH-{$year}-{$nextNumber}";

        while ($this->householdModel->where('household_no', $generatedHouseholdNo)->first()) {
            $count++;
            $nextNumber           = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            $generatedHouseholdNo = "HH-{$year}-{$nextNumber}";
        }

        return view('households/create', [
            'title'                => 'Add Household',
            'generatedHouseholdNo' => $generatedHouseholdNo,
        ]);
    }

    // ── Store ─────────────────────────────────────────────────────────
    public function store()
    {
        if ($r = $this->requireLogin()) return $r;

        $householdNo = $this->request->getPost('household_no');

        if (empty($householdNo)) {
            $year        = date('Y');
            $count       = $this->householdModel->like('household_no', "HH-{$year}-%")->countAllResults();
            $nextNumber  = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            $householdNo = "HH-{$year}-{$nextNumber}";

            while ($this->householdModel->where('household_no', $householdNo)->first()) {
                $count++;
                $nextNumber  = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
                $householdNo = "HH-{$year}-{$nextNumber}";
            }
        }

        $rules = [
            'household_no'     => 'required|is_unique[households.household_no]',
            'sitio'            => 'required',
            'address'          => 'permit_empty|max_length[255]',
            'street_address'   => 'permit_empty|max_length[255]',
            'head_resident_id' => 'permit_empty|integer',
            'house_type'       => 'permit_empty|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'household_no'     => $householdNo,
            'sitio'            => $this->request->getPost('sitio'),
            'address'          => $this->request->getPost('address'),
            'street_address'   => $this->request->getPost('street_address'),
            'head_resident_id' => $this->request->getPost('head_resident_id') ?: null,
            'house_type'       => $this->request->getPost('house_type'),
        ];

        if ($this->householdModel->insert($data)) {
            $householdId = $this->householdModel->getInsertID();
            $membersData = $this->request->getPost('household_members_data');
            $members     = json_decode($membersData, true) ?? [];
            $headId      = $this->request->getPost('head_resident_id');

            if ($headId && !isset($members[$headId])) {
                $members[$headId] = ['id' => $headId, 'relationship' => 'Head'];
            }

            $memberCount = 0;
            foreach ($members as $memberId => $memberInfo) {
                $updateData = [
                    'household_id'          => $householdId,
                    'relationship_to_head'  => $memberInfo['relationship'] ?? null,
                    'is_household_head'     => ($headId == $memberId) ? 1 : 0,
                    'member_status'         => 'Active',
                    'joined_household_date' => date('Y-m-d'),
                ];
                if ($this->residentModel->update($memberId, $updateData)) $memberCount++;
            }

            $message = "Household {$householdNo} added successfully";
            if ($memberCount > 0) $message .= " with {$memberCount} member(s)";

            return redirect()->to('/households')->with('success', $message);
        }

        return redirect()->back()->withInput()->with('error', 'Failed to add household. Please try again.');
    }

    // ── Edit ──────────────────────────────────────────────────────────
    public function edit($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $household = $this->householdModel->find($id);
        if (!$household) {
            return redirect()->to('/households')->with('error', 'Household not found');
        }

        $residentCount = $this->db->table('residents')
            ->where('household_id', $id)
            ->where('deleted_at', null)
            ->countAllResults();

        $currentMembers = $this->db->table('residents')
            ->where('household_id', $id)
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        return view('households/edit', [
            'title'          => 'Edit Household',
            'household'      => $household,
            'residentCount'  => $residentCount,
            'currentMembers' => $currentMembers,
        ]);
    }

    // ── Update ────────────────────────────────────────────────────────
    public function update($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $household = $this->householdModel->find($id);
        if (!$household) {
            return redirect()->to('/households')->with('error', 'Household not found');
        }

        $rules = [
            'household_no'     => "required|is_unique[households.household_no,id,{$id}]",
            'sitio'            => 'required',
            'address'          => 'permit_empty|max_length[255]',
            'street_address'   => 'permit_empty|max_length[255]',
            'head_resident_id' => 'permit_empty|integer',
            'house_type'       => 'permit_empty|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'household_no'     => $this->request->getPost('household_no'),
            'sitio'            => $this->request->getPost('sitio'),
            'address'          => $this->request->getPost('address'),
            'street_address'   => $this->request->getPost('street_address'),
            'head_resident_id' => $this->request->getPost('head_resident_id') ?: null,
            'house_type'       => $this->request->getPost('house_type'),
        ];

        if ($this->householdModel->update($id, $data)) {
            $headId      = $this->request->getPost('head_resident_id');
            $membersData = $this->request->getPost('household_members_data');
            $members     = json_decode($membersData, true) ?? [];

            $currentMembers   = $this->residentModel->where('household_id', $id)->where('deleted_at', null)->findAll();
            $currentMemberIds = array_column($currentMembers, 'id');
            $newMemberIds     = array_keys($members);
            $removedMemberIds = array_diff($currentMemberIds, $newMemberIds);

            if (!empty($removedMemberIds)) {
                $this->residentModel->whereIn('id', $removedMemberIds)->set([
                    'household_id'       => null,
                    'is_household_head'  => 0,
                    'member_status'      => 'Transferred',
                    'left_household_date' => date('Y-m-d'),
                ])->update();
            }

            $memberCount = 0;
            foreach ($members as $memberId => $memberInfo) {
                $updateData = [
                    'household_id'         => $id,
                    'relationship_to_head' => $memberInfo['relationship'] ?? null,
                    'is_household_head'    => ($headId == $memberId) ? 1 : 0,
                    'member_status'        => 'Active',
                ];
                if (!in_array($memberId, $currentMemberIds)) {
                    $updateData['joined_household_date'] = date('Y-m-d');
                }
                if ($this->residentModel->update($memberId, $updateData)) $memberCount++;
            }

            return redirect()->to('/households')->with('success', "Household updated successfully with {$memberCount} active member(s)");
        }

        return redirect()->back()->with('error', 'Failed to update household');
    }

    // ── View ──────────────────────────────────────────────────────────
    public function view($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $household = $this->householdModel->find($id);
        if (!$household) {
            return redirect()->to('/households')->with('error', 'Household not found');
        }

        $headResident = null;
        if (!empty($household['head_resident_id'])) {
            $headResident = $this->residentModel->find($household['head_resident_id']);
        }

        $residents = $this->db->table('residents')
            ->where('household_id', $id)
            ->where('deleted_at', null)
            ->orderBy('is_household_head', 'DESC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        return view('households/view', [
            'title'         => 'Household Details',
            'household'     => $household,
            'headResident'  => $headResident,
            'residents'     => $residents,
            'residentCount' => count($residents),
        ]);
    }

    // ── Delete (AJAX) ─────────────────────────────────────────────────
    public function delete($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $household = $this->householdModel->find($id);
        if (!$household) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Household not found', 'csrf_hash' => csrf_hash()]);
        }

        $hasResidents = $this->db->table('residents')
            ->where('household_id', $id)
            ->where('deleted_at', null)
            ->countAllResults();

        $force = $this->request->getPost('force') === 'true';

        if ($hasResidents > 0 && !$force) {
            return $this->response->setJSON([
                'status'    => 'error',
                'message'   => "Cannot delete household with {$hasResidents} resident(s). Please transfer or delete residents first.",
                'csrf_hash' => csrf_hash(),
            ]);
        }

        if ($force && $hasResidents > 0) {
            $this->db->table('residents')
                ->where('household_id', $id)
                ->where('deleted_at', null)
                ->update([
                    'household_id'       => null,
                    'is_household_head'  => 0,
                    'member_status'      => 'Transferred',
                    'left_household_date' => date('Y-m-d'),
                ]);
        }

        if ($this->householdModel->delete($id)) {
            $message = 'Household deleted successfully';
            if ($force && $hasResidents > 0) $message .= ". {$hasResidents} resident(s) transferred.";
            return $this->response->setJSON(['status' => 'success', 'message' => $message, 'csrf_hash' => csrf_hash()]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Delete failed', 'csrf_hash' => csrf_hash()]);
    }

    // ── AJAX: Get members of a household ──────────────────────────────
    public function getMembers($householdId)
    {
        try {
            $members = $this->residentModel->where('household_id', $householdId)->where('deleted_at', null)->findAll();
            return $this->response->setJSON(['status' => 'success', 'members' => $members, 'count' => count($members), 'csrf_hash' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    // ── AJAX: Residents by sitio ──────────────────────────────────────
    public function getResidentsBySitio()
    {
        $sitio = $this->request->getPost('sitio');
        if (empty($sitio)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sitio parameter is required']);
        }

        try {
            $residents = $this->db->table('residents')
                ->select('id, first_name, middle_name, last_name, sex, sitio as resident_sitio')
                ->where('deleted_at', null)
                ->groupStart()
                    ->where('sitio', $sitio)
                    ->orWhere('sitio', null)
                    ->orWhere('sitio', '')
                ->groupEnd()
                ->orderBy('last_name', 'ASC')
                ->get()->getResultArray();

            return $this->response->setJSON(['status' => 'success', 'residents' => $residents, 'count' => count($residents), 'csrf_hash' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    // ── AJAX: Households by sitio ─────────────────────────────────────
    public function getHouseholdsBySitio()
    {
        $sitio = $this->request->getGet('sitio') ?? $this->request->getPost('sitio');
        if (empty($sitio)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sitio is required']);
        }

        try {
            $households = $this->householdModel->where('sitio', $sitio)->orderBy('household_no', 'ASC')->findAll();
            return $this->response->setJSON(['status' => 'success', 'data' => $households, 'count' => count($households), 'csrf_hash' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    // ── AJAX: Get by sitio (alternate endpoint) ───────────────────────
    public function getBySitio()
    {
        $sitio = $this->request->getPost('sitio');
        if ($sitio) {
            $households = $this->householdModel->where('sitio', $sitio)->orderBy('household_no', 'ASC')->findAll();
            return $this->response->setJSON(['status' => 'success', 'households' => $households, 'csrf_hash' => csrf_hash()]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
    }

    // ── AJAX: Next household number ───────────────────────────────────
    public function getNextHouseholdNo()
    {
        $year        = date('Y');
        $count       = $this->householdModel->like('household_no', "HH-{$year}-%")->countAllResults();
        $nextNumber  = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        $householdNo = "HH-{$year}-{$nextNumber}";

        while ($this->householdModel->where('household_no', $householdNo)->first()) {
            $count++;
            $nextNumber  = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            $householdNo = "HH-{$year}-{$nextNumber}";
        }

        return $this->response->setJSON(['status' => 'success', 'household_no' => $householdNo, 'csrf_hash' => csrf_hash()]);
    }

    // ── AJAX: Check if household number exists ────────────────────────
    public function checkHouseholdNo()
    {
        $householdNo = $this->request->getGet('household_no');
        if (empty($householdNo)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Household number required']);
        }

        $exists = $this->householdModel->where('household_no', $householdNo)->first();
        return $this->response->setJSON([
            'status'    => 'success',
            'exists'    => (bool) $exists,
            'message'   => $exists ? 'Household number already exists' : 'Household number is available',
            'csrf_hash' => csrf_hash(),
        ]);
    }

    // ── AJAX: Get household details ───────────────────────────────────
    public function getDetails($id)
    {
        $household = $this->householdModel->find($id);
        if (!$household) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Household not found']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $household, 'csrf_hash' => csrf_hash()]);
    }
}