<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * DocumentModel
 * Manages document attachments and file versioning
 */
class DocumentModel extends Model
{
    protected $table = 'documents';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'entity_type',
        'entity_id',
        'document_type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'file_hash',
        'version',
        'is_active',
        'access_level',
        'uploaded_by',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'entity_type' => 'required|max_length[50]',
        'entity_id' => 'required|integer',
        'document_type' => 'required|max_length[100]',
        'file_name' => 'required|max_length[255]',
        'file_path' => 'required|max_length[500]',
        'file_size' => 'required|integer',
        'mime_type' => 'required|max_length[100]',
        'file_hash' => 'required|max_length[64]',
        'uploaded_by' => 'required|integer',
    ];

    /**
     * Get documents for an entity
     */
    public function getForEntity(string $entityType, int $entityId, bool $activeOnly = true): array
    {
        $builder = $this->where('entity_type', $entityType)
                       ->where('entity_id', $entityId);
        
        if ($activeOnly) {
            $builder->where('is_active', 1);
        }

        return $builder->orderBy('version', 'DESC')
                      ->orderBy('created_at', 'DESC')
                      ->findAll();
    }

    /**
     * Get the latest (active) document per document_type for an entity.
     *
     * MVP approach: fetch active docs ordered by document_type + version desc,
     * then keep the first row per type.
     */
    public function getLatestForEntity(string $entityType, int $entityId): array
    {
        $rows = $this->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('is_active', 1)
            ->orderBy('document_type', 'ASC')
            ->orderBy('version', 'DESC')
            ->findAll();

        $latest = [];
        foreach ($rows as $row) {
            $type = (string) ($row['document_type'] ?? '');
            if ($type === '' || isset($latest[$type])) {
                continue;
            }
            $latest[$type] = $row;
        }

        return array_values($latest);
    }

    /**
     * Upload new document
     */
    public function uploadDocument(array $documentData, $uploadedFile): array
    {
        // Generate file hash
        $fileHash = hash_file('sha256', $uploadedFile->getTempName());
        
        // Check for duplicates (scoped to same entity + document_type to avoid false positives)
        $existing = $this->where('entity_type', $documentData['entity_type'])
            ->where('entity_id', (int) $documentData['entity_id'])
            ->where('document_type', $documentData['document_type'])
            ->where('file_hash', $fileHash)
            ->first();
        if ($existing) {
            throw new \Exception('Duplicate file detected');
        }

        // Generate unique filename
        $fileName = $uploadedFile->getClientName();
        $extension = $uploadedFile->getClientExtension();
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $uniqueName = $baseName . '_' . time() . '.' . $extension;

        // Create directory structure
        $uploadPath = WRITEPATH . 'uploads/documents/' . $documentData['entity_type'] . '/' . $documentData['entity_id'] . '/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Move file
        $filePath = $uploadPath . $uniqueName;
        if (!$uploadedFile->move($uploadPath, $uniqueName)) {
            throw new \Exception('Failed to move uploaded file');
        }

        // Get next version number
        $lastVersion = $this->where('entity_type', $documentData['entity_type'])
                           ->where('entity_id', $documentData['entity_id'])
                           ->where('document_type', $documentData['document_type'])
                           ->selectMax('version')
                           ->first();
        
        $nextVersion = ($lastVersion['version'] ?? 0) + 1;

        // Deactivate previous active versions for this (entity + document_type)
        $this->where('entity_type', $documentData['entity_type'])
            ->where('entity_id', $documentData['entity_id'])
            ->where('document_type', $documentData['document_type'])
            ->set(['is_active' => 0])
            ->update();

        // Save to database
        $documentRecord = [
            'entity_type' => $documentData['entity_type'],
            'entity_id' => $documentData['entity_id'],
            'document_type' => $documentData['document_type'],
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_size' => $uploadedFile->getSize(),
            'mime_type' => $uploadedFile->getMimeType(),
            'file_hash' => $fileHash,
            'version' => $nextVersion,
            'is_active' => 1,
            'access_level' => $documentData['access_level'] ?? 'internal',
            'uploaded_by' => $documentData['uploaded_by'],
        ];

        $documentId = $this->insert($documentRecord);
        
        return $this->find($documentId);
    }

    /**
     * Create a document record from an already-existing file path on disk.
     * Useful for generated PDFs (certificates, reports) without going through HTTP upload.
     *
     * @param array<string, mixed> $documentData entity_type, entity_id, document_type, access_level, uploaded_by
     */
    public function createFromPath(array $documentData, string $filePath, string $originalFilename, string $mimeType): array
    {
        if (!is_file($filePath)) {
            throw new \Exception('File not found: ' . $filePath);
        }

        $fileHash = hash_file('sha256', $filePath);

        // Scoped duplicate check (same entity + document_type)
        $existing = $this->where('entity_type', $documentData['entity_type'])
            ->where('entity_id', (int) $documentData['entity_id'])
            ->where('document_type', $documentData['document_type'])
            ->where('file_hash', $fileHash)
            ->first();
        if ($existing) {
            return $existing;
        }

        $lastVersion = $this->where('entity_type', $documentData['entity_type'])
            ->where('entity_id', (int) $documentData['entity_id'])
            ->where('document_type', $documentData['document_type'])
            ->selectMax('version')
            ->first();

        $nextVersion = ((int) ($lastVersion['version'] ?? 0)) + 1;

        // Deactivate previous active versions
        $this->where('entity_type', $documentData['entity_type'])
            ->where('entity_id', (int) $documentData['entity_id'])
            ->where('document_type', $documentData['document_type'])
            ->set(['is_active' => 0])
            ->update();

        $record = [
            'entity_type'   => $documentData['entity_type'],
            'entity_id'     => (int) $documentData['entity_id'],
            'document_type' => $documentData['document_type'],
            'file_name'     => $originalFilename,
            'file_path'     => $filePath,
            'file_size'     => (int) filesize($filePath),
            'mime_type'     => $mimeType,
            'file_hash'     => $fileHash,
            'version'       => $nextVersion,
            'is_active'     => 1,
            'access_level'  => $documentData['access_level'] ?? 'internal',
            'uploaded_by'   => (int) ($documentData['uploaded_by'] ?? 0),
        ];

        $id = $this->insert($record);
        return $this->find($id);
    }

    /**
     * Get document by hash (for deduplication)
     */
    public function getByHash(string $hash): ?array
    {
        return $this->where('file_hash', $hash)->first();
    }

    /**
     * Archive old versions
     */
    public function archiveOldVersions(string $entityType, int $entityId, string $documentType, int $keepVersions = 3): int
    {
        $documents = $this->where('entity_type', $entityType)
                         ->where('entity_id', $entityId)
                         ->where('document_type', $documentType)
                         ->where('is_active', 1)
                         ->orderBy('version', 'DESC')
                         ->findAll();

        $archived = 0;
        if (count($documents) > $keepVersions) {
            $toArchive = array_slice($documents, $keepVersions);
            
            foreach ($toArchive as $document) {
                $this->update($document['id'], ['is_active' => 0]);
                $archived++;
            }
        }

        return $archived;
    }

    /**
     * Get storage statistics
     */
    public function getStorageStats(): array
    {
        $stats = $this->select('
            COUNT(*) as total_files,
            SUM(file_size) as total_size,
            AVG(file_size) as avg_size,
            COUNT(DISTINCT entity_type) as entity_types,
            COUNT(DISTINCT document_type) as document_types
        ')->where('is_active', 1)->first();

        return $stats;
    }

    /**
     * Clean up orphaned files
     */
    public function cleanupOrphanedFiles(): array
    {
        $documents = $this->where('is_active', 0)
                         ->where('created_at <', date('Y-m-d H:i:s', strtotime('-30 days')))
                         ->findAll();

        $cleaned = [];
        foreach ($documents as $document) {
            if (file_exists($document['file_path'])) {
                if (unlink($document['file_path'])) {
                    $cleaned[] = $document['file_path'];
                    $this->delete($document['id']);
                }
            } else {
                // File already doesn't exist, remove from database
                $this->delete($document['id']);
                $cleaned[] = $document['file_path'] . ' (already missing)';
            }
        }

        return $cleaned;
    }
}
