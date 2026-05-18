<?= $this->extend(session()->get('role') === 'resident' ? 'portal/layout' : 'theme/template') ?>
<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/advanced-features.css') ?>">

<div class="bmis-content af-container theme-event">
    <div style="display:flex;align-items:center;margin-bottom:24px">
        <div style="width:56px;height:56px;border-radius:16px;background:rgba(16,185,129,0.12);color:#10b981;display:flex;align-items:center;justify-content:center;font-size:24px;margin-right:18px">
            <i class="fas fa-calendar-plus"></i>
        </div>
        <div>
            <h1 class="ds-page-title" style="margin:0;font-size:28px;font-weight:800;color:var(--ink)">Create Event</h1>
            <p style="font-size:14px;color:var(--ink-muted);margin-top:2px">Schedule a new community event and manage registrations</p>
        </div>
    </div>
    
    <div class="af-card" style="max-width: 800px">
        <div class="af-card-header">
            <div class="ds-card-title"><i class="fas fa-clipboard-list"></i> Event Information</div>
        </div>
        <div class="af-card-body">
            <form action="<?= base_url('advanced/create-event') ?>" method="POST" id="eventForm">
                
                <div class="af-form-group">
                    <label class="af-label">Event Title</label>
                    <input type="text" name="title" class="af-input has-icon" required placeholder="e.g. Barangay Fiesta 2026">
                    <i class="fas fa-heading af-input-icon"></i>
                </div>

                <div class="af-form-group">
                    <label class="af-label">Description</label>
                    <textarea name="description" class="af-input" rows="3" placeholder="What is this event about?"></textarea>
                </div>

                <div class="ds-grid-2">
                    <div class="af-form-group">
                        <label class="af-label">Event Type</label>
                        <input type="text" name="event_type" class="af-input has-icon" required placeholder="e.g. Social, Seminar, Health">
                        <i class="fas fa-tags af-input-icon"></i>
                    </div>
                    <div class="af-form-group">
                        <label class="af-label">Venue</label>
                        <input type="text" name="venue" class="af-input has-icon" required placeholder="e.g. Covered Court">
                        <i class="fas fa-map-marker-alt af-input-icon"></i>
                    </div>
                </div>

                <div class="ds-grid-2">
                    <div class="af-form-group">
                        <label class="af-label">Start Date & Time</label>
                        <input type="datetime-local" name="start_date" class="af-input" required>
                    </div>
                    <div class="af-form-group">
                        <label class="af-label">End Date & Time</label>
                        <input type="datetime-local" name="end_date" class="af-input" required>
                    </div>
                </div>

                <div class="ds-grid-2">
                    <div class="af-form-group">
                        <label class="af-label">Max Participants</label>
                        <input type="number" name="max_participants" class="af-input has-icon" placeholder="0 for unlimited">
                        <i class="fas fa-users af-input-icon"></i>
                    </div>
                    <div class="af-form-group">
                        <label class="af-label">Registration Required?</label>
                        <div class="af-checkbox-group" style="margin-top:12px;">
                            <label class="af-checkbox-label">
                                <input type="checkbox" name="registration_required" value="1"> 
                                <i class="fas fa-check" style="color:var(--c-teal)"></i> Yes, Require Registration
                            </label>
                        </div>
                    </div>
                </div>

                <div class="af-form-group" style="margin-top: 32px">
                    <button type="submit" class="af-btn-primary">
                        <i class="fas fa-calendar-check"></i> Create Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.getElementById('eventForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch(e.target.action, { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
        alert('Event Created Successfully!');
        window.location.href = '<?= base_url('advanced/events') ?>';
    } else {
        alert('Error: ' + data.message);
    }
});
</script>
<?= $this->endSection() ?>
