<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>

<!-- CSRF Meta Tags -->
<meta name="csrf-name"  content="<?= csrf_token() ?>">
<meta name="csrf-token" content="<?= csrf_hash() ?>">

<div class="content-wrapper">

  <div id="alertBox" class="container-fluid pt-2"></div>

  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Residents Management</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('staff/dashboard') ?>">Home</a></li>
            <li class="breadcrumb-item active">Residents</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">List of Residents</h3>
          <div class="float-right">
            <button type="button" id="btnAddResident" class="btn btn-primary">
              <i class="fas fa-plus-circle mr-1"></i> Add New Resident
            </button>
          </div>
        </div>
        <div class="card-body">
          <table id="residentsTable" class="table table-bordered table-striped table-sm">
            <thead>
              <tr>
                <th>No.</th>
                <th>Full Name</th>
                <th>Birthdate</th>
                <th>Sex</th>
                <th>Civil Status</th>
                <th>Contact</th>
                <th>Household</th>
                <th>Categories</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- ══════════════════ ADD MODAL ══════════════════ -->
  <div class="modal fade" id="addResidentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">                          <!-- ✅ modal-content FIRST -->
        <form id="addResidentForm">                        <!-- ✅ form INSIDE modal-content -->
          <div class="modal-header bg-primary">
            <h5 class="modal-title text-white">
              <i class="fas fa-plus-circle mr-1"></i> Add New Resident
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>

          <div class="modal-body">
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
                <div class="form-group">
                  <label>Contact Number</label>
                  <input type="text" name="contact_number" class="form-control" placeholder="09123456789">
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
                    <option value="">-- Select Sex --</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Civil Status</label>
                  <select name="civil_status" class="form-control">
                    <option value="">-- Select Civil Status --</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Widowed">Widowed</option>
                    <option value="Separated">Separated</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Household</label>
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

            <div class="form-group">
              <label>Attributes</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="is_voter" id="add_is_voter" value="1">
                <label class="form-check-label" for="add_is_voter">Voter</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="is_senior_citizen" id="add_is_senior" value="1">
                <label class="form-check-label" for="add_is_senior">Senior Citizen</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="is_pwd" id="add_is_pwd" value="1">
                <label class="form-check-label" for="add_is_pwd">PWD</label>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <i class="fas fa-times-circle mr-1"></i> Cancel
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save mr-1"></i> Save Resident
            </button>
          </div>

        </form>                                            <!-- ✅ form closes before modal-content closes -->
      </div>
    </div>
  </div>

  <!-- ══════════════════ EDIT MODAL ══════════════════ -->
  <div class="modal fade" id="editResidentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">                          <!-- ✅ modal-content FIRST -->
        <form id="editResidentForm">                       <!-- ✅ form INSIDE modal-content -->
          <input type="hidden" id="edit_id" name="id">
          <div class="modal-header bg-warning">
            <h5 class="modal-title text-white">
              <i class="fas fa-edit mr-1"></i> Edit Resident
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>

          <div class="modal-body">
            <div id="editErrors"></div>
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
                <div class="form-group">
                  <label>Contact Number</label>
                  <input type="text" name="contact_number" id="edit_contact_number" class="form-control">
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
                    <option value="">-- Select Sex --</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Civil Status</label>
                  <select name="civil_status" id="edit_civil_status" class="form-control">
                    <option value="">-- Select Civil Status --</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Widowed">Widowed</option>
                    <option value="Separated">Separated</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Household</label>
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

            <div class="form-group">
              <label>Attributes</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="is_voter" id="edit_is_voter" value="1">
                <label class="form-check-label" for="edit_is_voter">Voter</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="is_senior_citizen" id="edit_is_senior_citizen" value="1">
                <label class="form-check-label" for="edit_is_senior_citizen">Senior Citizen</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="is_pwd" id="edit_is_pwd" value="1">
                <label class="form-check-label" for="edit_is_pwd">PWD</label>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <i class="fas fa-times-circle mr-1"></i> Cancel
            </button>
            <button type="submit" class="btn btn-warning">
              <i class="fas fa-save mr-1"></i> Update Resident
            </button>
          </div>

        </form>                                            <!-- ✅ form closes before modal-content closes -->
      </div>
    </div>
  </div>

  <!-- ══════════════════ VIEW MODAL ══════════════════ -->
  <div class="modal fade" id="viewResidentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-info">
          <h5 class="modal-title text-white">
            <i class="fas fa-eye mr-1"></i> View Resident
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" id="viewResidentBody"></div>
      </div>
    </div>
  </div>

  <!-- ══════════════════ DELETE MODAL ══════════════════ -->
  <div class="modal fade" id="deleteResidentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-white">
            <i class="fas fa-trash mr-1"></i> Delete Resident
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete <strong id="deleteResidentName"></strong>?</p>
          <p class="text-danger"><small>This action cannot be undone.</small></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" id="btnConfirmDelete" class="btn btn-danger">
            <i class="fas fa-trash mr-1"></i> Delete
          </button>
        </div>
      </div>
    </div>
  </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>const BASE = "<?= base_url() ?>";</script>
<script src="<?= base_url('js/residents/resident.js') ?>"></script>
<?= $this->endSection() ?>