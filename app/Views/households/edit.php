<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
  <!-- Content Header -->
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
            <li class="breadcrumb-item active">Edit</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <section class="content">
    <div class="container-fluid">

      <!-- Validation Errors -->
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

      <div class="card card-warning card-outline">
        <div class="card-header">
          <h3 class="card-title">Household Details</h3>
        </div>

        <form role="form" action="<?= base_url('households/update/' . $household['id']) ?>" method="post">
          <?= csrf_field() ?>

          <div class="card-body">
            <div class="row">

              <!-- Left Column -->
              <div class="col-md-6">
                <div class="form-group">
                  <label>Household No. <span class="text-danger">*</span></label>
                  <input type="text" name="household_no" class="form-control"
                         placeholder="e.g. HH-001" required
                         value="<?= old('household_no', $household['household_no']) ?>">
                </div>

                <div class="form-group">
                  <label>Sitio</label>
                  <input type="text" name="sitio" class="form-control"
                         placeholder="e.g. Sitio Maligaya"
                         value="<?= old('sitio', $household['sitio']) ?>">
                </div>

                <div class="form-group">
                  <label>Street Address</label>
                  <input type="text" name="street_address" class="form-control"
                         placeholder="e.g. 123 Rizal St."
                         value="<?= old('street_address', $household['street_address']) ?>">
                </div>
              </div>

              <!-- Right Column -->
              <div class="col-md-6">
                <?php
                  $houseType = old('house_type', $household['house_type']);
                  $houseTypes = ['Concrete', 'Semi-Concrete', 'Wood', 'Light Materials'];
                ?>
                <div class="form-group">
                  <label>House Type</label>
                  <select name="house_type" class="form-control">
                    <option value="">Select Type</option>
                    <?php foreach ($houseTypes as $type): ?>
                      <option value="<?= $type ?>" <?= $houseType == $type ? 'selected' : '' ?>>
                        <?= $type ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="form-group">
                  <label>Head Resident</label>
                  <select name="head_resident_id" class="form-control select2" style="width:100%;">
                    <option value="">-- Select Head Resident --</option>
                    <?php if (!empty($residents)): ?>
                      <?php foreach ($residents as $r): ?>
                        <?php
                          $selectedHead = old('head_resident_id', $household['head_resident_id']);
                        ?>
                        <option value="<?= $r['id'] ?>"
                          <?= $selectedHead == $r['id'] ? 'selected' : '' ?>>
                          <?= esc($r['last_name']) ?>, <?= esc($r['first_name']) ?>
                        </option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
              </div>

            </div>
          </div>
          <!-- /.card-body -->

          <div class="card-footer">
            <button type="submit" class="btn btn-warning">
              <i class="fas fa-save mr-2"></i>Update Household
            </button>
            <a href="<?= base_url('households') ?>" class="btn btn-default ml-2">Cancel</a>
          </div>
        </form>
      </div>
      <!-- /.card -->

    </div>
  </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function () {
    $('.select2').select2();
  });
</script>
<?= $this->endSection() ?>