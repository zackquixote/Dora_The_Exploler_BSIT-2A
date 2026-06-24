<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="bmis-header">
    <div class="bmis-title">
        <div class="icon"><i class="fas fa-calendar-plus"></i></div>
        <div>
            <h2>Create Event</h2>
            <p>Schedule a new barangay event or assembly.</p>
        </div>
    </div>
    <div class="bmis-actions">
        <a href="<?= base_url('admin/events') ?>" class="ds-btn ds-btn-light">
            <i class="fas fa-arrow-left"></i> Back to Events
        </a>
    </div>
</div>

<div class="ds-container" style="max-width: 800px; margin: 0 auto;">
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="ds-alert ds-alert-danger">
            <ul style="margin:0;padding-left:20px">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('admin/events/store') ?>" class="ds-card">
        <?= csrf_field() ?>
        <div class="ds-card-header">
            <h3>Event Details</h3>
        </div>
        <div class="ds-card-body">
            <div class="ds-form-group">
                <label class="ds-form-label">Event Title <span style="color:var(--c-rose)">*</span></label>
                <input type="text" name="title" class="ds-input" value="<?= old('title') ?>" required placeholder="e.g. Barangay General Assembly 2026">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
                <div class="ds-form-group" style="margin:0">
                    <label class="ds-form-label">Event Type <span style="color:var(--c-rose)">*</span></label>
                    <select name="event_type" class="ds-select" required>
                        <option value="">Select Type...</option>
                        <option value="General Assembly" <?= old('event_type') == 'General Assembly' ? 'selected' : '' ?>>General Assembly</option>
                        <option value="Health Mission" <?= old('event_type') == 'Health Mission' ? 'selected' : '' ?>>Health Mission</option>
                        <option value="Sports League" <?= old('event_type') == 'Sports League' ? 'selected' : '' ?>>Sports League</option>
                        <option value="Community Cleanup" <?= old('event_type') == 'Community Cleanup' ? 'selected' : '' ?>>Community Cleanup</option>
                        <option value="Other" <?= old('event_type') == 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="ds-form-group" style="margin:0">
                    <label class="ds-form-label">Venue <span style="color:var(--c-rose)">*</span></label>
                    <input type="text" name="venue" class="ds-input" value="<?= old('venue') ?>" required placeholder="e.g. Barangay Covered Court">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
                <div class="ds-form-group" style="margin:0">
                    <label class="ds-form-label">Start Date & Time <span style="color:var(--c-rose)">*</span></label>
                    <input type="datetime-local" name="start_date" class="ds-input" value="<?= old('start_date') ?>" required>
                </div>
                <div class="ds-form-group" style="margin:0">
                    <label class="ds-form-label">End Date & Time <span style="color:var(--c-rose)">*</span></label>
                    <input type="datetime-local" name="end_date" class="ds-input" value="<?= old('end_date') ?>" required>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
                <div class="ds-form-group" style="margin:0">
                    <label class="ds-form-label">Maximum Participants</label>
                    <input type="number" name="max_participants" class="ds-input" value="<?= old('max_participants') ?>" placeholder="Leave blank for unlimited" min="1">
                </div>
                <div class="ds-form-group" style="margin:0">
                    <label class="ds-form-label">Registration Deadline</label>
                    <input type="datetime-local" name="registration_deadline" class="ds-input" value="<?= old('registration_deadline') ?>">
                </div>
            </div>

            <div class="ds-form-group">
                <label class="ds-form-label">Description <span style="color:var(--c-rose)">*</span></label>
                <textarea name="description" class="ds-input" rows="4" required placeholder="Describe the event, agenda, or requirements..."><?= old('description') ?></textarea>
            </div>
        </div>
        <div class="ds-card-footer" style="display:flex;justify-content:flex-end;gap:10px">
            <a href="<?= base_url('admin/events') ?>" class="ds-btn ds-btn-light">Cancel</a>
            <button type="submit" class="ds-btn ds-btn-primary">Create Event</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
