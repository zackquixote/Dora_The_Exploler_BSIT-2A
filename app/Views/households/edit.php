<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap');

:root {
    --navy:    #03213b;
    --accent:  #2563eb;
    --al:      #eff6ff;
    --success: #16a34a;
    --danger:  #dc2626;
    --muted:   #6b7280;
    --border:  #e5e7eb;
    --bg:      #f9fafb;
    --card:    #ffffff;
    --text:    #111827;
    --tsm:     #374151;
    --radius:  12px;
    --shadow:  0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.05);
}
body, .content-wrapper { background: var(--bg) !important; font-family: 'DM Sans', sans-serif; }

.cr-header { padding:28px 32px 0; display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; }
.cr-breadcrumb { font-size:12px; color:var(--muted); margin-bottom:6px; }
.cr-breadcrumb a { color:var(--muted); text-decoration:none; }
.cr-breadcrumb a:hover { color:var(--accent); }
.cr-header h1 { font-size:26px; font-weight:700; color:var(--text); margin:0 0 4px; letter-spacing:-.4px; }
.cr-header p  { font-size:14px; color:var(--muted); margin:0; }
.cr-header-btns { display:flex; gap:8px; align-items:center; padding-top:16px; }
.btn-cancel { display:inline-flex; align-items:center; gap:6px; padding:10px 18px; border:1px solid var(--border); border-radius:8px; background:var(--card); font-size:14px; font-family:'DM Sans',sans-serif; color:var(--tsm); cursor:pointer; text-decoration:none; font-weight:500; transition:all .15s; }
.btn-cancel:hover { border-color:var(--muted); text-decoration:none; color:var(--text); }
.btn-save { display:inline-flex; align-items:center; gap:6px; padding:10px 20px; border:none; border-radius:8px; background:var(--navy); color:#fff; font-size:14px; font-family:'DM Sans',sans-serif; font-weight:600; cursor:pointer; transition:background .15s; }
.btn-save:hover { background:#0a3259; }

.cr-body { padding:24px 32px 40px; display:flex; flex-direction:column; gap:20px; }

.form-card { background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
.form-card-header { padding:18px 24px 14px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
.form-card-icon { width:36px; height:36px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.fci-blue   { background:var(--al);   color:var(--accent); }
.fci-green  { background:#f0fdf4; color:#16a34a; }
.fci-yellow { background:#fefce8; color:#ca8a04; }
.form-card-header h5 { font-size:15px; font-weight:700; color:var(--text); margin:0; }
.form-card-header p  { font-size:13px; color:var(--muted); margin:0; }
.form-card-body { padding:24px; }

.field-row { display:grid; gap:16px; }
.field-row.cols-3 { grid-template-columns:repeat(3,1fr); }
.field-row.cols-2 { grid-template-columns:repeat(2,1fr); }
.field-row.cols-1 { grid-template-columns:1fr; }
.field-group { display:flex; flex-direction:column; gap:6px; }
.field-label { font-size:12px; font-weight:600; color:var(--tsm); text-transform:uppercase; letter-spacing:.5px; }
.field-label .req { color:var(--danger); margin-left:2px; }
.field-control { padding:11px 14px; border:1px solid var(--border); border-radius:8px; font-size:14px; font-family:'DM Sans',sans-serif; color:var(--text); background:var(--bg); width:100%; transition:all .15s; }
.field-control:focus { outline:none; border-color:var(--accent); background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
.field-control:disabled { opacity:.65; cursor:not-allowed; background:#f3f4f6; }
.field-hint { font-size:12px; color:var(--muted); }

.state-alert { display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-radius:8px; font-size:13px; margin-top:12px; }
.state-alert.info    { background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; }
.state-alert.warning { background:#fffbeb; color:#92400e; border:1px solid #fde68a; }
.state-alert.loading { background:#f9fafb; color:var(--muted); border:1px solid var(--border); }
.state-alert.success { background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; }
.state-alert i { margin-top:1px; flex-shrink:0; }
.state-alert a { color:inherit; font-weight:600; }

.cr-footer { background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow); padding:16px 24px; display:flex; align-items:center; justify-content:space-between; }
.cr-footer .footer-note { font-size:13px; color:var(--muted); display:flex; align-items:center; gap:6px; }

/* Edit-specific: change indicator */
.hh-id-badge { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; background:var(--al); border-radius:6px; font-size:12px; font-weight:600; color:var(--accent); margin-left:10px; vertical-align:middle; }

.flash-zone { padding:16px 32px 0; }
.flash-zone .alert { border-radius:var(--radius); border:none; font-size:14px; }
</style>

<div class="content-wrapper">

    <!-- PAGE HEADER -->
    <div class="cr-header">
        <div>
            <div class="cr-breadcrumb">
                <a href="<?= base_url('households') ?>">Households</a>
                <span class="mx-1">›</span>
                <a href="<?= base_url('households/view/'.$household['id']) ?>"><?= esc($household['household_no']) ?></a>
                <span class="mx-1">›</span>
                <span>Edit</span>
            </div>
            <h1>
                Edit Household
                <span class="hh-id-badge">
                    <i class="fas fa-hashtag" style="font-size:10px;"></i>
                    <?= esc($household['household_no']) ?>
                </span>
            </h1>
            <p>Update the information for this household record.</p>
        </div>
        <div class="cr-header-btns">
            <a href="<?= base_url('households/view/'.$household['id']) ?>" class="btn-cancel">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" form="householdForm" class="btn-save">
                <i class="fas fa-save"></i> Update Household
            </button>
        </div>
    </div>

    <!-- FLASH ERRORS -->
    <?php if (session()->getFlashdata('errors')): ?>
    <div class="flash-zone">
        <div class="alert alert-danger alert-dismissible fade show">
            <strong><i class="fas fa-exclamation-circle mr-2"></i>Please fix the following:</strong>
            <ul class="mb-0 mt-1">
                <?php foreach (session()->getFlashdata('errors') as $e): ?>
                    <li><?= esc($e) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    </div>
    <?php endif; ?>

    <form id="householdForm"
          action="<?= base_url('households/update/'.$household['id']) ?>"
          method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $household['id'] ?>">

        <div class="cr-body">

            <!-- HOUSEHOLD INFO -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-card-icon fci-blue"><i class="fas fa-home"></i></div>
                    <div>
                        <h5>Household Information</h5>
                        <p>Basic identification details for this household</p>
                    </div>
                </div>
                <div class="form-card-body">
                    <div class="field-row cols-3">
                        <div class="field-group">
                            <label class="field-label">Household Number<span class="req">*</span></label>
                            <input type="text" name="household_no" class="field-control"
                                   value="<?= esc($household['household_no']) ?>">
                            <span class="field-hint">Must be unique across all households</span>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Purok / Sitio<span class="req">*</span></label>
                            <select name="sitio" id="sitioSelect" class="field-control">
                                <option value="">— Select Purok —</option>
                                <option value="Purok Malipayon" <?= $household['sitio']=='Purok Malipayon'?'selected':'' ?>>Purok Malipayon</option>
                                <option value="Purok Masagana"  <?= $household['sitio']=='Purok Masagana' ?'selected':'' ?>>Purok Masagana</option>
                                <option value="Purok Cory"      <?= $household['sitio']=='Purok Cory'     ?'selected':'' ?>>Purok Cory</option>
                                <option value="Purok Kawayan"   <?= $household['sitio']=='Purok Kawayan'  ?'selected':'' ?>>Purok Kawayan</option>
                                <option value="Purok Pagla-um"  <?= $household['sitio']=='Purok Pagla-um' ?'selected':'' ?>>Purok Pagla-um</option>
                            </select>
                        </div>
                        <div class="field-group">
                            <label class="field-label">House Type</label>
                            <select name="house_type" class="field-control">
                                <option value="">— Select Type —</option>
                                <option value="Concrete"       <?= $household['house_type']=='Concrete'       ?'selected':'' ?>>Concrete</option>
                                <option value="Semi-Concrete"  <?= $household['house_type']=='Semi-Concrete'  ?'selected':'' ?>>Semi-Concrete</option>
                                <option value="Wood"           <?= $household['house_type']=='Wood'            ?'selected':'' ?>>Wood</option>
                                <option value="Light Materials" <?= $household['house_type']=='Light Materials'?'selected':'' ?>>Light Materials</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ADDRESS -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-card-icon fci-green"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <h5>Address Information</h5>
                        <p>Physical location of the household</p>
                    </div>
                </div>
                <div class="form-card-body">
                    <div class="field-row cols-2">
                        <div class="field-group">
                            <label class="field-label">Street Address</label>
                            <input type="text" name="street_address" class="field-control"
                                   value="<?= esc($household['street_address'] ?? '') ?>"
                                   placeholder="e.g., Block 1, Lot 2, House #12">
                        </div>
                        <div class="field-group">
                            <label class="field-label">Complete Address</label>
                            <input type="text" name="address" class="field-control"
                                   value="<?= esc($household['address'] ?? '') ?>"
                                   placeholder="e.g., Barangay Salong, Iloilo">
                        </div>
                    </div>
                </div>
            </div>

            <!-- HEAD OF HOUSEHOLD -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-card-icon fci-yellow"><i class="fas fa-user"></i></div>
                    <div>
                        <h5>Head of Household</h5>
                        <p>Currently showing residents from <strong><?= esc($household['sitio']) ?></strong></p>
                    </div>
                </div>
                <div class="form-card-body">
                    <div class="field-row cols-1">
                        <div class="field-group">
                            <label class="field-label">Select Head Resident</label>
                            <select name="head_resident_id" id="headResidentSelect"
                                    class="field-control" disabled>
                                <option value="">Loading residents…</option>
                            </select>
                            <span class="field-hint">Shows residents from the selected purok and all unassigned residents.</span>
                        </div>
                    </div>
                    <div id="loadingAlert" class="state-alert loading" style="display:none;">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Loading residents…</span>
                    </div>
                    <div id="noResidentsAlert" class="state-alert warning" style="display:none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>No residents found in this purok.
                            <a href="<?= base_url('resident/create') ?>">Add a resident →</a>
                        </span>
                    </div>
                    <?php if ($residentCount > 0): ?>
                    <div class="state-alert success mt-2">
                        <i class="fas fa-info-circle"></i>
                        <span>This household currently has <strong><?= $residentCount ?> registered resident(s)</strong>.</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="cr-footer">
                <span class="footer-note">
                    <i class="fas fa-info-circle" style="color:var(--accent);"></i>
                    Fields marked <span style="color:var(--danger);font-weight:700;margin:0 2px;">*</span> are required.
                </span>
                <div style="display:flex;gap:8px;">
                    <a href="<?= base_url('households/view/'.$household['id']) ?>" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Update Household
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
$(document).ready(function () {
    var currentSitio  = '<?= esc($household['sitio']) ?>';
    var currentHeadId = '<?= $household['head_resident_id'] ?? '' ?>';

    function loadResidents(sitio, selectedId) {
        var headSelect = $('#headResidentSelect');
        if (!sitio) {
            headSelect.html('<option value="">— Select Purok first —</option>').prop('disabled', true);
            $('#noResidentsAlert, #loadingAlert').hide();
            return;
        }
        $('#loadingAlert').show();
        $('#noResidentsAlert').hide();
        headSelect.html('<option value="">Loading…</option>').prop('disabled', true);

        $.ajax({
            url  : '<?= base_url('households/getResidentsBySitio') ?>',
            type : 'POST',
            data : { sitio: sitio, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
            dataType: 'json',
            success: function (res) {
                $('#loadingAlert').hide();
                console.log('[Edit] residents response:', res);
                if (res.status === 'success' && res.residents && res.residents.length > 0) {
                    var opts = '<option value="">— Select Head of Household —</option>';
                    $.each(res.residents, function (i, r) {
                        var name = r.last_name + ', ' + r.first_name;
                        if (r.middle_name) name += ' ' + r.middle_name;
                        name += ' (' + (r.sex ? r.sex.charAt(0).toUpperCase() + r.sex.slice(1) : 'N/A') + ')';
                        if (!r.resident_sitio) name += ' [Unassigned]';
                        var sel = (selectedId && String(r.id) === String(selectedId)) ? 'selected' : '';
                        opts += '<option value="' + r.id + '" ' + sel + '>' + name + '</option>';
                    });
                    headSelect.html(opts).prop('disabled', false);
                } else {
                    headSelect.html('<option value="">— No residents found —</option>').prop('disabled', true);
                    $('#noResidentsAlert').show();
                    console.warn('No residents:', res);
                }
            },
            error: function (xhr) {
                $('#loadingAlert').hide();
                console.error('AJAX error:', xhr.status, xhr.responseText);
                headSelect.html('<option value="">— Request failed —</option>').prop('disabled', true);
            }
        });
    }

    // Auto-load on page open
    if (currentSitio) loadResidents(currentSitio, currentHeadId);

    $('#sitioSelect').on('change', function () {
        loadResidents($(this).val(), null);
    });

    $('#householdForm').on('submit', function (e) {
        var hhNo  = $('input[name="household_no"]').val().trim();
        var sitio = $('#sitioSelect').val();
        if (!hhNo)  { alert('Please enter household number.'); e.preventDefault(); return false; }
        if (!sitio) { alert('Please select Purok/Sitio.');     e.preventDefault(); return false; }
        $('.btn-save').html('<i class="fas fa-spinner fa-spin mr-1"></i> Updating…').prop('disabled', true);
    });
});
</script>

<?= $this->endSection() ?>