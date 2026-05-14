<?php

namespace App\Controllers;

use App\Models\HouseholdModel;
use App\Models\ResidentModel;
use App\Models\LogModel;

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
    protected $logModel;
    protected $db;

    public function __construct()
    {
        $this->householdModel = new HouseholdModel();
        $this->residentModel  = new ResidentModel();
        $this->logModel       = new LogModel();
        $this->db             = \Config\Database::connect();
    }

    /**
     * Execute requireLogin functionality.
     *
     * @return mixed
     */
    private function requireLogin()
    {
        if (! session()->get('logged_in')) {
            return $this->respondLoginRequired();
        }

        return null;
    }

    /**
     * Execute generateHouseholdNo functionality.
     *
     * @return mixed
     */
    private function generateHouseholdNo()
    {
        $year = date('Y');
        $last = $this->householdModel->withDeleted()
                                     ->like('household_no', "HH-{$year}-%", 'after')
                                     ->orderBy('id', 'DESC')
                                     ->first();

        if ($last && !empty($last['household_no'])) {
            $parts = explode('-', $last['household_no']);
            $next  = intval(end($parts)) + 1;
        } else {
            $next  = 1;
        }
        return "HH-{$year}-" . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Execute index functionality.
     *
     * @return mixed
     */
    public function index()
    {
        if ($r = $this->requireLogin()) return $r;

        $selectedPurok = $this->request->getGet('purok') ?? 'all';

        $builder = $this->db->table('households')->select('*')->where('deleted_at IS NULL');
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
            $headPhoto = 'assets/img/default.png';
            if (!empty($h['head_resident_id'])) {
                $head = $this->residentModel->find($h['head_resident_id']);
                if ($head) {
                    $headName = $head['first_name'] . ' ' . $head['last_name'];
                    if (!empty($head['profile_picture'])) {
                        $headPhoto = 'uploads/' . $head['profile_picture'];
                    }
                }
            }

            $householdsData[] = [
                'id'             => $h['id'],
                'household_no'   => $h['household_no'],
                'sitio'          => $h['sitio'] ?? 'Unassigned',
                'address'        => $h['address'] ?? '',
                'street_address' => $h['street_address'] ?? '',
                'head_name'      => $headName,
                'head_photo'     => $headPhoto,
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

    /**
     * Execute create functionality.
     *
     * @return mixed
     */
    public function create()
    {
        if ($r = $this->requireLogin()) return $r;

        $generatedHouseholdNo = $this->generateHouseholdNo();

        return view('households/create', [
            'title'                => 'Add Household',
            'generatedHouseholdNo' => $generatedHouseholdNo,
        ]);
    }

    /**
     * Execute store functionality.
     *
     * @return mixed
     */
    public function store()
    {
        if ($r = $this->requireLogin()) return $r;

        $householdNo = $this->request->getPost('household_no');

        if (empty($householdNo)) {
            $householdNo = $this->generateHouseholdNo();
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
                    'joined_household_date' => date('Y-m-d'),
                ];
                if ($this->residentModel->update($memberId, $updateData)) $memberCount++;
            }

            $message = "Household {$householdNo} added successfully";
            if ($memberCount > 0) $message .= " with {$memberCount} member(s)";

            $this->logModel->addLog("Created Household {$householdNo}" . ($memberCount > 0 ? " with {$memberCount} member(s)" : ''), 'household');

            return redirect()->to('/households')->with('success', $message);
        }

        return redirect()->back()->withInput()->with('error', 'Failed to add household. Please try again.');
    }

    /**
     * Execute edit functionality.
     *
     * @return mixed
     */
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

    /**
     * Execute update functionality.
     *
     * @return mixed
     */
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
                    'left_household_date' => date('Y-m-d'),
                ])->update();
            }

            $memberCount = 0;
            foreach ($members as $memberId => $memberInfo) {
                $updateData = [
                    'household_id'         => $id,
                    'relationship_to_head' => $memberInfo['relationship'] ?? null,
                    'is_household_head'    => ($headId == $memberId) ? 1 : 0,
                ];
                if (!in_array($memberId, $currentMemberIds)) {
                    $updateData['joined_household_date'] = date('Y-m-d');
                }
                if ($this->residentModel->update($memberId, $updateData)) $memberCount++;
            }

            $this->logModel->addLog("Updated Household {$data['household_no']} with {$memberCount} active member(s)", 'household');

            return redirect()->to('/households')->with('success', "Household updated successfully with {$memberCount} active member(s)");
        }

        return redirect()->back()->with('error', 'Failed to update household');
    }

    /**
     * Execute view functionality.
     *
     * @return mixed
     */
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

    /**
     * Execute delete functionality.
     *
     * @return mixed
 */
