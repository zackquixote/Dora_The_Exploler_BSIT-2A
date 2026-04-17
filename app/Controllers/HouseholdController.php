<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\HouseholdModel;

class HouseholdController extends BaseController
{
    public function index()
    {
        return view('households/index');
    }


    public function create()
    {
        return view('households/create');
    }

    public function edit($id)
    {
        $model = new HouseholdModel();

        $data['household'] = $model->find($id);

        return view('households/edit', $data);
    }
public function list()
{
    $db = \Config\Database::connect();

    $residents = $db->table('residents r')
        ->select('
            r.id,
            CONCAT(r.first_name, " ", IFNULL(r.middle_name, ""), " ", r.last_name) AS full_name,
            r.sex,
            r.birthdate,
            r.civil_status,
            h.household_no,
            r.occupation,
            r.citizenship,
            r.is_voter,
            r.is_senior_citizen,
            r.is_pwd
        ')
        ->join('households h', 'h.id = r.household_id', 'left')
        ->orderBy('r.last_name', 'ASC')
        ->get()
        ->getResultArray();

    return $this->response->setJSON([
        'data' => $residents
    ]);
}
    public function residentsOptions()
    {
        $db = \Config\Database::connect();

        return $this->response->setJSON([
            'data' => $db->table('residents')
                ->select('id, first_name, last_name')
                ->orderBy('last_name', 'ASC')
                ->get()
                ->getResult()
        ]);
    }

    public function store()
    {
        $model = new HouseholdModel();

        $model->insert($this->request->getPost());

        return redirect()->to('/households');
    }

    public function update($id)
    {
        $model = new HouseholdModel();

        $model->update($id, $this->request->getPost());

        return redirect()->to('/households');
    }

    public function delete($id)
    {
        $model = new HouseholdModel();

        $model->delete($id);

        return $this->response->setJSON([
            'status' => 'success'
        ]);
    }
    
}