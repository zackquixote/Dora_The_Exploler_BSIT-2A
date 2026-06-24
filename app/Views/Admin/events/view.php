<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<style>
.event-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
.stat-box { background: var(--bg); padding: 16px; border-radius: var(--r-sm); border: 1px solid var(--border); text-align: center; }
.stat-val { font-size: 24px; font-weight: 800; color: var(--ink); line-height: 1; margin-bottom: 4px; }
.stat-lbl { font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--ink-soft); letter-spacing: 0.05em; }

/* Scanner UI */
#qr-reader { width: 100%; border: 2px dashed var(--border); border-radius: var(--r-md); overflow: hidden; background: #000; }
#qr-reader img { margin: 0 auto; }
#scanner-result { margin-top: 16px; padding: 16px; border-radius: var(--r-sm); display: none; }
</style>

<div class="bmis-header">
    <div class="bmis-title">
        <div class="icon"><i class="fas fa-calendar-check"></i></div>
        <div>
            <h2><?= esc($event['title']) ?></h2>
            <p>Manage participants and scan QR tickets at the door.</p>
        </div>
    </div>
    <div class="bmis-actions">
        <a href="<?= base_url('admin/events') ?>" class="ds-btn ds-btn-light">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="ds-container">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="ds-alert ds-alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 350px;gap:24px">
        <!-- Main Content (Participants) -->
        <div>
            <div class="event-stats">
                <div class="stat-box">
                    <div class="stat-val"><?= count($participants) ?></div>
                    <div class="stat-lbl">Registered</div>
                </div>
                <div class="stat-box">
                    <?php 
                    $attended = count(array_filter($participants, fn($p) => $p['attendance_status'] === 'attended'));
                    ?>
                    <div class="stat-val" style="color:var(--c-teal)"><?= $attended ?></div>
                    <div class="stat-lbl">Checked In</div>
                </div>
                <div class="stat-box">
                    <div class="stat-val"><?= $event['max_participants'] ?: '∞' ?></div>
                    <div class="stat-lbl">Capacity</div>
                </div>
                <div class="stat-box">
                    <div class="stat-val" style="font-size:16px;margin-top:6px"><?= esc($event['event_code']) ?></div>
                    <div class="stat-lbl">Event Code</div>
                </div>
            </div>

            <div class="ds-card">
                <div class="ds-card-header" style="display:flex;justify-content:space-between;align-items:center">
                    <h3>Registered Residents</h3>
                    <button class="ds-btn ds-btn-light ds-btn-sm"><i class="fas fa-download"></i> Export List</button>
                </div>
                <div class="ds-card-body" style="padding:0">
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Purok</th>
                                <th>Registration Date</th>
                                <th>Status</th>
                                <th>Check-in Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($participants)): ?>
                                <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--ink-muted)">No registered participants yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($participants as $p): ?>
                                    <tr>
                                        <td><strong><?= esc($p['first_name'] . ' ' . $p['last_name']) ?></strong></td>
                                        <td><?= esc($p['sitio'] ?? '—') ?></td>
                                        <td><?= date('M d, Y h:i A', strtotime($p['registration_date'])) ?></td>
                                        <td>
                                            <?php if ($p['attendance_status'] === 'attended'): ?>
                                                <span class="ds-badge ds-badge-teal"><i class="fas fa-check"></i> Checked In</span>
                                            <?php elseif ($p['attendance_status'] === 'cancelled'): ?>
                                                <span class="ds-badge ds-badge-danger">Cancelled</span>
                                            <?php else: ?>
                                                <span class="ds-badge ds-badge-gray">Registered</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $p['check_in_time'] ? date('h:i A', strtotime($p['check_in_time'])) : '—' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar (QR Scanner) -->
        <div>
            <div class="ds-card">
                <div class="ds-card-header">
                    <h3><i class="fas fa-qrcode text-primary"></i> QR Scanner</h3>
                </div>
                <div class="ds-card-body" style="text-align:center">
                    <?php if ($event['status'] === 'open' || $event['status'] === 'ongoing'): ?>
                        <p style="font-size:12px;color:var(--ink-muted);margin-bottom:16px">Scan resident's QR ticket to check them in.</p>
                        <div id="qr-reader"></div>
                        <div id="scanner-result"></div>
                        
                        <button id="start-scan-btn" class="ds-btn ds-btn-primary" style="width:100%;justify-content:center;margin-top:16px" onclick="startScanner()">
                            <i class="fas fa-camera"></i> Start Scanner
                        </button>
                    <?php else: ?>
                        <div style="padding:24px;background:var(--bg);border-radius:var(--r-sm)">
                            <i class="fas fa-ban" style="font-size:24px;color:var(--ink-muted);margin-bottom:8px"></i>
                            <p style="margin:0;font-size:12px;color:var(--ink-soft)">Scanner is disabled. Event is <?= $event['status'] ?>.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ds-card" style="margin-top:24px">
                <div class="ds-card-header">
                    <h3>Event Details</h3>
                </div>
                <div class="ds-card-body">
                    <p><strong>Venue:</strong> <?= esc($event['venue']) ?></p>
                    <p><strong>Date:</strong> <?= date('F d, Y', strtotime($event['start_date'])) ?></p>
                    <p><strong>Time:</strong> <?= date('h:i A', strtotime($event['start_date'])) ?> to <?= date('h:i A', strtotime($event['end_date'])) ?></p>
                    <p style="margin-top:16px;font-size:13px;color:var(--ink-muted);line-height:1.5"><?= nl2br(esc($event['description'])) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let html5QrcodeScanner = null;
