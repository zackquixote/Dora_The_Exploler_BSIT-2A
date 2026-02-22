<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use App\Models\LogModel;
use App\Models\PersonModel;

class Person extends Controller
{
    public function index(){
        $model = new PersonModel();
        $data['person'] = $model->findAll();
        return view('person/index', $data);
    }

//working on this one
    public function save(){
        $name = $this->request->getPost('name');
        $bday = $this->request->getPost('bday');

        $userModel = new \App\Models\PersonModel();
        $logModel = new LogModel();

        $data = [
            'name'       => $name,
            'bday'      => $bday
        ];

        if ($userModel->insert($data) !== false) {
            $logModel->addLog('New Person has been added: ' . $name, 'ADD');
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save person']);
        }
    }

//error handling for update, if id is not found, return error message
 public function update(){
    $model = new PersonModel(); 
    $logModel = new LogModel();

    $userId = $this->request->getPost('id');

    $data = [
        'name' => $this->request->getPost('name'),
        'bday' => $this->request->getPost('bday'),
    ];

    if ($model->update($userId, $data)) {
        $logModel->addLog('Person updated: ' . $data['name'], 'UPDATED');

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Person updated successfully.'
        ]);
    }

    return $this->response->setJSON([
        'success' => false,
        'message' => 'Error updating person.'
    ]);
}

    public function edit($id){
        $model = new PersonModel();
    $user = $model->find($id); // Fetch user by ID

    if ($user) {
        return $this->response->setJSON(['data' => $user]); // Return user data as JSON
    } else {
        return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
    }
}

//error handling for delete, if name is not found, return error message
public function delete($name){
    $model = new PersonModel();
    $logModel = new LogModel();

    $deleteName = $name ?? $this->request->getPost('name'); // one variable is enough

    $deleted = $model->where('name', $deleteName)->delete();

    if ($deleted) {
        $logModel->addLog('Person deleted: ' . $deleteName, 'DELETE');
        return $this->response->setJSON(['success' => true, 'message' => 'Person deleted successfully.']);
    } else {
        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete person.']);
    }
}


//working on this one
public function fetchRecords(){
    $request = service('request');
    $model = new \App\Models\PersonModel();

    $start = $request->getPost('start') ?? 0;
    $length = $request->getPost('length') ?? 10;
    $searchValue = $request->getPost('search')['value'] ?? '';

    $totalRecords = $model->countAll();
    $result = $model->getRecords($start, $length, $searchValue);

    $data = [];
    $counter = $start + 1;
    foreach ($result['data'] as $row) {
        $row['row_number'] = $counter++;
        $data[] = $row;
    }

    return $this->response->setJSON([
        'draw' => intval($request->getPost('draw')),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $result['filtered'],
        'data' => $data,
    ]);
}
}