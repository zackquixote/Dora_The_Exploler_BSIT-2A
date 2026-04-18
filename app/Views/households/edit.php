<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="content-wrapper bg-light min-vh-100 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= base_url('households') ?>" class="text-muted small text-decoration-none">
                <i class="fas fa-arrow-left"></i> Back to Household List
            </a>
            <h2 class="font-weight-bold mt-1">Edit Household</h2>
            <p class="text-muted">
                Modifying household: <strong>#<?= esc($household['household_no']) ?></strong>
            </p>
        </div>
        <div>
            <button type="button" class="btn btn-outline-secondary mr-2 px-4 shadow-sm" onclick="window.history.back()">
                <i class="fas fa-times mr-1"></i> Cancel
            </button>
            <button type="submit" form="householdForm" class="btn btn-primary px-4 shadow-sm bg-navy border-0">
                <i class="fas fa-save mr-1"></i> Update Household
            </button>
        </div>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <h6><i class="fas fa-exclamation-triangle mr-2"></i> Please fix the following errors:</h6>
            <ul class="mb-0 small">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <form id="householdForm" action="<?= base_url('households/update/' . $household['id']) ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="_method" value="PUT">

        <!-- BASIC INFORMATION -->
        <div class="card shadow-sm border-0 rounded-lg mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-0 font-weight-bold"><i class="fas fa-home mr-2 text-primary"></i> Household Information</h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Household Number <span class="text-danger">*</span></label>
                        <input type="text" name="household_no" class="form-control form-control-lg bg-light border-0" 
                               value="<?= esc($household['household_no']) ?>" required>
                        <small class="text-muted">Unique household identifier</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Purok/Sitio <span class="text-danger">*</span></label>
                        <select name="sitio" class="form-control form-control-lg bg-light border-0" required>
                            <option value="Purok Malipayon" <?= ($household['sitio'] ?? '') == 'Purok Malipayon' ? 'selected' : '' ?>>Purok Malipayon</option>
                            <option value="Purok Masagana" <?= ($household['sitio'] ?? '') == 'Purok Masagana' ? 'selected' : '' ?>>Purok Masagana</option>
                            <option value="Purok Cory" <?= ($household['sitio'] ?? '') == 'Purok Cory' ? 'selected' : '' ?>>Purok Cory</option>
                            <option value="Purok Kawayan" <?= ($household['sitio'] ?? '') == 'Purok Kawayan' ? 'selected' : '' ?>>Purok Kawayan</option>
                            <option value="Purok Pagla-um" <?= ($household['sitio'] ?? '') == 'Purok Pagla-um' ? 'selected' : '' ?>>Purok Pagla-um</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">House Type</label>
                        <select name="house_type" class="form-control form-control-lg bg-light border-0">
                            <option value="">Select House Type</option>
                            <option value="Concrete" <?= ($household['house_type'] ?? '') == 'Concrete' ? 'selected' : '' ?>>Concrete</option>
                            <option value="Semi-Concrete" <?= ($household['house_type'] ?? '') == 'Semi-Concrete' ? 'selected' : '' ?>>Semi-Concrete</option>
                            <option value="Wood" <?= ($household['house_type'] ?? '') == 'Wood' ? 'selected' : '' ?>>Wood</option>
                            <option value="Light Materials" <?= ($household['house_type'] ?? '') == 'Light Materials' ? 'selected' : '' ?>>Light Materials</option>
                            <option value="Mixed" <?= ($household['house_type'] ?? '') == 'Mixed' ? 'selected' : '' ?>>Mixed</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADDRESS INFORMATION -->
        <div class="card shadow-sm border-0 rounded-lg mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-0 font-weight-bold"><i class="fas fa-location-dot mr-2 text-success"></i> Address Information</h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="small font-weight-bold text-secondary">Street Address</label>
                        <input type="text" name="street_address" class="form-control form-control-lg bg-light border-0" 
                               value="<?= esc($household['street_address'] ?? '') ?>" placeholder="e.g., Block 1, Lot 2">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small font-weight-bold text-secondary">Complete Address</label>
                        <input type="text" name="address" class="form-control form-control-lg bg-light border-0" 
                               value="<?= esc($household['address'] ?? '') ?>" placeholder="e.g., Sitio Kawayan, Barangay Salong">
                    </div>
                </div>
            </div>
        </div>

        <!-- HEAD OF HOUSEHOLD -->
        <div class="card shadow-sm border-0 rounded-lg mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-0 font-weight-bold"><i class="fas fa-user-crown mr-2 text-warning"></i> Head of Household</h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="small font-weight-bold text-secondary">Select Head Resident</label>
                        <select name="head_resident_id" class="form-control form-control-lg bg-light border-0">
                            <option value="">-- Select Resident as Head --</option>
                            <?php foreach ($residents as $r): ?>
                                <option value="<?= $r['id'] ?>" <?= ($household['head_resident_id'] ?? '') == $r['id'] ? 'selected' : '' ?>>
                                    <?= esc($r['last_name']) ?>, <?= esc($r['first_name']) ?> 
                                    <?= !empty($r['middle_name']) ? esc($r['middle_name']) : '' ?>
                                    (<?= ucfirst($r['sex']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">
                            <?php if ($residentCount > 0): ?>
                                This household has <strong><?= $residentCount ?></strong> member(s).
                            <?php else: ?>
                                No residents assigned to this household yet.
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- DANGER ZONE -->
        <?php if ($residentCount == 0): ?>
        <div class="card border-danger shadow-sm mb-4">
            <div class="card-header bg-danger text-white border-0">
                <h5 class="mb-0 font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i> Danger Zone</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-danger mb-0">
                    <i class="fas fa-trash mr-2"></i>
                    <strong>Delete this household?</strong> This action cannot be undone.
                    <button type="button" class="btn btn-danger btn-sm ml-3 delete-household-btn" data-id="<?= $household['id'] ?>">
                        <i class="fas fa-trash"></i> Delete Household
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- FORM FOOTER -->
        <div class="card border-0 shadow-sm mt-5 mb-5">
            <div class="card-body d-flex justify-content-between align-items-center py-3 px-4 bg-white rounded">
                <small class="text-muted">
                    <i class="fas fa-info-circle mr-1"></i> All fields marked with <span class="text-danger">*</span> are required.
                </small>
                <div>
                    <button type="button" class="btn btn-outline-secondary mr-2 px-4" onclick="window.history.back()">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4 bg-navy border-0">
                        <i class="fas fa-save mr-1"></i> Update Household
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.bg-navy { background-color: #03213b !important; }
.form-control:focus {
    box-shadow: none;
    border-color: #80bdff;
}
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
</style>

<script>
$(document).ready(function() {
    // Form validation
    $('#householdForm').on('submit', function(e) {
        var householdNo = $('input[name="household_no"]').val().trim();
        var sitio = $('select[name="sitio"]').val();
        
        if (!householdNo) {
            alert('Please enter household number');
            e.preventDefault();
            return false;
        }
        
        if (!sitio) {
            alert('Please select Purok/Sitio');
            e.preventDefault();
            return false;
        }
        
        var submitBtn = $('button[type="submit"]').last();
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
    });
    
    // Delete household from edit page
    $('.delete-household-btn').on('click', function() {
        var householdId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this household? This action cannot be undone.')) {
            $.ajax({
                url: '<?= base_url('households/delete') ?>/' + householdId,
                type: 'POST',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        window.location.href = '<?= base_url('households') ?>';
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error deleting household');
                }
            });
        }
    });
});
</script>

<?= $this->endSection() ?>