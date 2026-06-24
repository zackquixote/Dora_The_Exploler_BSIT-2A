<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/advanced-features.css') ?>">

<style>
.recipient-preview {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 12px 16px;
    margin-top: 12px;
    display: none;
}
.recipient-preview.show {
    display: block;
}
.char-counter {
    font-size: 12px;
    color: #666;
    margin-top: 4px;
}
.char-counter.warning {
    color: #ff9800;
}
.char-counter.danger {
    color: #f44336;
}
.template-item, .draft-item {
    padding: 8px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.template-item:hover, .draft-item:hover {
    background: #f5f5f5;
    border-color: var(--c-blue);
}
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
.loading-overlay.show {
    display: flex;
}
.loading-spinner {
    background: white;
    padding: 30px;
    border-radius: 12px;
    text-align: center;
}
</style>

<div class="bmis-content af-container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div style="display:flex;align-items:center">
            <div style="width:56px;height:56px;border-radius:16px;background:rgba(79,70,229,0.12);color:var(--c-blue);display:flex;align-items:center;justify-content:center;font-size:24px;margin-right:18px">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div>
                <h1 class="ds-page-title" style="margin:0;font-size:28px;font-weight:800;color:var(--ink)">Send Broadcast</h1>
                <p style="font-size:14px;color:var(--ink-muted);margin-top:2px">Send SMS or Email broadcasts directly to your residents</p>
            </div>
        </div>
        <div>
            <button type="button" class="af-btn-secondary" onclick="showTemplates()">
                <i class="fas fa-file-alt"></i> Templates
            </button>
            <button type="button" class="af-btn-secondary" onclick="showDrafts()">
                <i class="fas fa-save"></i> Drafts
            </button>
        </div>
    </div>
    
    <div class="af-card" style="max-width: 1000px">
        <div class="af-card-header">
            <div class="ds-card-title"><i class="fas fa-bullhorn"></i> New Message</div>
        </div>
        <div class="af-card-body">
            <form id="notificationForm" action="<?= base_url('advanced/send-notification') ?>" method="POST">
                <?= csrf_field() ?>
                
                <!-- Recipient Selection -->
                <div class="af-form-group">
                    <label class="af-label">Recipients <span style="color:red">*</span></label>
                    <select name="recipient_type" id="recipientType" class="af-input" onchange="updateRecipientOptions()">
                        <option value="all">All Active Residents</option>
                        <option value="sitio">By Sitio/Purok</option>
                        <option value="age_group">By Age Group</option>
                        <option value="gender">By Gender</option>
                        <option value="voters">Registered Voters Only</option>
                        <option value="seniors">Senior Citizens Only</option>
                        <option value="pwd">PWD Only</option>
                        <option value="household_heads">Household Heads Only</option>
                        <option value="specific">Specific Residents</option>
                    </select>
                    <i class="fas fa-users af-input-icon"></i>
                </div>

                <!-- Dynamic Recipient Filters -->
                <div id="recipientFilters"></div>

                <!-- Recipient Preview -->
                <div class="recipient-preview" id="recipientPreview">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <span><i class="fas fa-info-circle"></i> <strong id="recipientCount">0</strong> recipients will receive this message</span>
                        <span id="estimatedCost" style="font-size:12px;color:#666"></span>
                    </div>
                </div>

                <div class="ds-grid-2">
                    <div class="af-form-group">
                        <label class="af-label">Notification Type</label>
                        <select name="type" class="af-input">
                            <option value="announcement">Announcement</option>
                            <option value="emergency">Emergency Alert</option>
                            <option value="event">Event Notification</option>
                            <option value="reminder">Reminder</option>
                            <option value="information">Information</option>
                        </select>
                        <i class="fas fa-tag af-input-icon"></i>
                    </div>
                    
                    <div class="af-form-group">
                        <label class="af-label">Schedule (Optional)</label>
                        <input type="datetime-local" name="scheduled_at" class="af-input has-icon" id="scheduledAt">
                        <i class="fas fa-clock af-input-icon"></i>
                    </div>
                </div>

                <div class="af-form-group">
                    <label class="af-label">Message Title <span style="color:red">*</span></label>
                    <input type="text" name="title" id="messageTitle" class="af-input has-icon" required placeholder="e.g. Typhoon Warning">
                    <i class="fas fa-heading af-input-icon"></i>
                    <small style="color:#666;font-size:12px">
                        Available variables: {first_name}, {last_name}, {full_name}, {household_no}, {sitio}
                    </small>
                </div>
 
                <div class="af-form-group">
                    <label class="af-label">Message Content <span style="color:red">*</span></label>
                    <textarea name="message" id="messageContent" class="af-input" rows="5" required placeholder="Type your broadcast message here..." oninput="updateCharCounter()"></textarea>
                    <div class="char-counter" id="charCounter">0 / 160 characters</div>
                    <small style="color:#666;font-size:12px">
                        SMS messages over 160 characters will be split into multiple messages
                    </small>
                </div>

                <div class="af-form-group">
                    <label class="af-label">Delivery Channels <span style="color:red">*</span></label>
                    <div class="af-checkbox-group">
                        <label class="af-checkbox-label">
                            <input type="checkbox" name="channels[]" value="sms" checked onchange="updateEstimatedCost()"> 
                            <i class="fas fa-sms" style="color:var(--c-blue)"></i> SMS
                        </label>
                        <label class="af-checkbox-label">
                            <input type="checkbox" name="channels[]" value="email" onchange="updateEstimatedCost()"> 
                            <i class="fas fa-envelope" style="color:var(--c-amber)"></i> Email
                        </label>
                    </div>
                </div>

                <div class="af-form-group" style="margin-top: 32px">
                    <button type="button" class="af-btn-secondary" onclick="saveDraft()">
                        <i class="fas fa-save"></i> Save as Draft
                    </button>
                    <button type="button" class="af-btn-secondary" onclick="previewMessage()">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button type="submit" class="af-btn-primary" id="sendBtn">
                        <i class="fas fa-paper-plane"></i> <span id="sendBtnText">Send Broadcast</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner">
        <i class="fas fa-spinner fa-spin" style="font-size:48px;color:var(--c-blue)"></i>
        <p style="margin-top:16px;font-weight:600" id="loadingText">Sending notifications...</p>
        <p style="font-size:14px;color:#666" id="loadingProgress"></p>
    </div>
</div>

<!-- Templates Modal -->
<div class="modal fade" id="templatesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-alt"></i> Message Templates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="templatesList">
                    <?php if (!empty($templates)): ?>
                        <?php foreach ($templates as $template): ?>
                            <div class="template-item" onclick="loadTemplate(<?= $template['id'] ?>)">
                                <strong><?= esc($template['name']) ?></strong>
                                <span style="float:right;font-size:12px;color:#666"><?= esc($template['category']) ?></span>
                                <p style="font-size:13px;color:#666;margin:4px 0 0 0"><?= esc(substr($template['message'], 0, 100)) ?>...</p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align:center;color:#666">No templates available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Drafts Modal -->
<div class="modal fade" id="draftsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-save"></i> Saved Drafts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="draftsList">
                    <?php if (!empty($drafts)): ?>
                        <?php foreach ($drafts as $draft): ?>
                            <div class="draft-item" style="position:relative">
                                <div onclick="loadDraft(<?= $draft['id'] ?>)">
                                    <strong><?= esc($draft['title'] ?: 'Untitled Draft') ?></strong>
                                    <span style="float:right;font-size:12px;color:#666"><?= date('M j, Y g:i A', strtotime($draft['updated_at'])) ?></span>
                                    <p style="font-size:13px;color:#666;margin:4px 0 0 0"><?= esc(substr($draft['message'], 0, 100)) ?>...</p>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger" style="position:absolute;top:8px;right:8px" onclick="deleteDraft(<?= $draft['id'] ?>); event.stopPropagation()">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align:center;color:#666">No drafts saved</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let recipientCriteria = {type: 'all'};
const SMS_COST_PER_MESSAGE = 1.00; // PHP per SMS
const sitios = <?= json_encode($sitios ?? []) ?>;

// Update recipient options based on selection
function updateRecipientOptions() {
    const type = document.getElementById('recipientType').value;
    const filtersDiv = document.getElementById('recipientFilters');
    
    let html = '';
    
    switch(type) {
        case 'sitio':
            html = `
                <div class="af-form-group">
                    <label class="af-label">Select Sitio/Purok</label>
                    <select id="sitioFilter" class="af-input" onchange="updateRecipientCount()">
                        ${sitios.map(s => `<option value="${s}">${s}</option>`).join('')}
                    </select>
                </div>
            `;
            break;
        case 'age_group':
            html = `
                <div class="ds-grid-2">
                    <div class="af-form-group">
                        <label class="af-label">Min Age</label>
                        <input type="number" id="minAge" class="af-input" min="0" max="120" placeholder="0" onchange="updateRecipientCount()">
                    </div>
                    <div class="af-form-group">
                        <label class="af-label">Max Age</label>
                        <input type="number" id="maxAge" class="af-input" min="0" max="120" placeholder="120" onchange="updateRecipientCount()">
                    </div>
                </div>
            `;
            break;
        case 'gender':
            html = `
                <div class="af-form-group">
                    <label class="af-label">Select Gender</label>
                    <select id="genderFilter" class="af-input" onchange="updateRecipientCount()">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
            `;
            break;
        case 'specific':
            html = `
                <div class="af-form-group">
                    <label class="af-label">Search and Select Residents</label>
                    <input type="text" id="residentSearch" class="af-input" placeholder="Type to search residents..." oninput="searchResidents()">
                    <div id="residentSearchResults" style="max-height:200px;overflow-y:auto;border:1px solid #e0e0e0;border-radius:6px;margin-top:8px;display:none"></div>
                    <div id="selectedResidents" style="margin-top:12px"></div>
                </div>
            `;
            break;
    }
    
    filtersDiv.innerHTML = html;
    updateRecipientCount();
}

// Update recipient count
function updateRecipientCount() {
    const type = document.getElementById('recipientType').value;
    
    recipientCriteria = {type: type};
    
    switch(type) {
        case 'sitio':
            recipientCriteria.sitio = document.getElementById('sitioFilter')?.value;
            break;
        case 'age_group':
            recipientCriteria.min_age = document.getElementById('minAge')?.value;
            recipientCriteria.max_age = document.getElementById('maxAge')?.value;
            break;
        case 'gender':
            recipientCriteria.gender = document.getElementById('genderFilter')?.value;
            break;
        case 'specific':
            recipientCriteria.resident_ids = selectedResidentIds;
            break;
    }
    
    const formData = new FormData();
    formData.append('criteria', JSON.stringify(recipientCriteria));
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    
    fetch('<?= base_url('advanced/get-recipient-count') ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('recipientCount').textContent = data.count;
            document.getElementById('recipientPreview').classList.add('show');
            updateEstimatedCost();
        }
    })
    .catch(err => console.error('Error:', err));
}

