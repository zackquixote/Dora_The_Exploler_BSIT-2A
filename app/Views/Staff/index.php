<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
  <!-- Content Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Residents Management</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Dashboard v1</li>
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
                <button type="button" class="btn btn-md btn-primary" data-toggle="modal" data-target="#addResidentModal">
                  <i class="fa fa-plus-circle fa fw"></i> Add New Resident
                </button>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
               <table id="example1" class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th width="5%">No.</th>
                    <th style="display:none;">ID</th>
                    <th width="25%">Full Name</th>
                    <th width="10%">Sex</th>
                    <th width="15%">Birthdate</th>
                    <th width="15%">Household</th>
                    <th width="15%">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Data will be loaded via AJAX/JS -->
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>
    </div>
  </section>

  <!-- ✅ ADD NEW MODAL -->
  <div class="modal fade" id="addResidentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <!-- REMOVED action and method attributes to match Person view logic -->
      <form id="addResidentForm">
        <?= csrf_field() ?>
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <h5 class="modal-title text-white"><i class="fa fa-plus-circle fa fw"></i> Add New Resident</h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>

          <div class="modal-body">
            <div class="row">
                <!-- Name Column -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control" placeholder="Juan" required />
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" placeholder="Dela" />
                    </div>
                    <div class="form-group">
                        <label>Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control" placeholder="Cruz" required />
                    </div>
                </div>

                <!-- Details Column -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Birthdate <span class="text-danger">*</span></label>
                        <input type="date" name="birthdate" class="form-control" required />
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
                        <input type="text" name="contact_number" class="form-control" placeholder="09123456789" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Occupation</label>
                        <input type="text" name="occupation" class="form-control" />
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Household ID</label>
                        <select name="household_id" class="form-control select2" style="width: 100%;">
                            <option value="">Select Household</option>
                            <!-- Example Static Option -->
                            <option value="1">Block 1 Lot 5</option> 
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Relationship to Head</label>
                        <input type="text" name="relationship_to_head" class="form-control" placeholder="e.g. Son, Spouse" />
                    </div>
                </div>
            </div>

            <hr>
            <div class="row">
                <div class="col-md-12">
                    <label><strong>Attributes</strong></label>
                    <div class="d-flex gap-3 mt-2">
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
            </div>

          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class='fas fa-times-circle'></i> Cancel</button>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Resident</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ✅ EDIT MODAL -->
  <div class="modal fade" id="editResidentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <!-- REMOVED action and method attributes to match Person view logic -->
      <form id="editResidentForm">
         <?= csrf_field() ?>
        <div class="modal-content">
          <div class="modal-header bg-warning">
            <h5 class="modal-title"><i class="far fa-edit fa fw"></i> Edit Resident</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- Hidden ID for updating -->
            <input type="hidden" id="edit_id" name="id">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" id="edit_first_name" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" name="middle_name" id="edit_middle_name" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" id="edit_last_name" class="form-control" required />
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Birthdate</label>
                        <input type="date" name="birthdate" id="edit_birthdate" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Sex</label>
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
                        <label>Contact Number</label>
                        <input type="text" name="contact_number" id="edit_contact_number" class="form-control" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Occupation</label>
                        <input type="text" name="occupation" id="edit_occupation" class="form-control" />
                    </div>
                </div>
            </div>

             <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Household ID</label>
                        <select name="household_id" id="edit_household_id" class="form-control select2" style="width: 100%;">
                            <option value="1">Block 1 Lot 5</option> 
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Relationship to Head</label>
                        <input type="text" name="relationship_to_head" id="edit_relationship_to_head" class="form-control" />
                    </div>
                </div>
            </div>

            <hr>
            <div class="row">
                <div class="col-md-12">
                    <label><strong>Attributes</strong></label>
                    <div class="d-flex gap-3 mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_is_voter" name="is_voter" value="1">
                            <label class="form-check-label">Voter</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_is_senior_citizen" name="is_senior_citizen" value="1">
                            <label class="form-check-label">Senior Citizen</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_is_pwd" name="is_pwd" value="1">
                            <label class="form-check-label">PWD</label>
                        </div>
                    </div>
                </div>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class='fas fa-times-circle'></i> Cancel</button>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update Changes</button>
          </div>
        </div>
      </form>
    </div>
  </div>

</div>
<!-- /.content-wrapper -->

<!-- Toast Container -->
<div class="toasts-top-right fixed" id="toast-container" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Define Base URL for JS access -->
<script> const baseUrl = "<?= base_url() ?>"; </script>

<!-- DataTables & Select2 -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Initialize Select2 -->
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>

<!-- Your Custom JS -->
<script src="<?= base_url('js/residents/resident.js') ?>"></script>
<?= $this->endSection() ?>