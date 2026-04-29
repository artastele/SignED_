<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">
                <i class="bi bi-person-badge me-2 text-brand-red"></i>
                Admin Activity Monitor
            </h1>
            <p class="text-muted mb-0">Track all administrative actions and changes</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Admin Users Summary -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2"></i>
                        Administrator Accounts
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['admin_users'])): ?>
                        <div class="row">
                            <?php foreach ($data['admin_users'] as $admin): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card border-primary">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="bi bi-person-circle me-2 text-primary"></i>
                                                <?php echo htmlspecialchars($admin->fullname); ?>
                                            </h6>
                                            <p class="card-text mb-1">
                                                <small class="text-muted">
                                                    <i class="bi bi-envelope me-1"></i>
                                                    <?php echo htmlspecialchars($admin->email); ?>
                                                </small>
                                            </p>
                                            <p class="card-text mb-0">
                                                <span class="badge bg-primary">Administrator</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No administrator accounts found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Actions</h6>
                            <h3 class="mb-0 text-primary"><?php echo count($data['admin_logs']); ?></h3>
                        </div>
                        <i class="bi bi-activity fs-1 text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Approvals</h6>
                            <h3 class="mb-0 text-success">
                                <?php 
                                $approvalCount = 0;
                                foreach ($data['admin_logs'] as $log) {
                                    if ($log->action_type === 'approval') $approvalCount++;
                                }
                                echo $approvalCount;
                                ?>
                            </h3>
                        </div>
                        <i class="bi bi-check-circle fs-1 text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Settings Changes</h6>
                            <h3 class="mb-0 text-warning">
                                <?php 
                                $settingsCount = 0;
                                foreach ($data['admin_logs'] as $log) {
                                    if ($log->action_type === 'settings_update') $settingsCount++;
                                }
                                echo $settingsCount;
                                ?>
                            </h3>
                        </div>
                        <i class="bi bi-gear fs-1 text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">User Changes</h6>
                            <h3 class="mb-0 text-info">
                                <?php 
                                $userChangeCount = 0;
                                foreach ($data['admin_logs'] as $log) {
                                    if ($log->action_type === 'role_change' || $log->action_type === 'user_update') {
                                        $userChangeCount++;
                                    }
                                }
                                echo $userChangeCount;
                                ?>
                            </h3>
                        </div>
                        <i class="bi bi-person-gear fs-1 text-info opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Activity Logs Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="bi bi-list-check me-2"></i>
                Recent Admin Activity
            </h5>
        </div>
        <div class="card-body">
            <?php if (!empty($data['admin_logs'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Admin User</th>
                                <th>Action Type</th>
                                <th>Entity</th>
                                <th>Details</th>
                                <th>IP Address</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['admin_logs'] as $log): ?>
                                <tr>
                                    <td><?php echo $log->id; ?></td>
                                    <td>
                                        <div>
                                            <strong><?php echo htmlspecialchars($log->fullname); ?></strong>
                                        </div>
                                        <small class="text-muted"><?php echo htmlspecialchars($log->email); ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $actionBadge = 'secondary';
                                        $actionIcon = 'activity';
                                        
                                        switch ($log->action_type) {
                                            case 'login':
                                                $actionBadge = 'success';
                                                $actionIcon = 'box-arrow-in-right';
                                                break;
                                            case 'logout':
                                                $actionBadge = 'info';
                                                $actionIcon = 'box-arrow-right';
                                                break;
                                            case 'status_change':
                                                $actionBadge = 'warning';
                                                $actionIcon = 'arrow-left-right';
                                                break;
                                            case 'approval':
                                                $actionBadge = 'success';
                                                $actionIcon = 'check-circle';
                                                break;
                                            case 'rejection':
                                                $actionBadge = 'danger';
                                                $actionIcon = 'x-circle';
                                                break;
                                            case 'role_change':
                                                $actionBadge = 'primary';
                                                $actionIcon = 'person-gear';
                                                break;
                                            case 'settings_update':
                                                $actionBadge = 'warning';
                                                $actionIcon = 'gear';
                                                break;
                                            case 'announcement_create':
                                                $actionBadge = 'info';
                                                $actionIcon = 'megaphone';
                                                break;
                                            case 'user_update':
                                                $actionBadge = 'primary';
                                                $actionIcon = 'person-check';
                                                break;
                                            case 'clear_login_attempts':
                                                $actionBadge = 'danger';
                                                $actionIcon = 'shield-x';
                                                break;
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $actionBadge; ?>">
                                            <i class="bi bi-<?php echo $actionIcon; ?> me-1"></i>
                                            <?php echo ucfirst(str_replace('_', ' ', $log->action_type)); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($log->entity_type)): ?>
                                            <div>
                                                <span class="badge bg-secondary">
                                                    <?php echo ucfirst(str_replace('_', ' ', $log->entity_type)); ?>
                                                </span>
                                            </div>
                                            <?php if (!empty($log->entity_id)): ?>
                                                <small class="text-muted">ID: <?php echo $log->entity_id; ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($log->old_value) || !empty($log->new_value)): ?>
                                            <?php if (!empty($log->old_value)): ?>
                                                <small class="text-muted">
                                                    <strong>From:</strong> <?php echo htmlspecialchars(substr($log->old_value, 0, 50)); ?>
                                                    <?php echo strlen($log->old_value) > 50 ? '...' : ''; ?>
                                                </small><br>
                                            <?php endif; ?>
                                            <?php if (!empty($log->new_value)): ?>
                                                <small class="text-success">
                                                    <strong>To:</strong> <?php echo htmlspecialchars(substr($log->new_value, 0, 50)); ?>
                                                    <?php echo strlen($log->new_value) > 50 ? '...' : ''; ?>
                                                </small>
                                            <?php endif; ?>
                                        <?php elseif (!empty($log->additional_data)): ?>
                                            <?php 
                                            $additionalData = json_decode($log->additional_data, true);
                                            if (is_array($additionalData) && !empty($additionalData)) {
                                                echo '<small class="text-muted">';
                                                foreach ($additionalData as $key => $value) {
                                                    if (!is_array($value) && !is_object($value)) {
                                                        echo '<strong>' . htmlspecialchars($key) . ':</strong> ' . htmlspecialchars($value) . '<br>';
                                                    }
                                                }
                                                echo '</small>';
                                            } else {
                                                echo '<span class="text-muted">-</span>';
                                            }
                                            ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <code class="small"><?php echo htmlspecialchars($log->ip_address ?? 'N/A'); ?></code>
                                    </td>
                                    <td>
                                        <small><?php echo date('M j, Y g:i A', strtotime($log->created_at)); ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                    <h5 class="text-muted">No Admin Activity Found</h5>
                    <p class="text-muted">Administrative actions will appear here</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Security Information -->
    <div class="card shadow-sm mt-4 border-info">
        <div class="card-body">
            <h6 class="text-info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                About Admin Activity Monitoring
            </h6>
            <ul class="mb-0">
                <li><strong>Purpose:</strong> Track all actions performed by administrator accounts</li>
                <li><strong>Logged Actions:</strong> Login/logout, user management, role changes, settings updates, approvals/rejections</li>
                <li><strong>Data Storage:</strong> All activity stored in <code>audit_logs</code> database table</li>
                <li><strong>Retention:</strong> Logs are retained indefinitely for security and compliance</li>
                <li><strong>Access:</strong> Only administrators can view admin activity logs</li>
            </ul>
        </div>
    </div>
    
</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
