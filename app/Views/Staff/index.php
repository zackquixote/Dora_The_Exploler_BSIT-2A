<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>

<!-- CSRF Meta Tags (required by resident.js) -->
<meta name="csrf-name"  content="<?php echo csrf_token(); ?>">
<meta name="csrf-token" content="<?php echo csrf_hash(); ?>">

<div class="content-wrapper">

  <!-- Alert Box -->
  <div id="alertBox" class="container-fluid pt-2"></div>

  <!-- Content Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Residents Management</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?php echo base_url('staff/dashboard'); ?>">Home</a></li>
            <li class="breadcrumb-item active">Residents</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">List of Residents</h3>
              <div class="float-right">
                <!-- FIX: id="btnAddResident" so resident.js can bind the click -->
                <button type="button" id="btnAddResident" class="btn btn-md btn-primary">
                  <i class="fas fa-plus-circle mr-1"></i> Add New Resident
                </button>
              </div>
            </div>
            <div class="card-body">
              <!-- FIX: id changed from "example1" to "residentsTable" -->
              <table id="residentsTable" class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th width="5%">No.</th>
                    <th width="20%">Full Name</th>
                    <th width="8%">Sex</th>
                    <th width="12%">Birthdate</th>
                    <th width="10%">Civil Status</th>
                    <th width="10%">Household</th>
                    <th width="15%">Categories</th>
                    <th width="15%">Actions</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ══════════════════════════════════════════════════════════════ -->
  <!-- ADD MODAL                                                      -->
  <!-- ══════════════════════════════════════════════════════════════ -->
  <div class="modal fade" id="addResidentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <form id="addResidentForm">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <h5 class="modal-title text-white">
              <i class="fas fa-plus-circle mr-1"></i> Add New Resident
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>

          <div class="modal-body">
            <!-- Error container -->
            <div id="addErrors"></div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>First Name <span class="text-danger">*</span></label>
                  <input type="text" name="first_name" class="form-control" placeholder="Juan" required>
                </div>
                <div class="form-group">
                  <label>Middle Name</label>
                  <input type="text" name="middle_name" class="form-control" placeholder="Dela">
                </div>
                <div class="form-group">
                  <label>Last Name <span class="text-danger">*</span></label>
                  <input type="text" name="last_name" class="form-control" placeholder="Cruz" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Birthdate <span class="text-danger">*</span></label>
                  <input type="date" name="birthdate" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Sex <span class="text-danger">*</span></label>
                  <select name="sex" class="form-control" required>
                    <option value="">Select Sex</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Civil Status</label>
                  <select name="civil_status" class="form-control">
                    <option value="single">Single</option>
                    <option value="married">Married</option>
                    <option value="widowed">Widowed</option>
                    <option value="separated">Separated</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Contact Number</label>
                  <input type="text" name="contact_number" class="form-control" placeholder="09123456789">
                </div>
              </div>
              <div class="col-md-6">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Household</label>
                  <!-- FIX: id="add_household_id" so resident.js loadHouseholds() targets it -->
                  <select name="household_id" id="add_household_id" class="form-control">
                    <option value="">-- Select Household --</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Relationship to Head</label>
                  <input type="text" name="relationship_to_head" class="form-control" placeholder="e.g. Son, Spouse">
                </div>
              </div>
            </div>

            <hr>
            <label><strong>Attributes</strong></label>
            <div class="d-flex mt-2" style="gap: 1.5rem;">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_voter" value="1">
                <label class="form-check-label">Voter</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_senior_citizen" value="1">
                <label class="form-check-label">Senior Citizen</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_pwd" value="1">
                <label class="form-check-label">PWD</label>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <i class="fas fa-times-circle mr-1"></i> Cancel
            </button>
            <!-- FIX: id="btnSaveResident" so resident.js can disable it during save -->
            <button type="submit" id="btnSaveResident" class="btn btn-primary">
              <i class="fas fa-save mr-1"></i> Save Resident
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════════ -->
  <!-- EDIT MODAL                                                     -->
  <!-- ══════════════════════════════════════════════════════════════ -->
  <div class="modal fade" id="editResidentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <form id="editResidentForm">
        <div class="modal-content">
          <div class="modal-header bg-warning">
            <h5 class="modal-title">
              <i class="fas fa-edit mr-1"></i> Edit Resident
            </h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <div class="modal-body">
            <div id="editErrors"></div>
            <input type="hidden" id="edit_id" name="id">

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>First Name <span class="text-danger">*</span></label>
                  <input type="text" name="first_name" id="edit_first_name" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Middle Name</label>
                  <input type="text" name="middle_name" id="edit_middle_name" class="form-control">
                </div>
                <div class="form-group">
                  <label>Last Name <span class="text-danger">*</span></label>
                  <input type="text" name="last_name" id="edit_last_name" class="form-control" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Birthdate <span class="text-danger">*</span></label>
                  <input type="date" name="birthdate" id="edit_birthdate" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Sex <span class="text-danger">*</span></label>
                  <select name="sex" id="edit_sex" class="form-control" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Civil Status</label>
                  <select name="civil_status" id="edit_civil_status" class="form-control">
                    <option value="single">Single</option>
                    <option value="married">Married</option>
                    <option value="widowed">Widowed</option>
                    <option value="separated">Separated</option>
                  </select>
                </div>
              </div>
            </div>

            

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Household</label>
                  <!-- FIX: id="edit_household_id" for loadHouseholds() -->
                  <select name="household_id" id="edit_household_id" class="form-control">
                    <option value="">-- Select Household --</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Relationship to Head</label>
                  <input type="text" name="relationship_to_head" id="edit_relationship_to_head" class="form-control">
                </div>
              </div>
            </div>

            <hr>
            <label><strong>Attributes</strong></label>
            <div class="d-flex mt-2" style="gap: 1.5rem;">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="edit_is_voter" name="is_voter" value="1">
                <label class="form-check-label">Voter</label>
              </div>
              <div class="form-check">
                <!-- FIX: id changed from "edit_is_senior_citizen" to "edit_is_senior" to match resident.js -->
                <input class="form-check-input" type="checkbox" id="edit_is_senior" name="is_senior_citizen" value="1">
                <label class="form-check-label">Senior Citizen</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="edit_is_pwd" name="is_pwd" value="1">
                <label class="form-check-label">PWD</label>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <i class="fas fa-times-circle mr-1"></i> Cancel
            </button>
            <!-- FIX: id="btnUpdateResident" so resident.js can disable it during update -->
            <button type="submit" id="btnUpdateResident" class="btn btn-warning">
              <i class="fas fa-save mr-1"></i> Update Changes
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════════ -->
  <!-- VIEW MODAL                                                     -->
  <!-- ══════════════════════════════════════════════════════════════ -->
  <div class="modal fade" id="viewResidentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-info">
          <h5 class="modal-title text-white">
            <i class="fas fa-eye mr-1"></i> View Resident
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" id="viewResidentBody">
          <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════════ -->
  <!-- DELETE MODAL                                                   -->
  <!-- ══════════════════════════════════════════════════════════════ -->
  <div class="modal fade" id="deleteResidentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-white">
            <i class="fas fa-trash mr-1"></i> Delete Resident
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body text-center">
          <p>Are you sure you want to delete</p>
          <strong id="deleteResidentName"></strong>?
          <p class="text-muted mt-1"><small>This action cannot be undone.</small></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <!-- FIX: id="btnConfirmDelete" so resident.js can bind the confirm action -->
          <button type="button" id="btnConfirmDelete" class="btn btn-danger">
            <i class="fas fa-trash mr-1"></i> Delete
          </button>
        </div>
      </div>
    </div>
  </div>

</div>
<!-- /.content-wrapper -->

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<!-- FIX: APP global object replaces plain baseUrl; includes CSRF for resident.js -->
<script>
  const APP = {
    baseUrl:  "<?php echo base_url(); ?>",
    csrfName: "<?php echo csrf_token(); ?>",
    csrfHash: "<?php echo csrf_hash(); ?>"
  };
</script>

<!-- Your Custom JS -->
<script src="<?php echo base_url('js/residents/resident.js'); ?>"></script>

<?= $this->endSection() ?>