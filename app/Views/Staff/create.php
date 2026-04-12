<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
  <!-- Content Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Add New Resident</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('staff/dashboard') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('residents') ?>">Residents</a></li>
            <li class="breadcrumb-item active">Add New</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <section class="content">
    <div class="container-fluid">
      
      <!-- Display Validation Errors if any -->
      <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          <h5><i class="icon fas fa-ban"></i> Error!</h5>
          <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
              <li><?= esc($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="card card-primary card-outline">
        <div class="card-header">
          <h3 class="card-title">Resident Details</h3>
        </div>
        
        <!-- Form starts here -->
        <form role="form" action="<?= base_url('staff/resident/store') ?>" method="post">
          <?= csrf_field() ?>
          
          <div class="card-body">
            
            <div class="row">
              <!-- Left Column -->
              <div class="col-md-6">
                <div class="form-group">
                  <label>First Name <span class="text-danger">*</span></label>
                  <input type="text" name="first_name" class="form-control" placeholder="Enter first name" required value="<?= old('first_name') ?>">
                </div>

                <div class="form-group">
                  <label>Middle Name</label>
                  <input type="text" name="middle_name" class="form-control" placeholder="Enter middle name" value="<?= old('middle_name') ?>">
                </div>

                <div class="form-group">
                  <label>Last Name <span class="text-danger">*</span></label>
                  <input type="text" name="last_name" class="form-control" placeholder="Enter last name" required value="<?= old('last_name') ?>">
                </div>

                <div class="form-group">
                  <label>Birthdate <span class="text-danger">*</span></label>
                  <input type="date" name="birthdate" class="form-control" required value="<?= old('birthdate') ?>">
                </div>

                <div class="form-group">
                  <label>Sex <span class="text-danger">*</span></label>
                  <select name="sex" class="form-control" required>
                    <option value="">Select Sex</option>
                    <option value="male" <?= old('sex') == 'male' ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= old('sex') == 'female' ? 'selected' : '' ?>>Female</option>
                  </select>
                </div>
              </div>

              <!-- Right Column -->
              <div class="col-md-6">
                <div class="form-group">
                  <label>Civil Status <span class="text-danger">*</span></label>
                  <select name="civil_status" class="form-control" required>
                    <option value="">Select Status</option>
                    <option value="single" <?= old('civil_status') == 'single' ? 'selected' : '' ?>>Single</option>
                    <option value="married" <?= old('civil_status') == 'married' ? 'selected' : '' ?>>Married</option>
                    <option value="widowed" <?= old('civil_status') == 'widowed' ? 'selected' : '' ?>>Widowed</option>
                    <option value="separated" <?= old('civil_status') == 'separated' ? 'selected' : '' ?>>Separated</option>
                  </select>
                </div>

                <div class="form-group">
                  <label>Relationship to Head</label>
                  <input type="text" name="relationship_to_head" class="form-control" placeholder="e.g. Son, Daughter, Spouse" value="<?= old('relationship_to_head') ?>">
                </div>

                <div class="form-group">
                  <label>Household ID</label>
                  <input type="number" name="household_id" class="form-control" placeholder="Enter Household ID" value="<?= old('household_id') ?>">
                </div>

                <div class="form-group">
                  <label>Contact Number</label>
                  <input type="text" name="contact_number" class="form-control" placeholder="0912 345 6789" value="<?= old('contact_number') ?>">
                </div>


            <hr>
            
            <div class="row">
                <div class="col-md-12">
                    <label><strong>Attributes / Categories</strong></label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="is_voter" value="1" <?= old('is_voter') ? 'checked' : '' ?>>
                        <label class="form-check-label">Registered Voter</label>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="is_senior_citizen" value="1" <?= old('is_senior_citizen') ? 'checked' : '' ?>>
                        <label class="form-check-label">Senior Citizen</label>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="is_pwd" value="1" <?= old('is_pwd') ? 'checked' : '' ?>>
                        <label class="form-check-label">PWD (Person with Disability)</label>
                    </div>
                </div>
            </div>

          </div>
          <!-- /.card-body -->

          <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Save Resident</button>
            <a href="<?= base_url('residents') ?>" class="btn btn-default ml-2">Cancel</a>
          </div>
        </form>
      </div>
      <!-- /.card -->

    </div>
  </section>
</div>

<?= $this->endSection() ?>