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
            
            <!-- FLASH ERRORS -->
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
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-home mr-2"></i>Household Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Household Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="household_no" id="householdNo" class="form-control"
                                               value="<?= old('household_no', $generatedHouseholdNo ?? '') ?>"
                                               placeholder="e.g., HH-2024-001">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" id="generateHouseholdNo" title="Generate new household number">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success" id="checkHouseholdNo" title="Check availability">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted" id="householdNoFeedback">Auto-generated unique number (you can edit)</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Purok / Sitio <span class="text-danger">*</span></label>
                                    <select name="sitio" id="sitioSelect" class="form-control" required>
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
                                    <label>House Type</label>
                                    <select name="house_type" class="form-control">
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
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-map-marker-alt mr-2"></i>Address Information</h3>
                    </div>
                    <div class="card-body">
                        
                        <!-- HIDDEN INPUT: Generates address in background for DB storage -->
                        <input type="hidden" name="address" id="completeAddress" value="<?= old('address') ?>">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" name="street_address" id="streetAddress" class="form-control"
                                           value="<?= old('street_address') ?>"
                                           placeholder="e.g., Block 1, Lot 2, House #12">
                                    <small class="text-muted">Enter specific house details (Block/Lot #). The full address will be auto-generated.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HEAD OF HOUSEHOLD -->
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-tie mr-2"></i>Head of Household</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Select Head Resident</label>
                                    <select name="head_resident_id" id="headResidentSelect" class="form-control" disabled>
                                        <option value=""> Select Purok/Sitio first </option>
                                    </select>
                                    <small class="text-muted">Shows residents from selected purok and unassigned residents.</small>
                                </div>
                            </div>
                        </div>
                        <div id="loadingAlert" class="alert alert-info" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i> Loading residents from selected purok…
                        </div>
                        <div id="noResidentsAlert" class="alert alert-warning" style="display:none;">
                            <i class="fas fa-exclamation-triangle"></i> 
                            No residents found in this purok. 
                            <a href="<?= base_url('resident/create') ?>" class="alert-link">Add a resident first →</a>
                        </div>
                    </div>
                </div>

                <!-- HOUSEHOLD MEMBERS MANAGER -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users mr-2"></i>Household Members</h3>
                        <div class="card-tools">
                            <span class="badge badge-light mr-2" id="selectedCount">0 selected</span>
                            <button type="button" class="btn btn-tool" id="toggleAllMembers" title="Toggle All" disabled>
                                <i class="fas fa-check-double"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="membersLoadingAlert" class="alert alert-info m-3" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i> Loading members list…
                        </div>
                        <div id="noResidentsWarning" class="alert alert-warning m-3" style="display:none;">
                            <i class="fas fa-exclamation-triangle"></i> 
                            No residents found in this purok. 
                            <a href="<?= base_url('resident/create') ?>" class="alert-link">Add a resident first →</a>
                        </div>
                        
                        <!-- Members Table -->
                        <div id="membersTableContainer" style="display:none;">
                            <table class="table table-striped table-hover" id="membersTable">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">
                                            <input type="checkbox" id="selectAllCheckbox">
                                        </th>
                                        <th>Resident Name</th>
                                        <th style="width: 200px;">Relationship to Head</th>
                                    </tr>
                                </thead>
                                <tbody id="membersTableBody">
                                    <!-- Dynamically populated -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Empty state -->
                        <div id="emptyMembersState" class="text-center py-5 text-muted">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p>Select a Purok above to load residents</p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Check residents to add them to this household. Set their relationship to the head.
                        </small>
                    </div>
                </div>

                <!-- Hidden field to store member data -->
                <input type="hidden" name="household_members_data" id="householdMembersData" value="[]">

                <!-- FORM ACTIONS -->
                <div class="row mb-4">
                    <div class="col-12">
                        <a href="<?= base_url('households') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary float-right" id="submitBtn">
                            <i class="fas fa-save"></i> Save Household
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

<!-- Load Create Specific JS -->
<?= $this->section('scripts') ?>
<!-- REMOVED 'assets/' to match your folder structure -->
<script src="<?= base_url('js/households/household-create.js') ?>"></script>
<?= $this->endSection() ?>