// Update character counter
function updateCharCounter() {
    const message = document.getElementById('messageContent').value;
    const length = message.length;
    const counter = document.getElementById('charCounter');
    
    counter.textContent = `${length} / 160 characters`;
    
    if (length > 160) {
        const parts = Math.ceil(length / 153);
        counter.textContent += ` (${parts} SMS messages)`;
        counter.classList.add('warning');
    } else {
        counter.classList.remove('warning', 'danger');
    }
    
    if (length > 306) {
        counter.classList.add('danger');
    }
}

// Update estimated cost
function updateEstimatedCost() {
    const count = parseInt(document.getElementById('recipientCount').textContent) || 0;
    const message = document.getElementById('messageContent').value;
    const smsChecked = document.querySelector('input[name="channels[]"][value="sms"]').checked;
    
    if (!smsChecked || count === 0) {
        document.getElementById('estimatedCost').textContent = '';
        return;
    }
    
    const parts = message.length > 160 ? Math.ceil(message.length / 153) : 1;
    const cost = count * parts * SMS_COST_PER_MESSAGE;
    
    document.getElementById('estimatedCost').textContent = `Estimated cost: ₱${cost.toFixed(2)}`;
}


// Search residents
let selectedResidentIds = [];
let searchTimeout;

function searchResidents() {
    clearTimeout(searchTimeout);
    const query = document.getElementById('residentSearch').value;
    
    if (query.length < 2) {
        document.getElementById('residentSearchResults').style.display = 'none';
        return;
    }
    
    searchTimeout = setTimeout(() => {
        fetch('<?= base_url('advanced/search-residents') ?>?q=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                displaySearchResults(data.residents);
            }
        });
    }, 300);
}

