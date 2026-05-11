<!-- Base URL for JS -->
<script>window.baseUrl = '<?= base_url() ?>' + (('<?= base_url() ?>'.endsWith('/')) ? '' : '/');</script>

<!-- Core Libraries -->
<script src="<?= base_url('assets/adminlte/plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

<!-- DataTables -->
<script src="<?= base_url('assets/adminlte/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') ?>"></script>

<!-- External -->
<script src="<?= base_url('assets/adminlte/plugins/toastr/toastr.min.js') ?>"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer src="<?= base_url('js/blotter/notifications.js') ?>?v=<?= time() ?>"></script>

<!-- BMIS Core (mobile toggle, CSRF, search, dark mode, shortcuts) -->
<script defer src="<?= base_url('js/theme/bmis-core.js') ?>?v=<?= time() ?>"></script>

<!-- Flash data for toastr (requires PHP, kept inline) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (session()->getFlashdata('success')): ?>
        toastr.success('<?= esc(session()->getFlashdata('success')) ?>');
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        toastr.error('<?= esc(session()->getFlashdata('error')) ?>');
    <?php endif; ?>
});
</script>
