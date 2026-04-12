<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>

<!-- CSRF Meta Tags -->
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
          <h1 class="m-0">Households Management</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?php echo base_url('staff/dashboard'); ?>">Home</a></li>
            <li class="breadcrumb-item active">Households</li>
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
              <h3 class="card-title">List of Households</h3>
              <div class="float-right">
                <button type="button" id="btnAddHousehold" class="btn btn-md btn-primary">
                  <i class="fas fa-plus-circle mr-1"></i> Add New Household
                </button>
              </div>
            </div>
            <div class="card-body">
              <table id="householdsTable" class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th width="5%">No.</th>
                    <th width="15%">Household No.</th>
                    <th width="20%">Street Address</th>
                    <th width="15%">Sitio</th>
                    <th width="15%">House Type</th>
                    <th width="15%">Head Resident</th>
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

  <!-- ADD MODAL -->
  <div class="modal fade" id="addHouseholdModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <form id="addHouseholdForm">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <h5 class="modal-title text-white">
              <i class="fas fa-plus-circle mr-1"></i> Add New Household
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>

          <div class="modal-body">
            <div id="addErrors"></div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Household No. <span class="text-danger">*</span></label>
                  <input type="text" name="household_no" class="form-control" placeholder="HH-001" required>
                </div>
                <div class="form-group">
                  <label>Sitio</label>
                  <input type="text" name="sitio" class="form-control" placeholder="Sitio Name">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Street Address</label>
                  <input type="text" name="street_address" class="form-control" placeholder="123 Main St">
                </div>
                <div class="form-group">
                  <label>House Type</label>
                  <select name="house_type" class="form-control">
                    <option value="">-- Select Type --</option>
                    <option value="concrete">Concrete</option>
                    <option value="wooden">Wooden</option>
                    <option value="mixed">Mixed</option>
                    <option value="bamboo">Bamboo</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Head Resident</label>
                  <select name="head_resident_id" id="add_head_resident_id" class="form-control">
                    <option value="">-- Select Head Resident --</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" id="btnSaveHousehold" class="btn btn-primary">
              <i class="fas fa-save mr-1"></i> Save Household
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- EDIT MODAL -->
  <div class="modal fade" id="editHouseholdModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <form id="editHouseholdForm">
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-content">
          <div class="modal-header bg-warning">
            <h5 class="modal-title text-white">
              <i class="fas fa-edit mr-1"></i> Edit Household
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>

          <div class="modal-body">
            <div id="editErrors"></div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Household No. <span class="text-danger">*</span></label>
                  <input type="text" name="household_no" id="edit_household_no" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Sitio</label>
                  <input type="text" name="sitio" id="edit_sitio" class="form-control">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Street Address</label>
                  <input type="text" name="street_address" id="edit_street_address" class="form-control">
                </div>
                <div class="form-group">
                  <label>House Type</label>
                  <select name="house_type" id="edit_house_type" class="form-control">
                    <option value="">-- Select Type --</option>
                    <option value="concrete">Concrete</option>
                    <option value="wooden">Wooden</option>
                    <option value="mixed">Mixed</option>
                    <option value="bamboo">Bamboo</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Head Resident</label>
                  <select name="head_resident_id" id="edit_head_resident_id" class="form-control">
                    <option value="">-- Select Head Resident --</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" id="btnUpdateHousehold" class="btn btn-warning">
              <i class="fas fa-save mr-1"></i> Update Household
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- VIEW MODAL -->
  <div class="modal fade" id="viewHouseholdModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-info">
          <h5 class="modal-title text-white">
            <i class="fas fa-eye mr-1"></i> View Household
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" id="viewHouseholdBody">
          <!-- Content loaded via AJAX -->
        </div>
      </div>
    </div>
  </div>

  <!-- DELETE MODAL -->
  <div class="modal fade" id="deleteHouseholdModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-white">
            <i class="fas fa-trash mr-1"></i> Delete Household
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete household <strong id="deleteHouseholdName"></strong>?</p>
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

<script>
    const BASE = "<?php echo base_url(); ?>";
</script>

<script>
    <script src="<?php echo base_url('js/households/household.js'); ?>"></script>
</script>