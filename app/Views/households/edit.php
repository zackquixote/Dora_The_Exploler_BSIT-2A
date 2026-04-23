<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">

    <!-- PAGE HEADER -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Household</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('staff/dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('households') ?>">Households</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('households/view/'.$household['id']) ?>"><?= esc($household['household_no']) ?></a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                <h5><i class="icon fas fa-ban"></i> Please fix the following:</h5>
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $e): ?>
                        <li><?= esc($e) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php endif; ?>

            <form id="householdForm" action="<?= base_url('households/update/'.$household['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $household['id'] ?>">

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
                                    <input type="text" name="household_no" id="householdNo" class="form-control"
                                           value="<?= esc($household['household_no']) ?>" readonly>
                                    <small class="text-muted">Household number cannot be changed</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Purok / Sitio <span class="text-danger">*</span></label>
                                    <select name="sitio" id="sitioSelect" class="form-control" required>
                                        <option value="">— Select Purok —</option>
                                        <option value="Purok Malipayon" <?= ($household['sitio']??'')=='Purok Malipayon'?'selected':'' ?>>Purok Malipayon</option>
                                        <option value="Purok Masagana"  <?= ($household['sitio']??'')=='Purok Masagana' ?'selected':'' ?>>Purok Masagana</option>
                                        <option value="Purok Cory"      <?= ($household['sitio']??'')=='Purok Cory'     ?'selected':'' ?>>Purok Cory</option>
                                        <option value="Purok Kawayan"   <?= ($household['sitio']??'')=='Purok Kawayan'  ?'selected':'' ?>>Purok Kawayan</option>
                                        <option value="Purok Pagla-um"  <?= ($household['sitio']??'')=='Purok Pagla-um' ?'selected':'' ?>>Purok Pagla-um</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>House Type</label>
                                    <select name="house_type" class="form-control">
                                        <option value="">— Select Type —</option>
                                        <option value="Concrete"       <?= ($household['house_type']??'')=='Concrete'       ?'selected':'' ?>>Concrete</option>
                                        <option value="Semi-Concrete"  <?= ($household['house_type']??'')=='Semi-Concrete'  ?'selected':'' ?>>Semi-Concrete</option>
                                        <option value="Wood"           <?= ($household['house_type']??'')=='Wood'           ?'selected':'' ?>>Wood</option>
                                        <option value="Light Materials"<?= ($household['house_type']??'')=='Light Materials'?'selected':'' ?>>Light Materials</option>
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
                        <input type="hidden" name="address" id="completeAddress" value="<?= esc($household['address'] ?? '') ?>">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" name="street_address" id="streetAddress" class="form-control"
                                           value="<?= esc($household['street_address'] ?? '') ?>"
                                           placeholder="e.g., Block 1, Lot 2, House #12">
                                    <small class="text-muted">Enter specific house details. Full address is auto-generated.</small>
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
                                    <select name="head_resident_id" id="headResidentSelect" class="form-control">
                                        <option value="">— Select Head of Household —</option>
                                    </select>
                                    <small class="text-muted">Shows residents from selected purok and current members.</small>
                                </div>
                            </div>
                        </div>
                        <div id="membersLoadingAlert" class="alert alert-info" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i> Loading residents…
                        </div>
                    </div>
                </div>

                <!-- CURRENT MEMBERS SUMMARY -->
                <?php if (!empty($residentCount) && $residentCount > 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    This household currently has <strong><?= $residentCount ?> registered resident(s)</strong>.
                </div>
                <?php endif; ?>

                <!-- HOUSEHOLD MEMBERS MANAGER -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users mr-2"></i>Manage Household Members</h3>
                        <div class="card-tools">
                            <span class="badge badge-light mr-2" id="selectedCount">0 selected</span>
                            <button type="button" class="btn btn-tool" id="toggleAllMembers" title="Toggle All">
                                <i class="fas fa-check-double"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <!-- Members Table -->
                        <div id="membersTableContainer">
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
                        <div id="emptyMembersState" class="text-center py-5 text-muted" style="display:none;">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p>No residents available in this purok</p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Check residents to add them to this household. Uncheck to remove them.
                        </small>
                    </div>
                </div>

                <!-- Hidden field for member data -->
                <input type="hidden" name="household_members_data" id="householdMembersData" value="[]">

                <!-- JS VARIABLES BRIDGE -->
                <div id="js-variables" style="display:none;"
                     data-base-url="<?= base_url() ?>"
                     data-csrf-token="<?= csrf_token() ?>"
                     data-csrf-hash="<?= csrf_hash() ?>"
                     data-household-id="<?= $household['id'] ?>"
                     data-head-id="<?= $household['head_resident_id'] ?: 'null' ?>"
                     data-current-sitio="<?= esc($household['sitio'] ?? '') ?>"
                     data-current-members='<?= json_encode($currentMembers ?? []) ?>'>
                </div>

                <!-- FORM ACTIONS -->
                <div class="row mb-4">
                    <div class="col-12">
                        <a href="<?= base_url('households/view/'.$household['id']) ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary float-right" id="submitBtn">
                            <i class="fas fa-save"></i> Update Household
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </section>
</div>

<?= $this->endSection() ?>

<!-- Load Edit Specific JS -->
<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/households/household-edit.js') ?>"></script>
<?= $this->endSection() ?>