function displaySearchResults(residents) {
    const resultsDiv = document.getElementById('residentSearchResults');
    
    if (residents.length === 0) {
        resultsDiv.innerHTML = '<p style="padding:12px;text-align:center;color:#666">No residents found</p>';
        resultsDiv.style.display = 'block';
        return;
    }
    
    let html = '';
    residents.forEach(r => {
        if (!selectedResidentIds.includes(r.id)) {
            html += `
                <div style="padding:8px 12px;border-bottom:1px solid #f0f0f0;cursor:pointer" onclick="selectResident(${r.id}, '${r.first_name} ${r.last_name}', '${r.sitio || ''}')">
                    <strong>${r.first_name} ${r.last_name}</strong>
                    <span style="font-size:12px;color:#666;margin-left:8px">${r.sitio || ''}</span>
                </div>
            `;
        }
    });
    
    resultsDiv.innerHTML = html;
    resultsDiv.style.display = 'block';
}

function selectResident(id, name, sitio) {
    selectedResidentIds.push(id);
    
    const selectedDiv = document.getElementById('selectedResidents');
    const badge = document.createElement('span');
    badge.className = 'badge bg-primary';
    badge.style.cssText = 'margin:4px;padding:8px 12px;display:inline-block';
    badge.innerHTML = `${name} <i class="fas fa-times" style="margin-left:8px;cursor:pointer" onclick="removeResident(${id}, this)"></i>`;
    
    selectedDiv.appendChild(badge);
    document.getElementById('residentSearch').value = '';
    document.getElementById('residentSearchResults').style.display = 'none';
    
    updateRecipientCount();
}

