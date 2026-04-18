<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <!-- Table Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Accounts</h3>
                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#AddNewModal">Add New</button>
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr><th>No.</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add/Edit Modals (Include your existing HTML here) -->
<div class="toasts-top-right fixed" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const baseUrl = "<?= site_url('staff/users') ?>";
</script>
<script src="<?= base_url('js/users/users.js') ?>"></script>
<?= $this->endSection() ?>
