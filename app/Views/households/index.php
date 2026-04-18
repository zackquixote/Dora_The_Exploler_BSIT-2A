<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Households Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('staff/dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Households</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <div><?= $error ?></div>
                    <?php endforeach; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-2 mb-md-0">
                            <i class="fas fa-home mr-2"></i> List of Households
                            <?php if (isset($selectedPurok) && $selectedPurok !== 'all'): ?>
                                <span class="badge badge-info ml-2">Filtered by: <?= $selectedPurok ?></span>
                            <?php endif; ?>
                        </h3>
                        <div class="d-flex flex-wrap gap-2">
                            <form method="GET" action="<?= base_url('households') ?>" class="d-flex">
                                <select name="purok" class="form-control form-control-sm mr-2" style="min-width: 180px;" onchange="this.form.submit()">
                                    <option value="all" <?= (isset($selectedPurok) && $selectedPurok == 'all') ? 'selected' : '' ?>>All Puroks</option>
                                    <option value="Purok Malipayon" <?= (isset($selectedPurok) && $selectedPurok == 'Purok Malipayon') ? 'selected' : '' ?>>Purok Malipayon</option>
                                    <option value="Purok Masagana" <?= (isset($selectedPurok) && $selectedPurok == 'Purok Masagana') ? 'selected' : '' ?>>Purok Masagana</option>
                                    <option value="Purok Cory" <?= (isset($selectedPurok) && $selectedPurok == 'Purok Cory') ? 'selected' : '' ?>>Purok Cory</option>
                                    <option value="Purok Kawayan" <?= (isset($selectedPurok) && $selectedPurok == 'Purok Kawayan') ? 'selected' : '' ?>>Purok Kawayan</option>
                                    <option value="Purok Pagla-um" <?= (isset($selectedPurok) && $selectedPurok == 'Purok Pagla-um') ? 'selected' : '' ?>>Purok Pagla-um</option>
                                </select>
                                <?php if (isset($selectedPurok) && $selectedPurok != 'all'): ?>
                                    <a href="<?= base_url('households') ?>" class="btn btn-secondary btn-sm">Clear</a>
                                <?php endif; ?>
                            </form>
                            <a href="<?= base_url('households/create') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus-circle mr-1"></i> Add Household
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Household No.</th>
                                    <th>Purok/Sitio</th>
                                    <th>Address</th>
                                    <th>Head of Household</th>
                                    <th>Residents</th>
                                    <th>House Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($households)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No households found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($households as $h): ?>
                                        <tr>
                                            <td><?= $h['id'] ?? '' ?></td>
                                            <td><strong>#<?= esc($h['household_no'] ?? '') ?></strong></td>
                                            <td><span class="badge bg-primary"><?= esc($h['sitio'] ?? 'Unassigned') ?></span></td>
                                            <td><?= esc($h['street_address'] ?? $h['address'] ?? 'N/A') ?></td>
                                            <td><?= esc($h['head_name'] ?? 'Not assigned') ?></td>
                                            <td><span class="badge bg-info"><?= $h['resident_count'] ?? 0 ?> members</span></td>
                                            <td><?= esc($h['house_type'] ?? 'N/A') ?></td>
                                            <td>
                                                <a href="<?= base_url('households/view/'.$h['id']) ?>" class="btn btn-sm btn-info">View</a>
                                                <a href="<?= base_url('households/edit/'.$h['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
<button type="button" class="btn btn-sm btn-danger delete-household" data-id="<?= $h['id'] ?>">
    <i class="fas fa-trash"></i> Delete
</button>                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Purok Statistics Cards -->
            <?php if (!empty($purokCounts)): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-pie mr-2"></i> Households per Purok</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php 
                                $colors = ['primary', 'success', 'warning', 'danger', 'info', 'secondary'];
                                $i = 0;
                                foreach ($purokCounts as $purok => $count): 
                                    $color = $colors[$i % count($colors)];
                                ?>
                                    <div class="col-md-2 col-sm-3 col-6 mb-3">
                                        <a href="<?= base_url('households?purok=' . urlencode($purok)) ?>" class="text-decoration-none">
                                            <div class="small-box bg-<?= $color ?> text-white p-3 text-center rounded">
                                                <h3 class="mb-1"><?= $count ?></h3>
                                                <small><?= $purok ?></small>
                                            </div>
                                        </a>
                                    </div>
                                <?php 
                                    $i++;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Delete household handler
    $(document).on('click', '.delete-household', function() {
        var householdId = $(this).data('id');
        var row = $(this).closest('tr');
        
        if (confirm('Are you sure you want to delete this household? This action cannot be undone.')) {
            $.ajax({
                url: '<?= base_url('households/delete') ?>/' + householdId,
                type: 'POST',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        row.fadeOut('slow', function() {
                            $(this).remove();
                            if ($('tbody tr').length === 0) {
                                location.reload();
                            }
                        });
                        showAlert('success', response.message);
                    } else {
                        showAlert('danger', response.message);
                    }
                },
                error: function() {
                    showAlert('danger', 'Error deleting household');
                }
            });
        }
    });
    
    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        `;
        $('.alert').remove();
        $('.content .container-fluid').prepend(alertHtml);
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() { $(this).remove(); });
        }, 3000);
    }
});
</script>

<style>
.gap-2 { gap: 0.5rem; }
.small-box { transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; }
.small-box:hover { transform: translateY(-5px); box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
</style>
<!-- External JavaScript file -->
<script src="<?= base_url('assets/js/household/households-index.js') ?>"></script>
<?= $this->endSection() ?>