function removeResident(id, element) {
    selectedResidentIds = selectedResidentIds.filter(rid => rid !== id);
    element.parentElement.remove();
    updateRecipientCount();
}

// Save draft
function saveDraft() {
    const formData = new FormData(document.getElementById('notificationForm'));
    formData.append('save_as_draft', '1');
    
    fetch('<?= base_url('advanced/send-notification') ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('Draft saved successfully', 'success');
        } else {
            showToast(data.message || 'Failed to save draft', 'error');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('An error occurred', 'error');
    });
}

// Preview message
function previewMessage() {
    const title = document.getElementById('messageTitle').value;
    const message = document.getElementById('messageContent').value;
    
    if (!title || !message) {
        showToast('Please fill in title and message', 'warning');
        return;
    }
    
    const preview = `
        <strong>${title}</strong><br><br>
        ${message.replace(/\n/g, '<br>')}
    `;
    
    if (confirm('Preview:\n\n' + title + '\n\n' + message + '\n\nDoes this look correct?')) {
        // User confirmed
    }
}

// Load template
function loadTemplate(templateId) {
    fetch('<?= base_url('advanced/load-template') ?>/' + templateId)
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('messageTitle').value = data.template.title;
            document.getElementById('messageContent').value = data.template.message;
            updateCharCounter();
            const modal = bootstrap.Modal.getInstance(document.getElementById('templatesModal'));
            if (modal) modal.hide();
            showToast('Template loaded', 'success');
        }
    });
}

// Load draft
function loadDraft(draftId) {
    fetch('<?= base_url('advanced/load-draft') ?>/' + draftId)
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('messageTitle').value = data.draft.title || '';
            document.getElementById('messageContent').value = data.draft.message || '';
            updateCharCounter();
            const modal = bootstrap.Modal.getInstance(document.getElementById('draftsModal'));
            if (modal) modal.hide();
            showToast('Draft loaded', 'success');
        }
    });
}

// Delete draft
function deleteDraft(draftId) {
    if (!confirm('Delete this draft?')) return;
    
    fetch('<?= base_url('advanced/delete-draft') ?>/' + draftId, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('Draft deleted', 'success');
            location.reload();
        }
    });
}

// Show modals
function showTemplates() {
    new bootstrap.Modal(document.getElementById('templatesModal')).show();
}

function showDrafts() {
    new bootstrap.Modal(document.getElementById('draftsModal')).show();
}


