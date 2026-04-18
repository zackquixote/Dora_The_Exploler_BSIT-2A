<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="content-wrapper bg-light min-vh-100 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= base_url('households') ?>" class="text-muted small text-decoration-none">
                <i class="fas fa-arrow-left"></i> Back to Household List
            </a>
            <h2 class="font-weight-bold mt-1">Add New Household</h2>
            <p class="text-muted">Register a new household in the barangay database.</p>
        </div>
        <div>
            <button type="button" class="btn btn-outline-secondary mr-2 px-4 shadow-sm" onclick="window.history.back()">
                <i class="fas fa-times mr-1"></i> Cancel
            </button>
            <button type="submit" form="householdForm" class="btn btn-primary px-4 shadow-sm bg-navy border-0">
                <i class="fas fa-save mr-1"></i> Save Household
            </button>
        </div>
    </div>

    <form id="householdForm" action="<?= base_url('households/store') ?>" method="POST">
        <?= csrf_field() ?>

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
                               placeholder="e.g., 001, 002, H-001" required>
                        <small class="text-muted">Unique household identifier</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Purok/Sitio <span class="text-danger">*</span></label>
                        <select name="sitio" id="sitioSelect" class="form-control form-control-lg bg-light border-0" required>
                            <option disabled selected>Select Purok/Sitio</option>
                            <option value="Purok Malipayon">Purok Malipayon</option>
                            <option value="Purok Masagana">Purok Masagana</option>
                            <option value="Purok Cory">Purok Cory</option>
                            <option value="Purok Kawayan">Purok Kawayan</option>
                            <option value="Purok Pagla-um">Purok Pagla-um</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">House Type</label>
                        <select name="house_type" class="form-control form-control-lg bg-light border-0">
                            <option disabled selected>Select House Type</option>
                            <option value="Concrete">Concrete</option>
                            <option value="Semi-Concrete">Semi-Concrete</option>
                            <option value="Wood">Wood</option>
                            <option value="Light Materials">Light Materials</option>
                            <option value="Mixed">Mixed</option>
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
                               placeholder="e.g., Block 1, Lot 2">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small font-weight-bold text-secondary">Complete Address</label>
                        <input type="text" name="address" class="form-control form-control-lg bg-light border-0" 
                               placeholder="e.g., Sitio Kawayan, Barangay Salong">
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
                                <option value="<?= $r['id'] ?>">
                                    <?= esc($r['last_name']) ?>, <?= esc($r['first_name']) ?> 
                                    <?= !empty($r['middle_name']) ? esc($r['middle_name']) : '' ?>
                                    (<?= ucfirst($r['sex']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Optional - Can be set later from resident management</small>
                    </div>
                </div>
                
                <?php if (empty($residents)): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        No residents found. Please <a href="<?= base_url('resident/create') ?>" class="alert-link">add a resident</a> first before setting the head of household.
                    </div>
                <?php endif; ?>
            </div>
        </div>

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
                        <i class="fas fa-save mr-1"></i> Save Household
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
        var sitio = $('#sitioSelect').val();
        
        if (!householdNo) {
            alert('Please enter household number');
            e.preventDefault();
            return false;
        }
        
        if (!sitio || sitio === 'Select Purok/Sitio') {
            alert('Please select Purok/Sitio');
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        var submitBtn = $('button[type="submit"]').last();
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
    });
});
</script>

<?= $this->endSection() ?>