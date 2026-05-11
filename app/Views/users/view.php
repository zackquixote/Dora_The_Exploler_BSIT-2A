<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">
    <!-- Page Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:16px">
            <a href="<?= base_url('admin/users') ?>" class="ds-action-btn ab-blue" style="width:40px;height:40px" title="Back to Users"><i class="fas fa-arrow-left"></i></a>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:22px">User Profile</div>
                <div style="font-size:12px;color:var(--ink-muted);margin-top:2px">Detailed view of account information</div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:320px 1fr;gap:24px;align-items:start">
        
        <!-- LEFT COLUMN: Profile Card -->
        <div class="ds-card" style="overflow:hidden;border:none;box-shadow:0 10px 30px rgba(0,0,0,0.05)">
            <!-- Gradient Header -->
            <div style="height:120px;background:linear-gradient(135deg, var(--c-blue) 0%, #1e3a8a 100%);position:relative">
                <svg width="100%" height="100%" style="position:absolute;top:0;left:0;opacity:0.1" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="2" cy="2" r="2" fill="#fff"/></pattern></defs><rect width="100%" height="100%" fill="url(#dots)"/></svg>
            </div>
            
            <div style="padding:0 24px 24px;text-align:center;margin-top:-50px;position:relative;z-index:2">
                <!-- Avatar -->
                <div style="width:100px;height:100px;border-radius:50%;background:var(--white);padding:6px;margin:0 auto 16px;box-shadow:0 8px 24px rgba(0,0,0,0.1)">
                    <div style="width:100%;height:100%;border-radius:50%;background:var(--c-blue-bg);color:var(--c-blue);display:flex;align-items:center;justify-content:center;font-size:36px">
                        <i class="fas fa-user-astronaut"></i>
                    </div>
                </div>
                
                <h2 class="font-serif" style="margin:0 0 4px;font-size:24px;font-weight:800;color:var(--ink);letter-spacing:-0.01em;"><?= esc($user['name']) ?></h2>
                <div style="font-size:13px;color:var(--ink-muted);margin-bottom:16px"><i class="fas fa-envelope" style="margin-right:6px"></i><?= esc($user['email']) ?></div>
                
                <div style="display:flex;justify-content:center;gap:8px;margin-bottom:24px">
                    <span class="ds-badge <?= $user['role'] == 'admin' ? 'ds-badge-violet' : 'ds-badge-blue' ?>" style="padding:6px 12px;font-size:11px"><i class="fas fa-shield-alt" style="margin-right:6px"></i> <?= ucfirst(esc($user['role'])) ?></span>
                    <span class="ds-badge <?= $user['status'] == 'active' ? 'ds-badge-green' : 'ds-badge-gray' ?>" style="padding:6px 12px;font-size:11px"><i class="fas fa-power-off" style="margin-right:6px"></i> <?= ucfirst(esc($user['status'])) ?></span>
                </div>
                
                <div style="padding-top:20px;border-top:1px dashed var(--border);text-align:left">
                    <div style="font-size:10px;font-weight:800;text-transform:uppercase;color:var(--ink-muted);letter-spacing:.05em;margin-bottom:12px">Quick Actions</div>
                    <a href="<?= base_url('admin/users') ?>" class="ds-btn ds-btn-blue" style="width:100%;justify-content:center"><i class="fas fa-edit"></i> Edit from Directory</a>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Details & Meta -->
        <div style="display:flex;flex-direction:column;gap:24px">
            
            <!-- Contact & Security -->
            <div class="ds-card">
                <div class="ds-card-head" style="border-bottom:1px solid var(--border)">
                    <div class="ds-card-title"><i class="fas fa-id-card"></i> Account Details</div>
                </div>
                <div class="ds-card-body p0">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0">
                        <?php foreach([
                            ['Full Name', $user['name'], 'fa-user', 'blue'],
                            ['Email Address', $user['email'], 'fa-envelope-open', 'amber'],
                            ['Phone Number', $user['phone'] ?: 'Not specified', 'fa-phone-alt', 'green'],
                            ['Access Level', ucfirst(esc($user['role'])), 'fa-key', 'violet']
                        ] as $index => $item): ?>
                        <div style="padding:20px 24px;border-bottom:1px solid var(--border);<?= $index % 2 == 0 ? 'border-right:1px solid var(--border)' : '' ?>">
                            <div style="display:flex;align-items:flex-start;gap:16px">
                                <div style="width:40px;height:40px;border-radius:10px;background:var(--c-<?= $item[3] ?>-bg);color:var(--c-<?= $item[3] ?>);display:flex;align-items:center;justify-content:center;font-size:16px">
                                    <i class="fas <?= $item[2] ?>"></i>
                                </div>
                                <div>
                                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--ink-soft);letter-spacing:.03em;margin-bottom:4px"><?= $item[0] ?></div>
                                    <div style="font-size:14px;font-weight:600;color:var(--ink)"><?= esc($item[1]) ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Timestamps -->
            <div class="ds-card" style="background:var(--white);border:1px solid var(--border);box-shadow:none">
                <div class="ds-card-body" style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px">
                    <div style="display:flex;align-items:center;gap:12px">
                        <div style="width:32px;height:32px;border-radius:50%;background:var(--bg);color:var(--ink-soft);display:flex;align-items:center;justify-content:center"><i class="fas fa-calendar-plus"></i></div>
                        <div>
                            <div style="font-size:11px;color:var(--ink-soft);font-weight:600">Account Created</div>
                            <div style="font-size:13px;font-weight:700;color:var(--ink)"><?= date('F d, Y h:i A', strtotime($user['created_at'] ?? 'now')) ?></div>
                        </div>
                    </div>
                    <div style="width:1px;height:30px;background:var(--border)"></div>
                    <div style="display:flex;align-items:center;gap:12px">
                        <div style="width:32px;height:32px;border-radius:50%;background:var(--bg);color:var(--ink-soft);display:flex;align-items:center;justify-content:center"><i class="fas fa-history"></i></div>
                        <div>
                            <div style="font-size:11px;color:var(--ink-soft);font-weight:600">Last Updated</div>
                            <div style="font-size:13px;font-weight:700;color:var(--ink)"><?= date('F d, Y h:i A', strtotime($user['updated_at'] ?? 'now')) ?></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
