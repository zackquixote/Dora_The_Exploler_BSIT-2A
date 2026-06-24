<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="bmis-header">
    <div class="bmis-title">
        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
        <div>
            <h2>Barangay Events</h2>
            <p>Manage community events, general assemblies, and track QR check-ins.</p>
        </div>
    </div>
    <div class="bmis-actions">
        <a href="<?= base_url('admin/events/create') ?>" class="ds-btn ds-btn-primary">
            <i class="fas fa-plus"></i> Create Event
        </a>
    </div>
</div>

<div class="ds-container">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="ds-alert ds-alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="ds-alert ds-alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="ds-card">
        <div class="ds-card-body" style="padding:0">
            <div class="ds-table-wrapper">
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Event Title</th>
                            <th>Date & Time</th>
                            <th>Venue</th>
                            <th>Status</th>
                            <th>Participants</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($events)): ?>
                            <tr><td colspan="7" style="text-align:center;padding:24px;color:var(--ink-muted)">No events found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($events as $event): ?>
                                <tr>
                                    <td><strong><?= esc($event['event_code']) ?></strong></td>
                                    <td><strong><?= esc($event['title']) ?></strong><br><span style="font-size:11px;color:var(--ink-muted)"><?= esc($event['event_type']) ?></span></td>
                                    <td>
                                        <div style="font-weight:600"><?= date('M d, Y', strtotime($event['start_date'])) ?></div>
                                        <div style="font-size:11px;color:var(--ink-muted)"><?= date('h:i A', strtotime($event['start_date'])) ?> - <?= date('h:i A', strtotime($event['end_date'])) ?></div>
                                    </td>
                                    <td><?= esc($event['venue']) ?></td>
                                    <td>
                                        <?php if($event['status'] === 'open'): ?>
                                            <span class="ds-badge ds-badge-teal">Open</span>
                                        <?php elseif($event['status'] === 'completed'): ?>
                                            <span class="ds-badge ds-badge-primary">Completed</span>
                                        <?php elseif($event['status'] === 'cancelled'): ?>
                                            <span class="ds-badge ds-badge-danger">Cancelled</span>
                                        <?php else: ?>
                                            <span class="ds-badge ds-badge-gray"><?= ucfirst($event['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span style="font-weight:700;color:var(--c-teal)"><?= $event['participant_count'] ?></span>
                                        <?php if($event['max_participants']): ?>
                                            / <?= $event['max_participants'] ?>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:right">
                                        <a href="<?= base_url('admin/events/view/' . $event['id']) ?>" class="ds-btn ds-btn-light ds-btn-sm" title="View Details & Check-in">
                                            <i class="fas fa-eye"></i> Manage
                                        </a>
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
<?= $this->endSection() ?>
