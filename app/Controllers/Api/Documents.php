<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\DocumentModel;
use App\Services\AuditService;
use CodeIgniter\Exceptions\PageNotFoundException;

class Documents extends BaseController
{
    protected DocumentModel $documents;
    protected AuditService $audit;

    public function __construct()
    {
        $this->documents = new DocumentModel();
        $this->audit     = new AuditService();
    }

    /**
     * GET /api/documents/{id}
     */
    public function show(int $id)
    {
        $doc = $this->documents->find($id);
        if (! $doc) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Document not found', 'csrf_hash' => csrf_hash()]);
        }

        if (! $this->canView((string) ($doc['access_level'] ?? 'internal'))) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Forbidden', 'csrf_hash' => csrf_hash()]);
        }

        unset($doc['file_path'], $doc['file_hash']);
        return $this->response->setJSON(['document' => $doc, 'csrf_hash' => csrf_hash()]);
    }

    /**
     * GET /api/documents/{id}/versions
     * Version history for the same (entity_type, entity_id, document_type) group.
     */
    public function versions(int $id)
    {
        $doc = $this->documents->find($id);
        if (! $doc) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Document not found', 'csrf_hash' => csrf_hash()]);
        }

        if (! $this->canView((string) ($doc['access_level'] ?? 'internal'))) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Forbidden', 'csrf_hash' => csrf_hash()]);
        }

        $rows = $this->documents
            ->where('entity_type', $doc['entity_type'])
            ->where('entity_id', (int) $doc['entity_id'])
            ->where('document_type', $doc['document_type'])
            ->orderBy('version', 'DESC')
            ->findAll();

        foreach ($rows as &$r) {
            unset($r['file_path'], $r['file_hash']);
        }

        return $this->response->setJSON(['items' => $rows, 'csrf_hash' => csrf_hash()]);
    }

    /**
     * POST /api/documents/{id}/versions
     * Upload a new version (same group: entity_type/entity_id/document_type).
     * Multipart: file/document
     */
    public function uploadVersion(int $id)
    {
        $doc = $this->documents->find($id);
        if (! $doc) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Document not found', 'csrf_hash' => csrf_hash()]);
        }

        // Uploading a new version is staff/admin only (MVP)
        if (! $this->canWrite()) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Forbidden', 'csrf_hash' => csrf_hash()]);
        }

        $file = $this->request->getFile('file') ?? $this->request->getFile('document');
        if (! $file || ! $file->isValid()) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Invalid file', 'csrf_hash' => csrf_hash()]);
        }

        try {
            $newDoc = $this->documents->uploadDocument([
                'entity_type'    => $doc['entity_type'],
                'entity_id'      => (int) $doc['entity_id'],
                'document_type'  => $doc['document_type'],
                'access_level'   => $this->request->getPost('access_level') ?? $doc['access_level'] ?? 'internal',
                'uploaded_by'    => (int) (session()->get('user_id') ?? 0),
            ], $file);

            $this->audit->log('new_version', 'document', (int) ($newDoc['id'] ?? 0), $doc, $newDoc);

            unset($newDoc['file_path'], $newDoc['file_hash']);
            return $this->response->setStatusCode(201)->setJSON(['document' => $newDoc, 'csrf_hash' => csrf_hash()]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(400)->setJSON(['message' => $e->getMessage(), 'csrf_hash' => csrf_hash()]);
        }
    }

    /**
     * GET /api/documents/{id}/download
     */
    public function download(int $id)
    {
        $doc = $this->documents->find($id);
        if (! $doc) {
            throw new PageNotFoundException('Document not found');
        }

        if (! $this->canView((string) ($doc['access_level'] ?? 'internal'))) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }

        $path = (string) ($doc['file_path'] ?? '');
        if ($path === '' || ! is_file($path)) {
            throw new PageNotFoundException('File not found');
        }

        // Audit downloads only for staff/admin to reduce log noise (optional)
        if ($this->canWrite()) {
            $this->audit->log('download', 'document', (int) $doc['id']);
        }

        return $this->response->download($path, null);
    }

    /**
     * POST /api/documents/{id}
     * Accepts JSON or form-data: title, access_level, is_active
     */
    public function update(int $id)
    {
        if (! $this->canWrite()) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Forbidden', 'csrf_hash' => csrf_hash()]);
        }

        $doc = $this->documents->find($id);
        if (! $doc) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Document not found', 'csrf_hash' => csrf_hash()]);
        }

        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            $payload = $this->request->getPost() ?? [];
        }

        $allowed = array_intersect_key($payload, array_flip(['access_level', 'is_active', 'document_type']));
        if ($allowed === []) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'No updatable fields provided', 'csrf_hash' => csrf_hash()]);
        }

        $this->documents->update($id, $allowed);
        $updated = $this->documents->find($id);

        $this->audit->log('update', 'document', $id, $doc, $updated);

        unset($updated['file_path'], $updated['file_hash']);
        return $this->response->setJSON(['document' => $updated, 'csrf_hash' => csrf_hash()]);
    }

    /**
     * POST /api/documents/{id}/delete
     * Soft delete = set is_active=0 (MVP).
     */
    public function delete(int $id)
    {
        if (! $this->canWrite()) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Forbidden', 'csrf_hash' => csrf_hash()]);
        }

        $doc = $this->documents->find($id);
        if (! $doc) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Document not found', 'csrf_hash' => csrf_hash()]);
        }

        $this->documents->update($id, ['is_active' => 0]);
        $this->audit->log('delete', 'document', $id, $doc, ['is_active' => 0]);

        return $this->response->setJSON(['success' => true, 'csrf_hash' => csrf_hash()]);
    }

    private function canView(string $accessLevel): bool
    {
        $role = strtolower((string) (session()->get('role') ?? ''));

        if ($role === 'admin') {
            return true;
        }

        // Residents/public users: only public
        if ($role === 'resident') {
            return $accessLevel === 'public';
        }

        // Staff (default):
        return match ($accessLevel) {
            'restricted' => false,
            default      => true, // public/internal/confidential
        };
    }

    private function canWrite(): bool
    {
        $role = strtolower((string) (session()->get('role') ?? ''));
        return in_array($role, ['admin', 'staff'], true);
    }
}
