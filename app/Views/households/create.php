<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Add New Household</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('staff/dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('households') ?>">Households</a></li>
                        <li class="breadcrumb-item active">Add New</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <!-- FLASH ERRORS -->
            <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <h5><i class="icon fas fa-ban"></i> Please fix the following errors:</h5>
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $e): ?>
                        <li><?= esc($e) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php endif; ?>

            <form id="householdForm" action="<?= base_url('households/store') ?>" method="POST">
                <?= csrf_field() ?>

                <!-- HOUSEHOLD INFO -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-home mr-2"></i>Household Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Household Number <span class="text-danger">*</span></label>
                                    <input type="text" name="household_no" class="form-control"
                                           value="<?= old('household_no') ?>"
                                           placeholder="e.g., HH-2024-001">
                                    <small class="text-muted">Must be unique across all households</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Purok / Sitio <span class="text-danger">*</span></label>
                                    <select name="sitio" id="sitioSelect" class="form-control">
                                        <option value="">— Select Purok —</option>
                                        <option value="Purok Malipayon" <?= old('sitio')=='Purok Malipayon'?'selected':'' ?>>Purok Malipayon</option>
                                        <option value="Purok Masagana"  <?= old('sitio')=='Purok Masagana' ?'selected':'' ?>>Purok Masagana</option>
                                        <option value="Purok Cory"      <?= old('sitio')=='Purok Cory'     ?'selected':'' ?>>Purok Cory</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>House Type</label>
                                    <select name="house_type" class="form-control">
                                        <option value="">— Select Type —</option>
                                        <option value="Concrete"        <?= old('house_type')=='Concrete'       ?'selected':'' ?>>Concrete</option>
                                        <option value="Semi-Concrete"   <?= old('house_type')=='Semi-Concrete'  ?'selected':'' ?>>Semi-Concrete</option>
                                        <option value="Wood"            <?= old('house_type')=='Wood'            ?'selected':'' ?>>Wood</option>
                                        <option value="Light Materials" <?= old('house_type')=='Light Materials'?'selected':'' ?>>Light Materials</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ADDRESS -->
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-map-marker-alt mr-2"></i>Address Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" name="street_address" class="form-control"
                                           value="<?= old('street_address') ?>"
                                           placeholder="e.g., Block 1, Lot 2, House #12">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Complete Address</label>
                                    <input type="text" name="address" class="form-control"
                                           value="<?= old('address') ?>"
                                           placeholder="e.g., Barangay Salong, Iloilo">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HEAD OF HOUSEHOLD -->
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user mr-2"></i>Head of Household</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Select Head Resident</label>
                                    <select name="head_resident_id" id="headResidentSelect" class="form-control" disabled>
                                        <option value="">— Select Purok/Sitio first —</option>
                                    </select>
                                    <small class="text-muted">Shows residents from selected purok and unassigned residents.</small>
                                </div>
                            </div>
                        </div>
                        <div id="loadingAlert" class="alert alert-info" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i> Loading residents from selected purok…
                        </div>
                        <div id="noResidentsAlert" class="alert alert-warning" style="display:none;">
                            <i class="fas fa-exclamation-triangle"></i> 
                            No residents found in this purok. 
                            <a href="<?= base_url('resident/create') ?>" class="alert-link">Add a resident first →</a>
                        </div>
                    </div>
                </div>

                <!-- FORM ACTIONS -->
                <div class="row mb-4">
                    <div class="col-12">
                        <a href="<?= base_url('households') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary float-right">
                            <i class="fas fa-save"></i> Save Household
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </section>
</div>

<script>
$(document).ready(function () {

    $('#sitioSelect').on('change', function () {
        var sitio      = $(this).val();
        var headSelect = $('#headResidentSelect');

        $('#noResidentsAlert, #loadingAlert').hide();

        if (!sitio) {
            headSelect.html('<option value="">— Select Purok/Sitio first —</option>').prop('disabled', true);
            return;
        }

        $('#loadingAlert').show();
        headSelect.html('<option value="">Loading…</option>').prop('disabled', true);

        $.ajax({
            url  : '<?= base_url('households/getResidentsBySitio') ?>',
            type : 'POST',
            data : { 
                sitio: sitio, 
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>' 
            },
            dataType : 'json',
            success: function (res) {
                $('#loadingAlert').hide();
                
                if (res.csrf_hash) {
                    $('input[name="<?= csrf_token() ?>"]').val(res.csrf_hash);
                }
                
                if (res.status === 'success' && res.residents && res.residents.length > 0) {
                    var opts = '<option value="">— Select Head of Household —</option>';
                    $.each(res.residents, function (i, r) {
                        var name = r.last_name + ', ' + r.first_name;
                        if (r.middle_name) name += ' ' + r.middle_name;
                        name += ' (' + (r.sex ? r.sex.charAt(0).toUpperCase() + r.sex.slice(1) : 'N/A') + ')';
                        if (!r.resident_sitio) name += ' [Unassigned]';
                        opts += '<option value="' + r.id + '">' + name + '</option>';
                    });
                    headSelect.html(opts).prop('disabled', false);
                } else {
                    headSelect.html('<option value="">— No residents found —</option>').prop('disabled', true);
                    $('#noResidentsAlert').show();
                }
            },
            error: function () {
                $('#loadingAlert').hide();
                headSelect.html('<option value="">— Request failed —</option>').prop('disabled', true);
                alert('Failed to load residents. Please try again.');
            }
        });
    });

    $('#householdForm').on('submit', function (e) {
        var hhNo  = $('input[name="household_no"]').val().trim();
        var sitio = $('#sitioSelect').val();
        
        if (!hhNo)  { 
            alert('Please enter household number.'); 
            e.preventDefault(); 
            return false; 
        }
        if (!sitio) { 
            alert('Please select Purok/Sitio.');     
            e.preventDefault(); 
            return false; 
        }
        
        $('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Saving…').prop('disabled', true);
    });
});
</script>

<?= $this->endSection() ?>