# CodeIgniter 4 — Document Management (Phase 1.1) Setup + Quick Tests

## 1) What I added
- Improved `app/Models/DocumentModel.php`
  - `getLatestForEntity()`
  - upload now **deactivates previous versions** for the same `(entity_type, entity_id, document_type)`
  - duplicate detection is now scoped to the same entity + document_type
- Added API controllers:
  - `app/Controllers/Api/EntityDocuments.php`
  - `app/Controllers/Api/Documents.php`
- Registered routes in `app/Config/Routes.php` under the authenticated group.

## 2) Database
Your project already has a migration creating the `documents` table:
`app/Database/Migrations/2026-05-17-000001_CreateAdvancedSystemTables.php`

Run migrations (if not yet):
```bash
php spark migrate
```

## 3) File storage path
Uploads are saved to:
`WRITEPATH/uploads/documents/{entity_type}/{entity_id}/`

Make sure the `writable/` folder is writable by PHP.

## 4) API endpoints (authenticated; role=admin/staff)
### List latest docs for an entity
`GET /api/entities/{entityType}/{entityId}/documents`

### Upload doc (creates a new version group by document_type)
`POST /api/entities/{entityType}/{entityId}/documents`
Multipart fields:
- `file` (or `document`)
- `document_type` (required)
- `access_level` (optional; default `internal`)

### Detach a doc row (MVP = soft remove)
`POST /api/entities/{entityType}/{entityId}/documents/{documentId}/detach`

### Document metadata
`GET /api/documents/{documentId}`

### Version history (same entity + document_type)
`GET /api/documents/{documentId}/versions`

### Upload a new version
`POST /api/documents/{documentId}/versions`
Multipart:
- `file` (or `document`)
- `access_level` (optional; defaults to current)

### Download
`GET /api/documents/{documentId}/download`

### Update (access_level / is_active / document_type)
`POST /api/documents/{documentId}`

### Soft delete (sets is_active=0)
`POST /api/documents/{documentId}/delete`

## 5) Quick manual test (browser/devtools)
1. Log in as **admin** or **staff**
2. Upload:
   - `entity_type=resident`
   - `entity_id=1`
   - `document_type=id_card`
3. Upload again with the same entity/doc_type:
   - It should create **version 2**
   - It should mark version 1 `is_active=0`, version 2 `is_active=1`
4. Call list endpoint: it should return only the latest per document_type.