let isScanning = false;

function startScanner() {
    if (isScanning) return;
    
    document.getElementById('start-scan-btn').style.display = 'none';
    
    // Initialize scanner
    html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", { fps: 10, qrbox: {width: 250, height: 250} }
    );
    
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    isScanning = true;
}

function onScanSuccess(decodedText, decodedResult) {
    // The decodedText should be the URL /events/checkin/{participantId}/{token}
    // We will make an AJAX call to this URL.
    
    // Pause scanner
    if (html5QrcodeScanner) html5QrcodeScanner.pause();
    
    const resultBox = document.getElementById('scanner-result');
    resultBox.style.display = 'block';
    resultBox.style.background = 'var(--bg)';
    resultBox.style.border = '1px solid var(--border)';
    resultBox.innerHTML = '<i class="fas fa-spinner fa-spin text-primary"></i> Checking in...';

    // Verify it's a valid local URL or extract the path
    let fetchUrl = decodedText;
    if (!fetchUrl.startsWith('http') && !fetchUrl.startsWith('/')) {
        fetchUrl = '<?= base_url() ?>/' + fetchUrl; // fallback
    }

    fetch(fetchUrl)
        .then(res => res.text())
        .then(html => {
            // The endpoint returns HTML (events/checkin_result.php). We can extract the message.
            // Or just dump the HTML if it's small, or parse it.
            // For now, let's just dump the HTML inside the result box.
            resultBox.innerHTML = html;
            
            // Resume scanning after 3 seconds
            setTimeout(() => {
                resultBox.style.display = 'none';
                if (html5QrcodeScanner) html5QrcodeScanner.resume();
            }, 3000);
            
            // Reload page after 3.5s to update the participants table
            setTimeout(() => {
                window.location.reload();
            }, 3500);
        })
        .catch(err => {
            resultBox.style.background = 'var(--c-rose-bg)';
            resultBox.style.color = 'var(--c-rose)';
            resultBox.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Network error connecting to check-in endpoint.';
            setTimeout(() => {
                resultBox.style.display = 'none';
                if (html5QrcodeScanner) html5QrcodeScanner.resume();
            }, 3000);
        });
}

function onScanFailure(error) {
    // handle scan failure, usually better to ignore and keep scanning
}
</script>
<?= $this->endSection() ?>