public function delete($id)
{
    if ($r = $this->requireLogin()) return $r;

    $household = $this->householdModel->find($id);
    if (!$household) {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Household not found', 'csrf_hash' => csrf_hash()]);
        }
        return redirect()->back()->with('error', 'Household not found');
    }

    $hasResidents = $this->db->table('residents')
        ->where('household_id', $id)
        ->where('deleted_at', null)
        ->countAllResults();

    // Improved boolean conversion: accepts 'true', '1', 'yes', true
    $force = filter_var($this->request->getPost('force'), FILTER_VALIDATE_BOOLEAN);

    if ($hasResidents > 0 && !$force) {
        $msg = "Cannot delete household with {$hasResidents} resident(s). Please transfer or delete residents first.";
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'    => 'error',
                'message'   => $msg,
                'csrf_hash' => csrf_hash(),
            ]);
        }
        return redirect()->back()->with('error', $msg);
    }

    if ($force && $hasResidents > 0) {
        $this->db->table('residents')
            ->where('household_id', $id)
            ->where('deleted_at', null)
            ->update([
                'household_id'        => null,
                'is_household_head'   => 0,
                'left_household_date' => date('Y-m-d'),
            ]);
    }

    if ($this->householdModel->delete($id)) {
        $message = 'Household deleted successfully';
        if ($force && $hasResidents > 0) $message .= ". {$hasResidents} resident(s) transferred.";

        $this->logModel->addLog("Deleted Household {$household['household_no']}" . ($force && $hasResidents > 0 ? " (force: {$hasResidents} residents unlinked)" : ''), 'household');

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => $message, 'csrf_hash' => csrf_hash()]);
        }
        return redirect()->to(base_url('households'))->with('success', $message);
    }

    if ($this->request->isAJAX()) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Delete failed', 'csrf_hash' => csrf_hash()]);
    }
    return redirect()->back()->with('error', 'Delete failed');
}

    /**
     * Execute getMembers functionality.
     *
     * @return mixed
     */
    public function getMembers($householdId)
    {
        try {
            $members = $this->residentModel->where('household_id', $householdId)->where('deleted_at', null)->findAll();
            return $this->response->setJSON(['status' => 'success', 'members' => $members, 'count' => count($members), 'csrf_hash' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage(), 'csrf_hash' => csrf_hash()]);
        }
    }

    /**
     * Execute setHead functionality.
     *
     * @return mixed
     */
    public function setHead($residentId)
    {
        if ($r = $this->requireLogin()) {
            return $r;
        }

        $resident = $this->residentModel->find($residentId);
        if (!$resident || !$resident['household_id']) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Resident or Household not found', 'csrf_hash' => csrf_hash()]);
        }

        $householdId = $resident['household_id'];

        $this->db->transStart();
        
        // Remove head status from everyone in this household
        $this->db->table('residents')
            ->where('household_id', $householdId)
            ->update(['is_household_head' => 0]);

        // Set this resident as the head
        $this->residentModel->update($residentId, [
            'is_household_head' => 1,
            'relationship_to_head' => 'Head'
        ]);

        // Update the household record
        $this->householdModel->update($householdId, [
            'head_resident_id' => $residentId
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update household head', 'csrf_hash' => csrf_hash()]);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Household head updated successfully', 'csrf_hash' => csrf_hash()]);
    }

    /**

     * AJAX – Remove (unlink) a single resident from their household without deleting them.
     */
    public function removeMember($residentId)
    {
        if ($r = $this->requireLogin()) {
            return $r;
        }

        $resident = $this->residentModel->find($residentId);
        if (!$resident || !$resident['household_id']) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Resident or household not found.', 'csrf_hash' => csrf_hash()]);
        }

        $householdId = $resident['household_id'];

        // Block removing the household head — assign a new head first
        $household = $this->householdModel->find($householdId);
        if ($household && (int)$household['head_resident_id'] === (int)$residentId) {
            return $this->response->setJSON([
                'status'    => 'error',
                'message'   => 'Cannot remove the Household Head. Assign a new head first.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $this->residentModel->update($residentId, [
            'household_id'         => null,
            'is_household_head'    => 0,
            'relationship_to_head' => null,
            'left_household_date'  => date('Y-m-d'),
        ]);

        $this->logModel->addLog(
            "Removed {$resident['first_name']} {$resident['last_name']} from household #{$householdId}"
        );

        return $this->response->setJSON([
            'status'    => 'success',
            'message'   => $resident['first_name'] . ' ' . $resident['last_name'] . ' removed from household.',
            'csrf_hash' => csrf_hash(),
        ]);
    }

    /**
     * Execute getResidentsBySitio functionality.
     *
     * @return mixed
     */
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
                ->where('household_id', null) // Only residents not assigned to a household
                ->groupStart()
                    ->where('sitio', $sitio)
                    ->orWhere('sitio', null)
                    ->orWhere('sitio', '')
                ->groupEnd()
                ->orderBy('last_name', 'ASC')
                ->get()->getResultArray();

            return $this->response->setJSON(['status' => 'success', 'residents' => $residents, 'count' => count($residents), 'csrf_hash' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage(), 'csrf_hash' => csrf_hash()]);
        }
    }

    /**
     * Execute getHouseholdsBySitio functionality.
     *
     * @return mixed
     */
    public function getHouseholdsBySitio()
    {
        $sitio = $this->request->getGet('sitio') ?? $this->request->getPost('sitio');
        if (empty($sitio)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sitio is required', 'csrf_hash' => csrf_hash()]);
        }

        try {
            $households = $this->householdModel->where('sitio', $sitio)->orderBy('household_no', 'ASC')->findAll();
            return $this->response->setJSON(['status' => 'success', 'data' => $households, 'count' => count($households), 'csrf_hash' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage(), 'csrf_hash' => csrf_hash()]);
        }
    }

    /**
     * Execute getBySitio functionality.
     *
     * @return mixed
     */
    public function getBySitio()
    {
        $sitio = $this->request->getPost('sitio');
        if (empty($sitio)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sitio is required', 'csrf_hash' => csrf_hash()]);
        }

        $households = $this->householdModel
            ->where('sitio', $sitio)
            ->orderBy('household_no', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'status'     => 'success',
            'households' => $households,
            'csrf_hash'  => csrf_hash(),
        ]);
    }

    /**
     * Execute getNextHouseholdNo functionality.
     *
     * @return mixed
     */
    public function getNextHouseholdNo()
    {
        $householdNo = $this->generateHouseholdNo();

        return $this->response->setJSON(['status' => 'success', 'household_no' => $householdNo, 'csrf_hash' => csrf_hash()]);
    }

    /**
     * Execute checkHouseholdNo functionality.
     *
     * @return mixed
     */
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

    /**
     * Execute getDetails functionality.
     *
     * @return mixed
     */
    public function getDetails($id)
    {
        $household = $this->householdModel->find($id);
        if (!$household) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Household not found']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $household, 'csrf_hash' => csrf_hash()]);
    }
}