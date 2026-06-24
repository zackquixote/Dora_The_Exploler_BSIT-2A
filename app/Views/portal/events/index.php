<?= $this->extend('portal/layout') ?>
<?= $this->section('content') ?>

<div class="af-container">
    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:#d1fae5;color:#065f46;padding:12px 18px;border-radius:12px;margin-bottom:20px;font-weight:600;font-size:14px;display:flex;align-items:center;gap:10px;">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:#fee2e2;color:#991b1b;padding:12px 18px;border-radius:12px;margin-bottom:20px;font-weight:600;font-size:14px;display:flex;align-items:center;gap:10px;">
            <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div style="margin-bottom:28px">
        <h2 style="font-weight:800;font-size:28px;color:var(--ink);margin:0">Barangay Events 🎉</h2>
        <p style="color:var(--ink-muted);margin-top:6px">Browse upcoming events and register with your QR ticket.</p>
    </div>

    <!-- Upcoming Events -->
    <?php if (empty($events)): ?>
        <div class="af-card" style="text-align:center;padding:48px">
            <i class="fas fa-calendar-times" style="font-size:40px;color:var(--ink-muted);margin-bottom:16px"></i>
            <h3 style="color:var(--ink-muted)">No upcoming events</h3>
            <p style="color:var(--ink-soft);font-size:14px">Check back soon for new barangay activities!</p>
        </div>
    <?php else: ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:20px;margin-bottom:40px">
            <?php foreach ($events as $event): ?>
                <div class="af-card" style="position:relative;overflow:hidden">
                    <!-- Color accent bar -->
                    <div style="height:5px;background:linear-gradient(90deg,#10b981,#3b82f6);border-radius:12px 12px 0 0;margin:-1px -1px 0 -1px"></div>

                    <div class="af-card-body" style="padding:24px">
                        <!-- Date badge -->
                        <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:16px">
                            <div style="min-width:56px;text-align:center;background:#ecfdf5;border-radius:12px;padding:8px 4px">
                                <div style="font-size:20px;font-weight:800;color:#10b981;line-height:1"><?= date('d', strtotime($event['start_date'])) ?></div>
                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#065f46"><?= date('M', strtotime($event['start_date'])) ?></div>
                            </div>
                            <div>
                                <span style="font-size:11px;background:#dbeafe;color:#1d4ed8;padding:3px 8px;border-radius:20px;font-weight:600"><?= esc($event['event_type']) ?></span>
                                <h3 style="font-size:17px;font-weight:800;color:var(--ink);margin:6px 0 2px"><?= esc($event['title']) ?></h3>
                            </div>
                        </div>

                        <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:16px">
                            <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-muted)">
                                <i class="fas fa-clock" style="width:14px;color:#10b981"></i>
                                <?= date('h:i A', strtotime($event['start_date'])) ?> - <?= date('h:i A', strtotime($event['end_date'])) ?>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-muted)">
                                <i class="fas fa-map-marker-alt" style="width:14px;color:#ef4444"></i>
                                <?= esc($event['venue']) ?>
                            </div>
                            <?php if ($event['max_participants']): ?>
                            <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-muted)">
                                <i class="fas fa-users" style="width:14px;color:#6366f1"></i>
                                <?= $event['slots_taken'] ?> / <?= $event['max_participants'] ?> registered
                                <?php if ($event['slots_full']): ?>
                                    <span style="background:#fee2e2;color:#b91c1c;padding:2px 7px;border-radius:20px;font-size:11px;font-weight:700">FULL</span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($event['is_registered']): ?>
                            <div style="display:flex;gap:8px">
                                <a href="<?= base_url('portal/events/ticket/' . $event['registration']['id']) ?>"
                                   style="flex:1;text-align:center;padding:10px;border-radius:10px;background:linear-gradient(135deg,#10b981,#059669);color:white;font-weight:700;font-size:14px;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:6px">
                                    <i class="fas fa-qrcode"></i> View My Ticket
                                </a>
                                <form method="post" action="<?= base_url('portal/events/cancel/' . $event['registration']['id']) ?>" style="margin:0" onsubmit="return confirm('Cancel your registration for this event?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" style="padding:10px 14px;border-radius:10px;border:1px solid #e5e7eb;background:white;color:var(--ink-muted);cursor:pointer;font-size:14px" title="Cancel Registration">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        <?php elseif ($event['slots_full']): ?>
                            <button disabled style="width:100%;padding:10px;border-radius:10px;border:none;background:#e5e7eb;color:var(--ink-muted);font-weight:700;font-size:14px;cursor:not-allowed">
                                Event Full
                            </button>
                        <?php else: ?>
                            <form method="post" action="<?= base_url('portal/events/register/' . $event['id']) ?>" style="margin:0">
                                <?= csrf_field() ?>
                                <button type="submit" style="width:100%;padding:10px;border-radius:10px;border:none;background:linear-gradient(135deg,#10b981,#059669);color:white;font-weight:700;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;transition:opacity .2s" onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                                    <i class="fas fa-ticket-alt"></i> Register & Get Ticket
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- My Events History -->
    <?php if (!empty($myEvents)): ?>
    <h3 class="ds-section-label" style="margin-bottom:16px">My Registrations</h3>
    <div class="af-card">
        <div class="af-card-body" style="padding:0">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:var(--bg)">
                        <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;color:var(--ink-soft)">Event</th>
                        <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;color:var(--ink-soft)">Date</th>
                        <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;color:var(--ink-soft)">Attendance</th>
                        <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;text-transform:uppercase;color:var(--ink-soft)">Ticket</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myEvents as $my): ?>
                    <tr style="border-top:1px solid var(--border)">
                        <td style="padding:12px 16px;font-weight:600;color:var(--ink)"><?= esc($my['title']) ?></td>
                        <td style="padding:12px 16px;color:var(--ink-muted);font-size:13px"><?= date('M d, Y', strtotime($my['start_date'])) ?></td>
                        <td style="padding:12px 16px">
                            <?php if ($my['attendance_status'] === 'attended'): ?>
                                <span style="background:#d1fae5;color:#065f46;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700"><i class="fas fa-check"></i> Attended</span>
                            <?php elseif ($my['attendance_status'] === 'cancelled'): ?>
                                <span style="background:#fee2e2;color:#991b1b;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">Cancelled</span>
                            <?php else: ?>
                                <span style="background:#e0f2fe;color:#0369a1;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">Registered</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:12px 16px;text-align:right">
                            <?php if ($my['attendance_status'] === 'registered'): ?>
                                <a href="<?= base_url('portal/events/ticket/' . $my['id']) ?>" style="background:#10b981;color:white;padding:5px 12px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none">
                                    <i class="fas fa-qrcode"></i> Ticket
                                </a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
