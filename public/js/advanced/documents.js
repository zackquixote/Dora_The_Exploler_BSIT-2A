(function () {
    'use strict';

    const $entityType = $('#dmEntityType');
    const $entityId = $('#dmEntityId');
    const $load = $('#dmLoadBtn');
    const $clear = $('#dmClearBtn');
    const $refresh = $('#dmRefreshBtn');

    const $uploadForm = $('#dmUploadForm');
    const $docType = $('#dmDocumentType');
    const $access = $('#dmAccessLevel');
    const $file = $('#dmFile');
    const $uploadBtn = $('#dmUploadBtn');

    const $tbody = $('#dmTbody');
    const $meta = $('#dmResultMeta');

    let current = { entityType: '', entityId: 0 };

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function setEmpty(message) {
        $tbody.html(`<tr><td colspan="7" style="text-align:center;color:var(--ink-muted);padding:18px">${escapeHtml(message)}</td></tr>`);
        $meta.text('');
    }

    function setLoading() {
        $tbody.html(
            '<tr><td colspan="7" style="text-align:center;color:var(--ink-muted);padding:18px">' +
            '<div class="ds-skeleton" style="width:100%;height:32px;margin-bottom:8px"></div>' +
            '<div class="ds-skeleton" style="width:100%;height:32px;margin-bottom:8px"></div>' +
            '<div class="ds-skeleton" style="width:75%;height:32px"></div>' +
            '</td></tr>'
        );
        $meta.text('Loading...');
    }

    function apiUrl(path) {
        return (window.baseUrl || '/') + path.replace(/^\/+/, '');
    }

    function ensureEntitySelected() {
        const entityType = ($entityType.val() || '').trim();
        const entityId = parseInt(($entityId.val() || '0'), 10);
        if (!entityType || !entityId || entityId < 1) {
            showToast('error', 'Please provide entity type and entity id.');
            return null;
        }
        return { entityType, entityId };
    }

    function renderRows(items) {
        if (!items || items.length === 0) {
            setEmpty('No documents found for this entity.');
            return;
        }

        const html = items.map(d => {
            const id = d.id;
            const download = apiUrl('api/documents/' + id + '/download');
            const uploadedAt = d.created_at || d.updated_at || '';
            const actions = `
                <a class="ds-btn ds-btn-ghost" href="${download}" target="_blank" rel="noopener" style="height:30px;padding:0 10px;font-size:12px;display:inline-flex;align-items:center;gap:6px">
                    <i class="fas fa-download"></i> Download
                </a>
                <button class="ds-btn ds-btn-ghost dmVersionsBtn" data-id="${id}" style="height:30px;padding:0 10px;font-size:12px;display:inline-flex;align-items:center;gap:6px">
                    <i class="fas fa-code-branch"></i> Versions
                </button>
                <button class="ds-btn ds-btn-ghost dmDetachBtn" data-id="${id}" style="height:30px;padding:0 10px;font-size:12px;display:inline-flex;align-items:center;gap:6px">
                    <i class="fas fa-unlink"></i> Deactivate
                </button>
            `;

            return `
                <tr>
                    <td>${escapeHtml(id)}</td>
                    <td><strong>${escapeHtml(d.document_type || '')}</strong></td>
                    <td>${escapeHtml(d.version || '')}</td>
                    <td>${escapeHtml(d.file_name || '')}</td>
                    <td>${escapeHtml(d.access_level || '')}</td>
                    <td>${escapeHtml(uploadedAt)}</td>
                    <td style="display:flex;gap:8px;flex-wrap:wrap">${actions}</td>
                </tr>
            `;
        }).join('');

        $tbody.html(html);
        $meta.text(`${items.length} item(s)`);
    }

    function loadEntityDocuments(entityType, entityId) {
        current = { entityType, entityId };
        setLoading();

        return $.get(apiUrl('api/entities/' + encodeURIComponent(entityType) + '/' + entityId + '/documents'))
            .done(function (res) {
                if (!res) {
                    renderRows([]);
                    return;
                }
                if (res.csrf_hash) refreshCsrf(res);
                renderRows(res.items || []);
            })
            .fail(function (xhr) {
                if (typeof redirectIfSessionExpired === 'function' && redirectIfSessionExpired(xhr)) return;
                showToast('error', 'Failed to load documents.');
                renderRows([]);
            });
    }

    function uploadDocument() {
        const selected = ensureEntitySelected();
        if (!selected) return $.Deferred().reject().promise();

        const documentType = ($docType.val() || '').trim();
        if (!documentType) {
            showToast('error', 'document_type is required.');
            return $.Deferred().reject().promise();
        }

        const fileEl = $file[0];
        if (!fileEl || !fileEl.files || !fileEl.files[0]) {
            showToast('error', 'Please choose a file.');
            return $.Deferred().reject().promise();
        }

        const fd = new FormData();
        fd.append('file', fileEl.files[0]);
        fd.append('document_type', documentType);
        fd.append('access_level', ($access.val() || 'internal'));
        if (window.csrfName && window.csrfHash) fd.append(window.csrfName, window.csrfHash);

        $uploadBtn.prop('disabled', true);

        return $.ajax({
            url: apiUrl('api/entities/' + encodeURIComponent(selected.entityType) + '/' + selected.entityId + '/documents'),
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            headers: { Accept: 'application/json' }
        }).done(function (res) {
            if (res && res.csrf_hash) refreshCsrf(res);
            if (!res || !res.document) {
                showToast('error', (res && res.message) ? res.message : 'Upload failed.');
                return;
            }
            showToast('success', 'Uploaded successfully.');
            $file.val('');
            loadEntityDocuments(selected.entityType, selected.entityId);
        }).fail(function (xhr) {
            if (typeof redirectIfSessionExpired === 'function' && redirectIfSessionExpired(xhr)) return;
            showToast('error', 'Upload failed.');
        }).always(function () {
            $uploadBtn.prop('disabled', false);
        });
    }

    function detachDocument(documentId) {
        if (!current.entityType || !current.entityId) {
            showToast('error', 'Load an entity first.');
            return $.Deferred().reject().promise();
        }

        return $.ajax({
            url: apiUrl('api/entities/' + encodeURIComponent(current.entityType) + '/' + current.entityId + '/documents/' + documentId + '/detach'),
            method: 'POST',
            headers: { Accept: 'application/json' },
            data: (window.csrfName && window.csrfHash) ? { [window.csrfName]: window.csrfHash } : {}
        }).done(function (res) {
            if (res && res.csrf_hash) refreshCsrf(res);
            if (!res || !res.success) {
                showToast('error', (res && res.message) ? res.message : 'Failed to deactivate.');
                return;
            }
            showToast('success', 'Deactivated.');
            loadEntityDocuments(current.entityType, current.entityId);
        }).fail(function (xhr) {
            if (typeof redirectIfSessionExpired === 'function' && redirectIfSessionExpired(xhr)) return;
            showToast('error', 'Failed to deactivate.');
        });
    }

    function showVersions(documentId) {
        return $.get(apiUrl('api/documents/' + documentId + '/versions'))
            .done(function (res) {
                if (res && res.csrf_hash) refreshCsrf(res);
                const items = (res && res.items) ? res.items : [];
                if (!items.length) {
                    showToast('error', 'No versions found.');
                    return;
                }
                const rows = items.map(v => {
                    const download = apiUrl('api/documents/' + v.id + '/download');
                    const label = `v${escapeHtml(v.version)} - ${escapeHtml(v.file_name || '')}`;
                    return `<div style="display:flex;justify-content:space-between;gap:10px;padding:8px 0;border-bottom:1px solid var(--border)">
                        <div style="font-size:13px;color:var(--ink);font-weight:600">${label}</div>
                        <a href="${download}" target="_blank" rel="noopener" style="font-size:12px;text-decoration:none;color:var(--c-blue)"><i class="fas fa-download"></i> Download</a>
                    </div>`;
                }).join('');
                Swal.fire({
                    title: 'Versions',
                    html: `<div style="text-align:left;max-height:360px;overflow:auto">${rows}</div>`,
                    width: 720,
                    confirmButtonText: 'Close'
                });
            })
            .fail(function (xhr) {
                if (typeof redirectIfSessionExpired === 'function' && redirectIfSessionExpired(xhr)) return;
                showToast('error', 'Failed to load versions.');
            });
    }

    $load.on('click', function () {
        const selected = ensureEntitySelected();
        if (!selected) return;
        loadEntityDocuments(selected.entityType, selected.entityId);
    });

    $refresh.on('click', function () {
        const selected = ensureEntitySelected();
        if (!selected) return;
        loadEntityDocuments(selected.entityType, selected.entityId);
    });

    $clear.on('click', function () {
        current = { entityType: '', entityId: 0 };
        $entityType.val('');
        $entityId.val('');
        $docType.val('');
        $file.val('');
        setEmpty('Load an entity to view documents.');
    });

    $uploadForm.on('submit', function (e) {
        e.preventDefault();
        uploadDocument();
    });

    $(document).on('click', '.dmDetachBtn', function () {
        const id = parseInt($(this).data('id'), 10);
        if (!id) return;
        Swal.fire({
            title: 'Deactivate this document?',
            text: 'This will hide it from the latest list. The file stays on disk.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--c-rose)',
            cancelButtonColor: 'var(--ink-soft)',
            confirmButtonText: 'Yes, deactivate'
        }).then((result) => {
            if (result.isConfirmed) detachDocument(id);
        });
    });

    $(document).on('click', '.dmVersionsBtn', function () {
        const id = parseInt($(this).data('id'), 10);
        if (!id) return;
        showVersions(id);
    });

    setEmpty('Load an entity to view documents.');
})();

