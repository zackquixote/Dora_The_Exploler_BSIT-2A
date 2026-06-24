<?= $this->extend('portal/layout') ?>
<?= $this->section('content') ?>

<div class="af-container">
    <div class="af-header">
        <h1 class="af-title">Barangay Facilities</h1>
        <p class="af-subtitle">Reserve venues, vehicles, or equipment for your events and needs.</p>
    </div>

    <div class="row" style="margin-top: 30px;">
        <!-- Left Column: Booking Form -->
        <div class="col-md-5">
            <div class="af-card" style="padding: 24px; position: sticky; top: 20px;">
                <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--c-navy); margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                    <i class="fas fa-calendar-plus" style="color: var(--c-blue); margin-right: 8px;"></i> Request a Booking
                </h3>

                <form action="<?= base_url('portal/facilities/book') ?>" method="POST" id="bookingForm" aria-label="Facility Booking Form">
                    <?= csrf_field() ?>
                    
                    <div class="af-form-group">
                        <label for="facility_id" class="af-label">Select Facility *</label>
                        <select id="facility_id" name="facility_id" class="af-input" required>
                            <option value="">Choose a facility...</option>
                            <?php foreach ($facilities as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= esc($f['name']) ?> (<?= esc($f['type']) ?>)</option>
                            <?php endforeach; ?>
                            <?php if (empty($facilities)): ?>
                                <option value="" disabled>No facilities available</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="row g-2" style="margin-top: 15px;">
                        <div class="col-12 col-sm-6">
                            <div class="af-form-group">
                                <label for="start_datetime" class="af-label">Start Time *</label>
                                <input type="datetime-local" id="start_datetime" name="start_datetime" class="af-input" required min="<?= date('Y-m-d\TH:i') ?>">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="af-form-group">
                                <label for="end_datetime" class="af-label">End Time *</label>
                                <input type="datetime-local" id="end_datetime" name="end_datetime" class="af-input" required min="<?= date('Y-m-d\TH:i') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="af-form-group" style="margin-top: 15px;">
                        <label for="purpose" class="af-label">Purpose of Booking *</label>
                        <textarea id="purpose" name="purpose" class="af-input" rows="3" placeholder="Why do you need to reserve this facility?" required></textarea>
                    </div>

                    <button type="submit" class="af-btn" style="width: 100%; margin-top: 20px;" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Submit Request
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: Availability Calendar & My Bookings -->
        <div class="col-md-7">
            <!-- Facility Availability Calendar -->
            <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--c-navy); margin-bottom: 20px;">
                <i class="fas fa-calendar-alt" style="color: var(--c-blue); margin-right: 8px;"></i> Availability Calendar
            </h3>
            <div class="af-card" style="padding: 20px; margin-bottom: 30px;">
                <div id="calendar"></div>
            </div>

            <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--c-navy); margin-bottom: 20px;">
                <i class="fas fa-history" style="color: var(--c-blue); margin-right: 8px;"></i> My Bookings
            </h3>

            <?php if (empty($my_bookings)): ?>
                <div class="af-card" style="text-align: center; padding: 40px; color: var(--c-gray);">
                    <i class="fas fa-calendar-times" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
                    <p style="font-weight: 600;">You haven't requested any facilities yet.</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <?php foreach ($my_bookings as $booking): ?>
                        <div class="af-card" style="padding: 20px; display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <h4 style="font-weight: 800; font-size: 16px; margin: 0; color: var(--c-navy);">
                                    <?= esc($booking['facility_name']) ?>
                                    <span style="font-size: 11px; font-weight: 600; background: #e2e8f0; color: var(--c-gray); padding: 2px 8px; border-radius: 12px; margin-left: 8px;">
                                        <?= esc($booking['facility_type']) ?>
                                    </span>
                                </h4>
                                <div style="font-size: 13px; color: var(--c-gray); margin-top: 8px;">
                                    <i class="fas fa-clock" style="width: 16px;"></i> 
                                    <?= date('M d, Y h:i A', strtotime($booking['start_datetime'])) ?> - <?= date('h:i A', strtotime($booking['end_datetime'])) ?>
                                </div>
                                <div style="font-size: 13px; color: var(--c-gray); margin-top: 4px;">
                                    <i class="fas fa-info-circle" style="width: 16px;"></i> <?= esc($booking['purpose']) ?>
                                </div>
                                <?php if ($booking['remarks']): ?>
                                    <div style="font-size: 12px; color: #ef4444; margin-top: 8px; background: #fef2f2; padding: 6px 10px; border-radius: 6px;">
                                        <strong>Admin Note:</strong> <?= esc($booking['remarks']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div style="text-align: right;">
                                <?php 
                                    $badgeClass = 'bg-secondary';
                                    if ($booking['status'] == 'Approved') $badgeClass = 'bg-success';
                                    if ($booking['status'] == 'Pending') $badgeClass = 'bg-warning text-dark';
                                    if ($booking['status'] == 'Rejected' || $booking['status'] == 'Cancelled') $badgeClass = 'bg-danger';
                                ?>
                                <span class="badge <?= $badgeClass ?>" style="padding: 6px 12px; border-radius: 20px; font-weight: 600;">
                                    <?= esc($booking['status']) ?>
                                </span>
                                <?php if ($booking['status'] === 'Pending'): ?>
                                    <form action="<?= base_url('portal/facilities/cancel/' . $booking['id']) ?>" method="POST" style="margin-top: 10px;" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" style="background: none; border: 1px solid #ef4444; color: #ef4444; padding: 4px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.getElementById('bookingForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        btn.style.opacity = '0.7';
        btn.style.pointerEvents = 'none';
    });
</script>

<!-- FullCalendar Integration -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            height: 'auto',
            events: '<?= base_url("portal/facilities/calendar-data") ?>',
            eventClick: function(info) {
                alert('Facility: ' + info.event.title + '\nFrom: ' + info.event.start.toLocaleString() + (info.event.end ? '\nTo: ' + info.event.end.toLocaleString() : ''));
            }
        });
        calendar.render();
    });
</script>

<?= $this->endSection() ?>
