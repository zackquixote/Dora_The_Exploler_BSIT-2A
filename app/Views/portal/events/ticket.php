<?= $this->extend('portal/layout') ?>
<?= $this->section('content') ?>

<div class="af-container" style="max-width: 500px; margin: 0 auto; padding: 24px 16px;">
    <div style="text-align:center;margin-bottom:24px">
        <a href="<?= base_url('portal/events') ?>" style="font-size:13px;color:var(--ink-muted);text-decoration:none;display:inline-flex;align-items:center;gap:6px"><i class="fas fa-arrow-left"></i> Back to Events</a>
    </div>

    <style>
        .ticket-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        @media (max-width: 400px) {
            .ticket-grid { grid-template-columns: 1fr; gap: 8px; }
        }
    </style>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:#d1fae5;color:#065f46;padding:12px 18px;border-radius:12px;margin-bottom:20px;font-weight:600;font-size:14px;text-align:center;">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Ticket Card -->
    <div style="background:white;border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.12);border:1px solid #e5e7eb">

        <!-- Ticket Top Header -->
        <div style="background:linear-gradient(135deg,#059669,#10b981,#3b82f6);padding:28px 24px;text-align:center;color:white;position:relative">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;opacity:0.8;margin-bottom:8px">Official Event Ticket</div>
            <h2 style="font-size:22px;font-weight:800;margin:0 0 8px;line-height:1.2"><?= esc($event['title']) ?></h2>
            <div style="font-size:13px;opacity:0.9"><?= esc($event['event_code']) ?></div>

            <!-- Torn edge effect -->
            <div style="position:absolute;bottom:-10px;left:0;right:0;height:20px;background:white;border-radius:100% 100% 0 0 / 20px 20px 0 0"></div>
        </div>

        <!-- Event Info -->
        <div style="padding:28px 24px 20px">
            <div class="ticket-grid">
                <div style="background:#f8fafc;border-radius:12px;padding:14px">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#64748b;letter-spacing:0.05em;margin-bottom:4px"><i class="fas fa-calendar"></i> Date</div>
                    <div style="font-size:15px;font-weight:700;color:#0f172a"><?= date('F d, Y', strtotime($event['start_date'])) ?></div>
                </div>
                <div style="background:#f8fafc;border-radius:12px;padding:14px">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#64748b;letter-spacing:0.05em;margin-bottom:4px"><i class="fas fa-clock"></i> Time</div>
                    <div style="font-size:15px;font-weight:700;color:#0f172a"><?= date('h:i A', strtotime($event['start_date'])) ?></div>
                </div>
            </div>
            <div style="background:#f8fafc;border-radius:12px;padding:14px;margin-bottom:20px">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#64748b;letter-spacing:0.05em;margin-bottom:4px"><i class="fas fa-map-marker-alt"></i> Venue</div>
                <div style="font-size:15px;font-weight:700;color:#0f172a"><?= esc($event['venue']) ?></div>
            </div>

            <!-- Registered To -->
            <div style="display:flex;align-items:center;gap:12px;background:#ecfdf5;border-radius:12px;padding:14px;margin-bottom:24px">
                <div style="width:44px;height:44px;border-radius:50%;background:#10b981;color:white;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;flex-shrink:0">
                    <?= strtoupper(substr($resident['first_name'], 0, 1)) ?>
                </div>
                <div>
                    <div style="font-size:10px;text-transform:uppercase;font-weight:700;color:#065f46;letter-spacing:0.05em">Registered Attendee</div>
                    <div style="font-size:15px;font-weight:800;color:#0f172a"><?= esc($resident['first_name'] . ' ' . $resident['last_name']) ?></div>
                    <?php if ($participant['attendance_status'] === 'attended'): ?>
                        <div style="font-size:12px;color:#059669;font-weight:600;margin-top:2px"><i class="fas fa-check-circle"></i> Checked In at <?= date('h:i A', strtotime($participant['check_in_time'])) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Dashed divider -->
            <div style="border-top:2px dashed #e2e8f0;margin:0 -24px 24px;padding:0 24px;position:relative">
                <div style="position:absolute;top:-12px;left:-12px;width:24px;height:24px;background:#f1f5f9;border-radius:50%"></div>
                <div style="position:absolute;top:-12px;right:-12px;width:24px;height:24px;background:#f1f5f9;border-radius:50%"></div>
            </div>

            <!-- QR Code -->
            <div style="text-align:center">
                <p style="font-size:13px;color:#64748b;margin-bottom:12px">Show this QR code at the event entrance</p>
                <div style="display:inline-block;background:white;border:2px solid #e2e8f0;border-radius:16px;padding:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08)">
                    <!-- Offline-ready QR Code using JavaScript -->
                    <div id="qrcode" style="width:220px;height:220px;display:block;margin:0 auto;"></div>
                </div>
                <div style="margin-top:12px;font-family:monospace;font-size:11px;color:#94a3b8;word-break:break-all;background:#f8fafc;border-radius:8px;padding:8px">
                    Ticket ID: #<?= $participant['id'] ?>
                </div>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div style="background:#f8fafc;padding:16px 24px;text-align:center;border-top:1px solid #e2e8f0">
            <p style="font-size:11px;color:#94a3b8;margin:0">This is an official digital ticket from Barangay Tabu, Ilog City.</p>
            <p style="font-size:11px;color:#94a3b8;margin:4px 0 0">Valid for one-time use. Non-transferable.</p>
        </div>
    </div>

    <!-- Print Button -->
    <div style="text-align:center;margin-top:20px">
        <button onclick="window.print()" style="background:linear-gradient(135deg,#10b981,#059669);color:white;padding:12px 32px;border-radius:12px;border:none;font-weight:700;font-size:14px;cursor:pointer;display:inline-flex;align-items:center;gap:8px">
            <i class="fas fa-print"></i> Print Ticket
        </button>
    </div>
</div>

<style>
@media print {
    .portal-nav, .portal-header, .portal-sidebar, a[href] { display: none !important; }
    body { background: white !important; }
}
</style>

<!-- Load qrcode.js for offline QR generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        new QRCode(document.getElementById("qrcode"), {
            text: "<?= $checkInUrl ?>",
            width: 220,
            height: 220,
            colorDark : "#0f172a",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    });
</script>

<?= $this->endSection() ?>