// Form submission
document.getElementById('notificationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const count = parseInt(document.getElementById('recipientCount').textContent) || 0;
    
    if (count === 0) {
        showToast('No recipients selected', 'warning');
        return;
    }
    
    const scheduledAt = document.getElementById('scheduledAt').value;
    const confirmMsg = scheduledAt 
        ? `Schedule this message for ${count} recipients on ${new Date(scheduledAt).toLocaleString()}?`
        : `Send this message to ${count} recipients now?`;
    
    if (!confirm(confirmMsg)) {
        return;
    }
    
    // Show loading overlay
    document.getElementById('loadingOverlay').classList.add('show');
    document.getElementById('loadingText').textContent = scheduledAt ? 'Scheduling notifications...' : 'Sending notifications...';
    document.getElementById('sendBtn').disabled = true;
    
    const formData = new FormData(this);
    formData.append('recipients', JSON.stringify(recipientCriteria));
    
    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('loadingOverlay').classList.remove('show');
        document.getElementById('sendBtn').disabled = false;
        
        if (data.success) {
            showResultsModal(data);
        } else {
            showToast(data.message || 'Failed to send notifications', 'error');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        document.getElementById('loadingOverlay').classList.remove('show');
        document.getElementById('sendBtn').disabled = false;
        showToast('An error occurred', 'error');
    });
});

// Show results modal
function showResultsModal(data) {
    const stats = data.stats || {};
    const failed = data.failed_recipients || [];
    
    let html = `
        <div class="modal fade" id="resultsModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="fas fa-check-circle"></i> ${data.message}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div style="text-align:center;padding:20px">
                            <div style="font-size:48px;color:#4caf50;margin-bottom:16px">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h4>Delivery Summary</h4>
                            <div style="margin-top:24px">
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;text-align:left">
                                    <div style="padding:12px;background:#f5f5f5;border-radius:8px">
                                        <div style="font-size:24px;font-weight:bold;color:#4caf50">${stats.success || 0}</div>
                                        <div style="font-size:12px;color:#666">Successful</div>
                                    </div>
                                    <div style="padding:12px;background:#f5f5f5;border-radius:8px">
                                        <div style="font-size:24px;font-weight:bold;color:#f44336">${stats.failed || 0}</div>
                                        <div style="font-size:12px;color:#666">Failed</div>
                                    </div>
                                    <div style="padding:12px;background:#f5f5f5;border-radius:8px">
                                        <div style="font-size:24px;font-weight:bold;color:#2196f3">${stats.sms_sent || 0}</div>
                                        <div style="font-size:12px;color:#666">SMS Sent</div>
                                    </div>
                                    <div style="padding:12px;background:#f5f5f5;border-radius:8px">
                                        <div style="font-size:24px;font-weight:bold;color:#ff9800">${stats.email_sent || 0}</div>
                                        <div style="font-size:12px;color:#666">Emails Sent</div>
                                    </div>
                                </div>
                                ${stats.estimated_cost ? `<div style="margin-top:16px;padding:12px;background:#fff3cd;border-radius:8px">
                                    <strong>Estimated Cost:</strong> ₱${stats.estimated_cost.toFixed(2)}
                                </div>` : ''}
                            </div>
                        </div>
                        ${failed.length > 0 ? `
                            <div style="margin-top:24px">
                                <h6 style="color:#f44336"><i class="fas fa-exclamation-triangle"></i> Failed Recipients</h6>
                                <div style="max-height:200px;overflow-y:auto;border:1px solid #e0e0e0;border-radius:6px;padding:8px">
                                    ${failed.map(f => `<div style="padding:4px">${f.name}</div>`).join('')}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="location.reload()">Send Another</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', html);
    new bootstrap.Modal(document.getElementById('resultsModal')).show();
}

// Toast notification
function showToast(message, type = 'info') {
    const colors = {
        success: '#4caf50',
        error: '#f44336',
        warning: '#ff9800',
        info: '#2196f3'
    };
    
    const toast = document.createElement('div');
    toast.style.cssText = `
        position:fixed;top:20px;right:20px;background:${colors[type]};color:white;
        padding:16px 24px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);
        z-index:10000;animation:slideIn 0.3s ease;
    `;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateRecipientCount();
});
</script>

<style>
@keyframes slideIn {
    from { transform: translateX(400px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
@keyframes slideOut {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(400px); opacity: 0; }
}
</style>

<?= $this->endSection() ?>
