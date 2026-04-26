<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">
                <i class="bi bi-file-text me-2 text-brand-red"></i>
                Audit Logs
            </h1>
            <p class="text-muted mb-0">View system activity and audit trail</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-outline-secondary me-2">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
            </button>
        </div>
    </div>
    
    <!-- Logs Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (!empty($data['logs'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Entity</th>
                                <th>Details</th>
                                <th>IP Address</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['logs'] as $log): ?>
                                <tr>
                                    <td><?php echo $log->id; ?></td>
                                    <td>
                                        <?php if (!empty($log->fullname)): ?>
                                            <div><?php echo htmlspecialchars($log->fullname); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($log->email); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">System</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $actionBadge = 'secondary';
                                        switch ($log->action_type) {
                                            case 'login':
                                                $actionBadge = 'success';
                                                break;
                                            case 'logout':
                                                $actionBadge = 'info';
                                                break;
                                            case 'status_change':
                                                $actionBadge = 'warning';
                                                break;
                                            case 'approval':
                                                $actionBadge = 'success';
                                                break;
                                            case 'rejection':
                                                $actionBadge = 'danger';
                                                break;
                                            case 'role_change':
                                                $actionBadge = 'primary';
                                                break;
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $actionBadge; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $log->action_type)); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($log->entity_type)): ?>
                                            <div><?php echo ucfirst(str_replace('_', ' ', $log->entity_type)); ?></div>
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
                                                <small class="text-muted">From: <?php echo htmlspecialchars($log->old_value); ?></small><br>
                                            <?php endif; ?>
                                            <?php if (!empty($log->new_value)): ?>
                                                <small class="text-success">To: <?php echo htmlspecialchars($log->new_value); ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo htmlspecialchars($log->ip_address ?? 'N/A'); ?></small>
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
                    <h5 class="text-muted">No Audit Logs Found</h5>
                    <p class="text-muted">System activity will appear here</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
