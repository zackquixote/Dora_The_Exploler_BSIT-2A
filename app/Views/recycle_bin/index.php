<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">
    <div class="tb-left mb-4">
        <h1>Recycle Bin</h1>
        <p>Restore or permanently delete soft-deleted records.</p>
    </div>

    <!-- Custom Tabs for Modern UI -->
    <ul class="nav nav-tabs mb-4" id="recycleBinTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="residents-tab" data-toggle="tab" href="#residents" role="tab" aria-controls="residents" aria-selected="true">
                <i class="fas fa-users mr-2"></i> Residents
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="households-tab" data-toggle="tab" href="#households" role="tab" aria-controls="households" aria-selected="false">
                <i class="fas fa-home mr-2"></i> Households
            </a>
        </li>
    </ul>

    <div class="tab-content" id="recycleBinTabsContent">
        <!-- Residents Tab -->
        <div class="tab-pane fade show active" id="residents" role="tabpanel" aria-labelledby="residents-tab">
            <div class="ds-card">
                <div class="ds-card-head">
                    <div class="ds-card-title"><i class="fas fa-trash-alt"></i> Deleted Residents</div>
                </div>
                <div class="ds-card-body p0">
                    <div class="table-responsive">
                        <table class="ds-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Deleted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($deletedResidents)): ?>
                                    <tr><td colspan="3" class="text-center">No deleted residents found.</td></tr>
                                <?php else: ?>
                                    <?php foreach($deletedResidents as $r): ?>
                                        <tr>
                                            <td><strong><?= esc($r['first_name'] . ' ' . $r['last_name']) ?></strong></td>
                                            <td><span class="mono"><?= esc($r['deleted_at']) ?></span></td>
                                            <td>
                                                <form action="<?= base_url("recyclebin/restoreResident/{$r['id']}") ?>" method="POST" style="display:inline;" data-confirm="Restore this resident?">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="ds-badge ds-badge-teal border-0"><i class="fas fa-undo"></i> Restore</button>
                                                </form>
                                                <form action="<?= base_url("recyclebin/forceDeleteResident/{$r['id']}") ?>" method="POST" style="display:inline;" data-confirm="Permanently delete this resident? This cannot be undone.">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="ds-badge ds-badge-rose border-0"><i class="fas fa-times"></i> Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Households Tab -->
        <div class="tab-pane fade" id="households" role="tabpanel" aria-labelledby="households-tab">
            <div class="ds-card">
                <div class="ds-card-head">
                    <div class="ds-card-title"><i class="fas fa-trash-alt"></i> Deleted Households</div>
                </div>
                <div class="ds-card-body p0">
                    <div class="table-responsive">
                        <table class="ds-table">
                            <thead>
                                <tr>
                                    <th>Household No.</th>
                                    <th>Deleted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($deletedHouseholds)): ?>
                                    <tr><td colspan="3" class="text-center">No deleted households found.</td></tr>
                                <?php else: ?>
                                    <?php foreach($deletedHouseholds as $h): ?>
                                        <tr>
                                            <td><strong><?= esc($h['household_no']) ?></strong></td>
                                            <td><span class="mono"><?= esc($h['deleted_at']) ?></span></td>
                                            <td>
                                                <form action="<?= base_url("recyclebin/restoreHousehold/{$h['id']}") ?>" method="POST" style="display:inline;" data-confirm="Restore this household?">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="ds-badge ds-badge-teal border-0"><i class="fas fa-undo"></i> Restore</button>
                                                </form>
                                                <form action="<?= base_url("recyclebin/forceDeleteHousehold/{$h['id']}") ?>" method="POST" style="display:inline;" data-confirm="Permanently delete this household? This cannot be undone.">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="ds-badge ds-badge-rose border-0"><i class="fas fa-times"></i> Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
