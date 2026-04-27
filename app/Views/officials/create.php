<?php 
// ---------------------------------------------------------
// SMART THEME LOADER
// ---------------------------------------------------------
 $role = session()->get('role');
 $template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- ... the rest of your content stays the same ... -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Add Official</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('officials') ?>">Officials</a></li>
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Official Details</h3>
                </div>
                <form action="<?= base_url('officials/store') ?>" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" name="full_name" class="form-control" value="<?= old('full_name') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Position</label>
                                    <select name="position" class="form-control custom-select" required>
                                        <option value="">Select Position</option>
                                        <option value="Punong Barangay">Punong Barangay</option>
                                        <option value="Kagawad - 1st District">Kagawad - 1st District</option>
                                        <option value="Kagawad - 2nd District">Kagawad - 2nd District</option>
                                        <option value="Kagawad - 3rd District">Kagawad - 3rd District</option>
                                        <option value="Kagawad - 4th District">Kagawad - 4th District</option>
                                        <option value="Kagawad - 5th District">Kagawad - 5th District</option>
                                        <option value="Kagawad - 6th District">Kagawad - 6th District</option>
                                        <option value="Kagawad - 7th District">Kagawad - 7th District</option>
                                        <option value="SK Chairman">SK Chairman</option>
                                        <option value="Barangay Secretary">Barangay Secretary</option>
                                        <option value="Barangay Treasurer">Barangay Treasurer</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="text" name="contact_number" class="form-control" value="<?= old('contact_number') ?>">
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="form-group">
                                    <label>Photo</label>
                                    <input type="file" name="photo" class="form-control-file" id="photoInput">
                                    <small class="text-muted">Max size: 2MB (JPG/PNG)</small>
                                    <div class="mt-2">
                                        <img id="preview" src="<?= base_url('assets/img/default.png') ?>" class="img-circle" width="100" height="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Save Official</button>
                        <a href="<?= base_url('officials') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
    // Simple Image Preview
    document.getElementById('photoInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('preview').src = URL.createObjectURL(file);
        }
    });
</script>

<?= $this->endSection() ?>