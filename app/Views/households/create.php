<?php
// ---------------------------------------------------------
// SMART THEME LOADER
// ---------------------------------------------------------
$role = strtolower(session()->get('role') ?? 'staff');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>

<?= $this->extend($template) ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Add New Household</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('staff/dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('households') ?>">Households</a></li>
                        <li class="breadcrumb-item active">Add New</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <h5><i class="icon fas fa-ban"></i> Please fix the following errors:</h5>
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $e): ?>
                        <li><?= esc($e) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php endif; ?>

            <form id="householdForm" action="<?= base_url('households/store') ?>" method="POST">
                <?= csrf_field() ?>

                <!-- HOUSEHOLD INFO -->
                <div class="card border-0 shadow-sm rounded-lg mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h4 class="card-title text-primary font-weight-bold"><i class="fas fa-home mr-2"></i>Household Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Household Number <span class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm border-0 rounded">
                                        <input type="text" name="household_no" id="householdNo" class="form-control border-light bg-light"
                                               value="<?= old('household_no', $generatedHouseholdNo ?? '') ?>"
                                               placeholder="e.g., HH-2024-001">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" id="generateHouseholdNo" title="Generate new household number">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                            <button type="button" class="btn btn-success" id="checkHouseholdNo" title="Check availability">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted mt-2 d-block" id="householdNoFeedback">Auto-generated unique number (you can edit)</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Purok / Sitio <span class="text-danger">*</span></label>
                                    <select name="sitio" id="sitioSelect" class="form-control border-light bg-light shadow-sm custom-select" required>
                                        <option value="">— Select Purok —</option>
                                        <option value="Purok Malipayon" <?= old('sitio')=='Purok Malipayon'?'selected':'' ?>>Purok Malipayon</option>
                                        <option value="Purok Masagana"  <?= old('sitio')=='Purok Masagana' ?'selected':'' ?>>Purok Masagana</option>
                                        <option value="Purok Cory"      <?= old('sitio')=='Purok Cory'     ?'selected':'' ?>>Purok Cory</option>
                                        <option value="Purok Kawayan"   <?= old('sitio')=='Purok Kawayan'  ?'selected':'' ?>>Purok Kawayan</option>
                                        <option value="Purok Pagla-um"  <?= old('sitio')=='Purok Pagla-um' ?'selected':'' ?>>Purok Pagla-um</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">House Type</label>
                                    <select name="house_type" class="form-control border-light bg-light shadow-sm custom-select">
                                        <option value="">— Select Type —</option>
                                        <option value="Concrete"        <?= old('house_type')=='Concrete'       ?'selected':'' ?>>Concrete</option>
                                        <option value="Semi-Concrete"   <?= old('house_type')=='Semi-Concrete'  ?'selected':'' ?>>Semi-Concrete</option>
                                        <option value="Wood"            <?= old('house_type')=='Wood'            ?'selected':'' ?>>Wood</option>
                                        <option value="Light Materials" <?= old('house_type')=='Light Materials'?'selected':'' ?>>Light Materials</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ADDRESS -->
                <div class="card border-0 shadow-sm rounded-lg mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h4 class="card-title text-success font-weight-bold"><i class="fas fa-map-marker-alt mr-2"></i>Address Information</h4>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="address" id="completeAddress" value="<?= old('address') ?>">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Street Address</label>
                                    <input type="text" name="street_address" id="streetAddress" class="form-control border-light bg-light shadow-sm"
                                           value="<?= old('street_address') ?>"
                                           placeholder="e.g., Block 1, Lot 2, House #12">
                                    <small class="text-muted mt-2 d-block">Enter specific house details (Block/Lot #). The full address will be auto-generated.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HEAD OF HOUSEHOLD -->
                <div class="card border-0 shadow-sm rounded-lg mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h4 class="card-title text-warning font-weight-bold"><i class="fas fa-user-tie mr-2"></i>Head of Household</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Select Head Resident</label>
                                    <select name="head_resident_id" id="headResidentSelect" class="form-control border-light bg-light shadow-sm custom-select" disabled>
                                        <option value=""> Select Purok/Sitio first </option>
                                    </select>
                                    <small class="text-muted mt-2 d-block">Shows residents from selected purok and unassigned residents.</small>
                                </div>
                            </div>
                        </div>
                        <div id="loadingAlert" class="alert alert-light border shadow-sm text-info mt-3" style="display:none;">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Loading residents from selected purok…
                        </div>
                        <div id="noResidentsAlert" class="alert alert-light border shadow-sm text-warning mt-3" style="display:none;">
                            <i class="fas fa-exclamation-triangle mr-2"></i> 
                            No residents found in this purok. 
                            <a href="<?= base_url('resident/create') ?>" class="alert-link ml-2">Add a resident first →</a>
                        </div>
                    </div>
                </div>

                <!-- HOUSEHOLD MEMBERS MANAGER -->
                <div class="card border-0 shadow-sm rounded-lg mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                        <h4 class="card-title text-info font-weight-bold mb-0"><i class="fas fa-users mr-2"></i>Household Members</h4>
                        <div class="card-tools">
                            <span class="badge badge-primary px-3 py-2 mr-2 shadow-sm rounded-pill" id="selectedCount">0 selected</span>
                            <button type="button" class="btn btn-outline-info btn-sm shadow-sm" id="toggleAllMembers" title="Toggle All" disabled>
                                <i class="fas fa-check-double mr-1"></i> Toggle All
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="membersLoadingAlert" class="alert alert-light border shadow-sm text-info m-4" style="display:none;">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Loading members list…
                        </div>
                        <div id="noResidentsWarning" class="alert alert-light border shadow-sm text-warning m-4" style="display:none;">
                            <i class="fas fa-exclamation-triangle mr-2"></i> 
                            No residents found in this purok. 
                            <a href="<?= base_url('resident/create') ?>" class="alert-link ml-2">Add a resident first →</a>
                        </div>
                        
                        <div id="membersTableContainer" style="display:none;">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="membersTable">
                                    <thead class="bg-light text-secondary">
                                        <tr>
                                            <th class="border-top-0 pl-4" style="width: 40px;">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="selectAllCheckbox">
                                                    <label class="custom-control-label" for="selectAllCheckbox"></label>
                                                </div>
                                            </th>
                                            <th class="border-top-0 font-weight-bold">Resident Name</th>
                                            <th class="border-top-0 font-weight-bold pr-4" style="width: 250px;">Relationship to Head</th>
                                        </tr>
                                    </thead>
                                    <tbody id="membersTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                        <div id="emptyMembersState" class="text-center py-5 text-muted">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-users fa-2x text-secondary"></i>
                            </div>
                            <h5 class="font-weight-bold text-dark">No Purok Selected</h5>
                            <p class="mb-0">Select a Purok above to load residents</p>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0 rounded-bottom">
                        <small class="text-muted font-weight-bold">
                            <i class="fas fa-info-circle mr-1 text-info"></i> 
                            Check residents to add them to this household. Set their relationship to the head.
                        </small>
                    </div>
                </div>

                <input type="hidden" name="household_members_data" id="householdMembersData" value="[]">

                <div class="row mb-5">
                    <div class="col-12 d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('households') ?>" class="btn btn-light shadow-sm font-weight-bold px-4">
                            <i class="fas fa-arrow-left mr-2"></i> Cancel & Return
                        </a>
                        <button type="submit" class="btn btn-primary shadow px-5 font-weight-bold" id="submitBtn">
                            <i class="fas fa-save mr-2"></i> Save Household
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </section>
</div>

<div id="js-variables" style="display:none;"
     data-base-url="<?= base_url() ?>"
     data-csrf-token="<?= csrf_token() ?>"
     data-csrf-hash="<?= csrf_hash() ?>"
     data-old-household-no="<?= old('household_no') ?>">
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/households/household-create.js') ?>"></script>
<?= $this->endSection() ?>