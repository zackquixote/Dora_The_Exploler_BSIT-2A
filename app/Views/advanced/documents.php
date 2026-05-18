<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<?php
    $stats = $storage_stats ?? [];
    $totalFiles = (int) ($stats['total_files'] ?? 0);
    $totalSize = (int) ($stats['total_size'] ?? 0);
    $avgSize = (int) ($stats['avg_size'] ?? 0);
    $entityTypes = (int) ($stats['entity_types'] ?? 0);
    $documentTypes = (int) ($stats['document_types'] ?? 0);
?>

<div class="bmis-content">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:rgba(245,158,11,0.12);color:#d97706;display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fas fa-folder-open"></i>
            </div>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:24px;font-weight:800;color:var(--ink)">Document Management</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-top:2px">Upload, version, and retrieve attachments per entity</div>
            </div>
        </div>
        <div style="display:flex;gap:10px">
            <button class="ds-btn ds-btn-ghost" id="dmRefreshBtn" style="height:36px">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
    </div>

    <div class="ds-grid-4" style="margin-bottom:14px">
        <div class="ds-stat">
            <div class="ds-stat-stripe" style="background:var(--c-amber)"></div>
            <div class="ds-stat-top">
                <div class="ds-stat-icon ic-amber"><i class="fas fa-file"></i></div>
            </div>
            <div class="ds-stat-num"><?= esc($totalFiles) ?></div>
            <div class="ds-stat-label">Active Files</div>
            <div class="ds-stat-footer" style="color:var(--c-amber)">is_active=1 rows</div>
        </div>

        <div class="ds-stat">
            <div class="ds-stat-stripe str-blue"></div>
            <div class="ds-stat-top">
                <div class="ds-stat-icon ic-blue"><i class="fas fa-database"></i></div>
            </div>
            <div class="ds-stat-num" id="dmTotalSize"><?= esc($totalSize) ?></div>
            <div class="ds-stat-label">Total Size (bytes)</div>
            <div class="ds-stat-footer ft-blue">Active files only</div>
        </div>

        <div class="ds-stat">
            <div class="ds-stat-stripe str-teal"></div>
            <div class="ds-stat-top">
                <div class="ds-stat-icon ic-green"><i class="fas fa-compress-arrows-alt"></i></div>
            </div>
            <div class="ds-stat-num" id="dmAvgSize"><?= esc($avgSize) ?></div>
            <div class="ds-stat-label">Avg Size (bytes)</div>
            <div class="ds-stat-footer ft-teal">Active files only</div>
        </div>

        <div class="ds-stat">
            <div class="ds-stat-stripe str-rose"></div>
            <div class="ds-stat-top">
                <div class="ds-stat-icon" style="background:var(--c-rose-bg);color:var(--c-rose)"><i class="fas fa-sitemap"></i></div>
            </div>
            <div class="ds-stat-num"><?= esc($entityTypes) ?>/<?= esc($documentTypes) ?></div>
            <div class="ds-stat-label">Entity Types / Doc Types</div>
            <div class="ds-stat-footer" style="color:var(--c-rose)">Distinct active rows</div>
        </div>
    </div>

    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body">
            <div class="ds-filter-header"><i class="fas fa-search"></i> Find an Entity</div>
            <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:12px;align-items:end">
                <div>
                    <label class="ds-input-label">Entity Type</label>
                    <input type="text" id="dmEntityType" class="ds-input" placeholder="resident / certificate / blotter / health_record">
                </div>
                <div>
                    <label class="ds-input-label">Entity ID</label>
                    <input type="number" id="dmEntityId" class="ds-input" placeholder="e.g. 1" min="1">
                </div>
                <div style="display:flex;gap:10px">
                    <button id="dmLoadBtn" class="ds-btn ds-btn-primary" style="height:36px"><i class="fas fa-search"></i> Load</button>
                    <button id="dmClearBtn" class="ds-btn ds-btn-ghost" style="height:36px"><i class="fas fa-eraser"></i> Clear</button>
                </div>
            </div>
        </div>
    </div>

    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body">
            <div class="ds-filter-header"><i class="fas fa-upload"></i> Upload Document</div>
            <form id="dmUploadForm" enctype="multipart/form-data" autocomplete="off">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
                    <div>
                        <label class="ds-input-label">Document Type</label>
                        <input type="text" id="dmDocumentType" name="document_type" class="ds-input" placeholder="receipt / id_card / photo / requirement">
                    </div>
                    <div>
                        <label class="ds-input-label">Access Level</label>
                        <select id="dmAccessLevel" name="access_level" class="ds-select">
                            <option value="internal">internal</option>
                            <option value="public">public</option>
                            <option value="confidential">confidential</option>
                            <option value="restricted">restricted</option>
                        </select>
                    </div>
                    <div>
                        <label class="ds-input-label">File</label>
                        <input type="file" id="dmFile" name="file" class="ds-input" style="padding:8px" required>
                    </div>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px">
                    <button type="submit" id="dmUploadBtn" class="ds-btn ds-btn-primary" style="height:36px"><i class="fas fa-cloud-upload-alt"></i> Upload</button>
                </div>
            </form>
            <div style="margin-top:8px;font-size:12px;color:var(--ink-muted)">
                Uploading the same entity + document type creates a new version and deactivates the previous active one.
            </div>
        </div>
    </div>

    <div class="ds-card" style="border:none;box-shadow:0 10px 30px rgba(0,0,0,0.03);border-radius:var(--r-lg);overflow:hidden">
        <div class="ds-card-head" style="background:var(--white);padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <div class="ds-card-title"><i class="fas fa-list"></i> Latest Documents</div>
            <div id="dmResultMeta" style="font-size:12px;color:var(--ink-muted)"></div>
        </div>
        <div class="ds-card-body p0">
            <div style="overflow-x:auto">
                <table class="ds-table" id="dmTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Version</th>
                            <th>Filename</th>
                            <th>Access</th>
                            <th>Uploaded</th>
                            <th style="width:220px">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dmTbody">
                        <tr>
                            <td colspan="7" style="text-align:center;color:var(--ink-muted);padding:18px">
                                Load an entity to view documents.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
window.csrfName = "<?= csrf_token() ?>";
window.csrfHash = "<?= csrf_hash() ?>";
</script>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script defer src="<?= base_url('js/advanced/documents.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>

