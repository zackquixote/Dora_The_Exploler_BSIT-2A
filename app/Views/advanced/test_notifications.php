<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:rgba(16,185,129,0.12);color:#059669;display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:24px;font-weight:800;color:var(--ink)">Test Notifications</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-top:2px">Send a test SMS and/or Gmail email to confirm integrations</div>
            </div>
        </div>
    </div>

    <div class="ds-card">
        <div class="ds-card-body">
            <div class="ds-filter-header"><i class="fas fa-sliders-h"></i> Send</div>
            <form method="post" action="<?= base_url('advanced/test-notifications/send') ?>" autocomplete="off">
                <?= csrf_field() ?>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div>
                        <label class="ds-input-label">Phone (SMS)</label>
                        <input type="text" name="phone" class="ds-input" placeholder="09xxxxxxxxx or 63xxxxxxxxxx">
                    </div>
                    <div>
                        <label class="ds-input-label">Email (Gmail)</label>
                        <input type="email" name="email" class="ds-input" placeholder="you@example.com">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px">
                    <div>
                        <label class="ds-input-label">Title (Email)</label>
                        <input type="text" name="title" class="ds-input" value="Test Notification">
                    </div>
                    <div>
                        <label class="ds-input-label">Channels</label>
                        <div style="display:flex;gap:12px;align-items:center;height:38px">
                            <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink);margin:0">
                                <input type="checkbox" name="channels[]" value="sms"> SMS
                            </label>
                            <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink);margin:0">
                                <input type="checkbox" name="channels[]" value="email"> Email
                            </label>
                        </div>
                    </div>
                </div>

                <div style="margin-top:12px">
                    <label class="ds-input-label">Message</label>
                    <textarea name="message" class="ds-input" rows="4" placeholder="Type your test message..."></textarea>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px">
                    <button type="submit" class="ds-btn ds-btn-primary" style="height:36px">
                        <i class="fas fa-paper-plane"></i> Send Test
                    </button>
                </div>

                <div style="margin-top:8px;font-size:12px;color:var(--ink-muted)">
                    If Gmail API is not connected yet, Email will automatically fallback to SMTP (if SMTP env is configured).
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

