<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;

class Announcements extends BaseController
{
    public function index()
    {
        $model = new AnnouncementModel();
        
        $announcements = $model->orderBy('is_pinned', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('Admin/announcements/index', [
            'title' => 'Announcements',
            'announcements' => $announcements
        ]);
    }

    public function create()
    {
        if (strtolower((string)$this->request->getMethod()) === 'post') {
            $model = new AnnouncementModel();
            
            $data = [
                'title'      => $this->request->getPost('title'),
                'body'       => $this->request->getPost('body'),
                'is_pinned'  => $this->request->getPost('is_pinned') ? 1 : 0,
                'created_by' => session()->get('user_id') ?? session()->get('id')
            ];

            // Handle image upload
            $file = $this->request->getFile('image');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/announcements', $newName);
                $data['image_url'] = 'uploads/announcements/' . $newName;
            }

            if ($model->insert($data)) {
                return redirect()->to(base_url('admin/announcements'))->with('success', 'Announcement posted successfully.');
            }
            return redirect()->back()->with('error', 'Failed to post announcement.');
        }

        return view('Admin/announcements/create', ['title' => 'Create Announcement']);
    }

    public function delete($id)
    {
        $model = new AnnouncementModel();
        $announcement = $model->find($id);

        if ($announcement) {
            // Delete image if exists
            if (!empty($announcement['image_url']) && file_exists(FCPATH . $announcement['image_url'])) {
                unlink(FCPATH . $announcement['image_url']);
            }
            $model->delete($id);
            return redirect()->to(base_url('admin/announcements'))->with('success', 'Announcement deleted.');
        }
        
        return redirect()->back()->with('error', 'Announcement not found.');
    }
}
