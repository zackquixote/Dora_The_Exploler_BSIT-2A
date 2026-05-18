<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/advanced-features.css') ?>">

<div class="bmis-content af-container">
    <div style="display:flex;align-items:center;margin-bottom:24px">
        <div style="width:56px;height:56px;border-radius:16px;background:rgba(79,70,229,0.12);color:var(--c-blue);display:flex;align-items:center;justify-content:center;font-size:24px;margin-right:18px">
            <i class="fas fa-paper-plane"></i>
        </div>
        <div>
            <h1 class="ds-page-title" style="margin:0;font-size:28px;font-weight:800;color:var(--ink)">Send Broadcast</h1>
            <p style="font-size:14px;color:var(--ink-muted);margin-top:2px">Send SMS or Email broadcasts directly to your residents</p>
        </div>
    </div>
    
    <div class="af-card" style="max-width: 800px">
        <div class="af-card-header">
            <div class="ds-card-title"><i class="fas fa-bullhorn"></i> New Message</div>
        </div>
        <div class="af-card-body">
            <form action="<?= base_url('advanced/send-notification') ?>" method="POST">
                
                <div class="ds-grid-2">
                    <div class="af-form-group">
                        <label class="af-label">Recipients</label>
                        <select name="recipients" class="af-input" style="appearance:none">
                            <option value="all">All Active Residents</option>
                        </select>
                        <i class="fas fa-users af-input-icon"></i>
                    </div>
                    
                    <div class="af-form-group">
                        <label class="af-label">Notification Type</label>
                        <input type="text" name="type" class="af-input has-icon" value="announcement">
                        <i class="fas fa-tag af-input-icon"></i>
                    </div>
                </div>

                <div class="af-form-group">
                    <label class="af-label">Message Title</label>
                    <input type="text" name="title" class="af-input has-icon" required placeholder="e.g. Typhoon Warning">
                    <i class="fas fa-heading af-input-icon"></i>
                </div>

                <div class="af-form-group">
                    <label class="af-label">Message Content</label>
                    <textarea name="message" class="af-input" rows="5" required placeholder="Type your broadcast message here..."></textarea>
                </div>

                <div class="af-form-group">
                    <label class="af-label">Delivery Channels</label>
                    <div class="af-checkbox-group">
                        <label class="af-checkbox-label">
                            <input type="checkbox" name="channels[]" value="sms" checked> 
                            <i class="fas fa-sms" style="color:var(--c-blue)"></i> SMS
                        </label>
                        <label class="af-checkbox-label">
                            <input type="checkbox" name="channels[]" value="email"> 
                            <i class="fas fa-envelope" style="color:var(--c-amber)"></i> Email
                        </label>
                    </div>
                </div>

                <div class="af-form-group" style="margin-top: 32px">
                    <button type="submit" class="af-btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Broadcast
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
