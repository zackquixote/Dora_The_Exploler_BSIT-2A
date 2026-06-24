<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .metric-card {
            transition: transform 0.2s;
        }
        .metric-card:hover {
            transform: translateY(-2px);
        }
        .progress-bar {
            transition: width 0.3s ease;
        }
        .status-good { color: #28a745; }
        .status-warning { color: #ffc107; }
        .status-danger { color: #dc3545; }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-3">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    System Performance Monitor
                </h1>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" onclick="refreshMetrics()">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                    <button class="btn btn-warning" onclick="clearCache()">
                        <i class="fas fa-broom me-1"></i> Clear Cache
                    </button>
                    <button class="btn btn-info" onclick="optimizeDatabase()">
                        <i class="fas fa-database me-1"></i> Optimize DB
                    </button>
                </div>
            </div>
        </div>

        <!-- Performance Metrics Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card metric-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-memory fa-2x text-primary mb-2"></i>
                        <h5>Memory Usage</h5>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-primary" style="width: <?= $performance_metrics['memory_usage_percent'] ?>%"></div>
                        </div>
                        <small class="text-muted"><?= $system_info['current_memory_usage'] ?> / <?= $system_info['memory_limit'] ?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card metric-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-hdd fa-2x text-success mb-2"></i>
                        <h5>Disk Usage</h5>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-success" style="width: <?= $performance_metrics['disk_usage']['usage_percent'] ?>%"></div>
                        </div>
                        <small class="text-muted"><?= $performance_metrics['disk_usage']['used'] ?> / <?= $performance_metrics['disk_usage']['total'] ?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card metric-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-database fa-2x text-info mb-2"></i>
                        <h5>DB Query Time</h5>
                        <h3 class="<?= $performance_metrics['database_query_time'] > 100 ? 'status-danger' : ($performance_metrics['database_query_time'] > 50 ? 'status-warning' : 'status-good') ?>">
                            <?= $performance_metrics['database_query_time'] ?>ms
                        </h3>
                        <small class="text-muted">Response Time</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card metric-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                        <h5>Page Load</h5>
                        <h3 class="<?= $performance_metrics['page_load_time'] > 1000 ? 'status-danger' : ($performance_metrics['page_load_time'] > 500 ? 'status-warning' : 'status-good') ?>">
                            <?= $performance_metrics['page_load_time'] ?>ms
                        </h3>
                        <small class="text-muted">Total Time</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-server me-2"></i>System Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Operating System:</strong></td>
                                <td><?= $system_info['os'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>PHP Version:</strong></td>
                                <td><?= $system_info['php_version'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>Server Software:</strong></td>
                                <td><?= $system_info['server_software'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>Memory Limit:</strong></td>
                                <td><?= $system_info['memory_limit'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>Max Execution Time:</strong></td>
                                <td><?= $system_info['max_execution_time'] ?>s</td>
                            </tr>
                            <tr>
                                <td><strong>Upload Max Size:</strong></td>
                                <td><?= $system_info['upload_max_filesize'] ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-database me-2"></i>Database Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Database Version:</strong></td>
                                <td><?= $database_info['version'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>Database Name:</strong></td>
                                <td><?= $database_info['database_name'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>Database Size:</strong></td>
                                <td><?= $database_info['size_mb'] ?> MB</td>
                            </tr>
                            <tr>
                                <td><strong>Table Count:</strong></td>
                                <td><?= $database_info['table_count'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>Active Connections:</strong></td>
                                <td><?= $database_info['active_connections'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>Connection Status:</strong></td>
                                <td>
                                    <span class="badge bg-<?= strpos($database_info['connection_status'], 'Error') !== false ? 'danger' : 'success' ?>">
                                        <?= $database_info['connection_status'] ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Recommendations -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Performance Recommendations</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="text-primary">System Level</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Close unnecessary applications</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Restart computer periodically</li>
                                    <li><i class="fas fa-exclamation text-warning me-2"></i>Monitor RAM usage (currently <?= $performance_metrics['memory_usage_percent'] ?>%)</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-primary">Application Level</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-<?= $php_info['opcache_enabled'] ? 'check text-success' : 'times text-danger' ?> me-2"></i>OPcache <?= $php_info['opcache_enabled'] ? 'Enabled' : 'Disabled' ?></li>
                                    <li><i class="fas fa-check text-success me-2"></i>Lazy loading implemented</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Database caching enabled</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-primary">Database Level</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-info text-info me-2"></i>Run optimization script</li>
                                    <li><i class="fas fa-info text-info me-2"></i>Add database indexes</li>
                                    <li><i class="fas fa-info text-info me-2"></i>Monitor slow queries</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function refreshMetrics() {
            location.reload();
        }

        function clearCache() {
            if (confirm('Are you sure you want to clear the cache?')) {
                fetch('/system-monitor/clear-cache', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
            }
        }

        function optimizeDatabase() {
            if (confirm('Are you sure you want to optimize the database? This may take a few minutes.')) {
                fetch('/system-monitor/optimize-database', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
            }
        }

        // Auto-refresh every 30 seconds
        setInterval(function() {
            fetch('/system-monitor/get-metrics')
                .then(response => response.json())
                .then(data => {
                    // Update memory usage display
                    const memoryPercent = (data.memory_usage / data.memory_limit) * 100;
                    document.querySelector('.progress-bar').style.width = memoryPercent + '%';
                })
                .catch(error => console.error('Error updating metrics:', error));
        }, 30000);
    </script>
</body>
</html>