<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">
                <i class="bi bi-shield-lock me-2 text-brand-red"></i>
                Login Attempts Monitor
            </h1>
            <p class="text-muted mb-0">Track failed login attempts and account lockouts</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Failed Attempts</h6>
                            <h3 class="mb-0 text-danger">
                                <?php 
                                $totalAttempts = 0;
                                foreach ($data['login_attempts'] as $attempt) {
                                    $totalAttempts += $attempt['attempt_count'];
                                }
                                echo $totalAttempts;
                                ?>
                            </h3>
                        </div>
                        <i class="bi bi-x-circle fs-1 text-danger opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Locked Accounts</h6>
                            <h3 class="mb-0 text-warning">
                                <?php 
                                $lockedCount = 0;
                                foreach ($data['login_attempts'] as $attempt) {
                                    if ($attempt['is_locked']) $lockedCount++;
                                }
                                echo $lockedCount;
                                ?>
                            </h3>
                        </div>
                        <i class="bi bi-lock fs-1 text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Successful Logins</h6>
                            <h3 class="mb-0 text-success">
                                <?php 
                                $successCount = 0;
                                foreach ($data['login_logs'] as $log) {
                                    $additionalData = json_decode($log['additional_data'], true);
                                    if (isset($additionalData['success']) && $additionalData['success']) {
                                        $successCount++;
                                    }
                                }
                                echo $successCount;
                                ?>
                            </h3>
                        </div>
                        <i class="bi bi-check-circle fs-1 text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Unique IPs</h6>
                            <h3 class="mb-0 text-info">
                                <?php 
                                $uniqueIps = [];
                                foreach ($data['login_logs'] as $log) {
                                    if (!empty($log['ip_address'])) {
                                        $uniqueIps[$log['ip_address']] = true;
                                    }
                                }
                                echo count($uniqueIps);
                                ?>
                            </h3>
                        </div>
                        <i class="bi bi-globe fs-1 text-info opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Failed Login Attempts Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Failed Login Attempts (Cache Files)
            </h5>
        </div>
        <div class="card-body">
            <?php if (!empty($data['login_attempts'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Email Hash</th>
                                <th>Attempt Count</th>
                                <th>Last Attempt</th>
                                <th>Status</th>
                                <th>Lockout Remaining</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['login_attempts'] as $attempt): ?>
                                <tr class="<?php echo $attempt['is_locked'] ? 'table-warning' : ''; ?>">
                                    <td>
                                        <code class="small"><?php echo htmlspecialchars($attempt['email_hash']); ?></code>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $attempt['attempt_count'] >= 5 ? 'danger' : 'warning'; ?>">
                                            <?php echo $attempt['attempt_count']; ?> attempts
                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo date('M j, Y g:i A', $attempt['last_attempt_timestamp']); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($attempt['is_locked']): ?>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-lock me-1"></i>LOCKED
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">
                                                <i class="bi bi-exclamation-triangle me-1"></i>MONITORING
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($attempt['is_locked']): ?>
                                            <span class="text-danger">
                                                <?php 
                                                $minutes = floor($attempt['lockout_remaining'] / 60);
                                                $seconds = $attempt['lockout_remaining'] % 60;
                                                echo $minutes . 'm ' . $seconds . 's';
                                                ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" action="<?php echo URLROOT; ?>/admin/clearLoginAttempts" style="display: inline;">
                                            <input type="hidden" name="email_hash" value="<?php echo htmlspecialchars($attempt['email_hash']); ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Clear login attempts for this account?')">
                                                <i class="bi bi-trash me-1"></i>Clear
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-shield-check fs-1 text-success d-block mb-3"></i>
                    <h5 class="text-muted">No Failed Login Attempts</h5>
                    <p class="text-muted">All login attempts are successful</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Login History Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-clock-history me-2"></i>
                Login History (Audit Logs)
            </h5>
        </div>
        <div class="card-body">
            <?php if (!empty($data['login_logs'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>IP Address</th>
                                <th>User Agent</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['login_logs'] as $log): ?>
                                <?php 
                                $additionalData = json_decode($log['additional_data'], true);
                                $isSuccess = isset($additionalData['success']) && $additionalData['success'];
                                ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($log['first_name'])): ?>
                                            <?php echo htmlspecialchars($log['first_name'] . ' ' . $log['last_name']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">Unknown</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($additionalData['email'] ?? 'N/A'); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($isSuccess): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Success
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>Failed
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <code class="small"><?php echo htmlspecialchars($log['ip_address'] ?? 'N/A'); ?></code>
                                    </td>
                                    <td>
                                        <small class="text-muted" title="<?php echo htmlspecialchars($log['user_agent'] ?? 'N/A'); ?>">
                                            <?php 
                                            $userAgent = $log['user_agent'] ?? 'N/A';
                                            echo htmlspecialchars(substr($userAgent, 0, 50)) . (strlen($userAgent) > 50 ? '...' : '');
                                            ?>
                                        </small>
                                    </td>
                                    <td>
                                        <small><?php echo date('M j, Y g:i A', strtotime($log['created_at'])); ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                    <h5 class="text-muted">No Login History</h5>
                    <p class="text-muted">Login attempts will appear here</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Security Information -->
    <div class="card shadow-sm mt-4 border-info">
        <div class="card-body">
            <h6 class="text-info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                Security Information
            </h6>
            <ul class="mb-0">
                <li><strong>Account Lockout Policy:</strong> 5 failed attempts within 15 minutes = 30 minute lockout</li>
                <li><strong>Failed Attempts:</strong> Stored in <code>/cache/login_attempts_*.txt</code> files</li>
                <li><strong>Login History:</strong> Stored in <code>audit_logs</code> database table</li>
                <li><strong>Auto-Reset:</strong> Failed attempts reset after 15 minutes of inactivity</li>
                <li><strong>Manual Clear:</strong> Admins can manually clear failed attempts using the "Clear" button</li>
            </ul>
        </div>
    </div>
    
</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
