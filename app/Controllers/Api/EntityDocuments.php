<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\DocumentModel;
use App\Services\AuditService;

class EntityDocuments extends BaseController
{
    protected DocumentModel $documents;
    protected AuditService $audit;

    public function __construct()
    {
        $this->documents = new DocumentModel();
        $this->audit     = new AuditService();
    }

    /**
     * GET /api/entities/{entityType}/{entityId}/documents
     * Returns latest (active) docs per document_type for the entity.
     */
    public function index(string $entityType, int $entityId)
    {
        $rows = $this->documents->getLatestForEntity($entityType, $entityId);

        // Filter out non-viewable docs
        $items = [];
        foreach ($rows as $row) {
            if (! $this->canView((string) ($row['access_level'] ?? 'internal'))) {
                continue;
            }
            unset($row['file_path'], $row['file_hash']);
            $items[] = $row;
        }

        return $this->response->setJSON(['items' => $items, 'csrf_hash' => csrf_hash()]);
    }

    /**
     * POST /api/entities/{entityType}/{entityId}/documents
     * Multipart: file/document + document_type + access_level(optional)
     */
    public function upload(string $entityType, int $entityId)
    {
        if (! $this->canWrite()) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Forbidden', 'csrf_hash' => csrf_hash()]);
        }

        $file = $this->request->getFile('file') ?? $this->request->getFile('document');
        if (! $file || ! $file->isValid()) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Invalid file', 'csrf_hash' => csrf_hash()]);
        }

        $documentType = (string) ($this->request->getPost('document_type') ?? '');
        if ($documentType === '') {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'document_type is required', 'csrf_hash' => csrf_hash()]);
        }

        $accessLevel = (string) ($this->request->getPost('access_level') ?? 'internal');

        try {
            $doc = $this->documents->uploadDocument([
                'entity_type'   => $entityType,
                'entity_id'     => $entityId,
                'document_type' => $documentType,
                'access_level'  => $accessLevel,
                'uploaded_by'   => (int) (session()->get('user_id') ?? 0),
            ], $file);

            $this->audit->log('uploaded', 'document', (int) ($doc['id'] ?? 0), null, $doc);

            unset($doc['file_path'], $doc['file_hash']);
            return $this->response->setStatusCode(201)->setJSON(['document' => $doc, 'csrf_hash' => csrf_hash()]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(400)->setJSON(['message' => $e->getMessage(), 'csrf_hash' => csrf_hash()]);
        }
    }

    /**
     * POST /api/entities/{entityType}/{entityId}/documents/{documentId}/detach
     * MVP detach = set is_active=0 for that row.
     */
    public function detach(string $entityType, int $entityId, int $documentId)
    {
        if (! $this->canWrite()) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Forbidden', 'csrf_hash' => csrf_hash()]);
        }

        $doc = $this->documents->find($documentId);
        if (! $doc || $doc['entity_type'] !== $entityType || (int) $doc['entity_id'] !== $entityId) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Document not found', 'csrf_hash' => csrf_hash()]);
        }

        $this->documents->update($documentId, ['is_active' => 0]);
        $this->audit->log('detached', 'document', $documentId, $doc, ['is_active' => 0]);

        return $this->response->setJSON(['success' => true, 'csrf_hash' => csrf_hash()]);
    }

    private function canView(string $accessLevel): bool
    {
        $role = strtolower((string) (session()->get('role') ?? ''));

        if ($role === 'admin') {
            return true;
        }

        if ($role === 'resident') {
            return $accessLevel === 'public';
        }

        return match ($accessLevel) {
            'restricted' => false,
            default      => true,
        };
    }

    private function canWrite(): bool
    {
        $role = strtolower((string) (session()->get('role') ?? ''));
        return in_array($role, ['admin', 'staff'], true);
    }
}
