<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">
                <i class="bi bi-speedometer2 me-2 text-brand-red"></i>
                Admin Dashboard
            </h1>
            <p class="text-muted mb-0">Complete system overview and management</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
            </button>
        </div>
    </div>
    
    <!-- System Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Total Users</div>
                            <div class="stat-number"><?php echo $data['total_users'] ?? 0; ?></div>
                        </div>
                        <div class="stat-icon text-primary">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Total Learners</div>
                            <div class="stat-number"><?php echo $data['total_learners'] ?? 0; ?></div>
                        </div>
                        <div class="stat-icon text-success">
                            <i class="bi bi-person-badge"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Pending Enrollments</div>
                            <div class="stat-number"><?php echo $data['pending_enrollments'] ?? 0; ?></div>
                        </div>
                        <div class="stat-icon text-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Active IEPs</div>
                            <div class="stat-number"><?php echo $data['active_ieps'] ?? 0; ?></div>
                        </div>
                        <div class="stat-icon text-info">
                            <i class="bi bi-file-earmark-medical"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Access Cards -->
    <h5 class="mb-3">Quick Access</h5>
    <div class="row g-3 mb-4">
        <!-- User Management -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon bg-primary text-white rounded-circle me-3">
                            <i class="bi bi-people"></i>
                        </div>
                        <h5 class="card-title mb-0">User Management</h5>
                    </div>
                    <p class="card-text text-muted">Manage all system users, roles, and permissions</p>
                    <div class="d-grid">
                        <a href="<?php echo URLROOT; ?>/admin/users" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-right-circle me-1"></i>Manage Users
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Enrollments -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon bg-success text-white rounded-circle me-3">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <h5 class="card-title mb-0">Enrollments</h5>
                    </div>
                    <p class="card-text text-muted">View and manage all enrollment applications</p>
                    <div class="d-grid">
                        <a href="<?php echo URLROOT; ?>/enrollment/verify" class="btn btn-outline-success">
                            <i class="bi bi-arrow-right-circle me-1"></i>View Enrollments
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Assessments -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon bg-warning text-white rounded-circle me-3">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <h5 class="card-title mb-0">Assessments</h5>
                    </div>
                    <p class="card-text text-muted">Monitor learner assessments and evaluations</p>
                    <div class="d-grid">
                        <a href="<?php echo URLROOT; ?>/assessment/list" class="btn btn-outline-warning">
                            <i class="bi bi-arrow-right-circle me-1"></i>View Assessments
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- IEP Management -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon bg-info text-white rounded-circle me-3">
                            <i class="bi bi-file-earmark-medical"></i>
                        </div>
                        <h5 class="card-title mb-0">IEP Management</h5>
                    </div>
                    <p class="card-text text-muted">Manage Individual Education Programs</p>
                    <div class="d-grid">
                        <a href="<?php echo URLROOT; ?>/iep/list" class="btn btn-outline-info">
                            <i class="bi bi-arrow-right-circle me-1"></i>View IEPs
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Learning Materials -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon bg-secondary text-white rounded-circle me-3">
                            <i class="bi bi-book"></i>
                        </div>
                        <h5 class="card-title mb-0">Learning Materials</h5>
                    </div>
                    <p class="card-text text-muted">Manage educational resources and materials</p>
                    <div class="d-grid">
                        <a href="<?php echo URLROOT; ?>/learner/uploadMaterial" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-right-circle me-1"></i>View Materials
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Settings -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon bg-dark text-white rounded-circle me-3">
                            <i class="bi bi-gear"></i>
                        </div>
                        <h5 class="card-title mb-0">System Settings</h5>
                    </div>
                    <p class="card-text text-muted">Configure system settings and preferences</p>
                    <div class="d-grid">
                        <a href="<?php echo URLROOT; ?>/admin/settings" class="btn btn-outline-dark">
                            <i class="bi bi-arrow-right-circle me-1"></i>Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- User Breakdown & Recent Activity -->
    <div class="row g-3 mb-4">
        <!-- User Breakdown by Role -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2 text-brand-red"></i>
                        Users by Role
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['users_by_role'])): ?>
                        <div class="list-group list-group-flush">
                            <?php 
                            $roleLabels = [
                                'admin' => 'Administrators',
                                'sped_teacher' => 'SPED Teachers',
                                'teacher' => 'Teachers',
                                'guidance' => 'Guidance Counselors',
                                'principal' => 'Principals',
                                'parent' => 'Parents',
                                'learner' => 'Learners'
                            ];
                            
                            foreach ($data['users_by_role'] as $role => $count): 
                                $label = $roleLabels[$role] ?? ucfirst(str_replace('_', ' ', $role));
                            ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-person me-2"></i><?php echo $label; ?></span>
                                    <span class="badge bg-primary rounded-pill"><?php echo $count; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No user data available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2 text-brand-red"></i>
                        Recent Activity
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['recent_users'])): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($data['recent_users'] as $user): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($user->fullname); ?></h6>
                                            <small class="text-muted">
                                                <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($user->email); ?>
                                            </small>
                                            <br>
                                            <span class="badge bg-secondary mt-1">
                                                <?php echo ucfirst(str_replace('_', ' ', $user->role)); ?>
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y', strtotime($user->created_at)); ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No recent activity</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- System Health Status -->
    <div class="row g-3 mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-heart-pulse me-2 text-brand-red"></i>
                        System Health
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-database me-2 text-success"></i>
                                <strong>Database:</strong>
                            </div>
                            <span class="badge bg-success">
                                <?php echo $data['system_status']['database'] ?? 'Unknown'; ?>
                            </span>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-hdd me-2 text-info"></i>
                                <strong>Storage:</strong>
                            </div>
                            <?php if (isset($data['system_status']['storage'])): ?>
                                <span class="text-muted">
                                    <?php echo $data['system_status']['storage']['used']; ?> / 
                                    <?php echo $data['system_status']['storage']['total']; ?>
                                    (<?php echo $data['system_status']['storage']['percent']; ?>%)
                                </span>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-code-slash me-2 text-primary"></i>
                                <strong>PHP Version:</strong>
                            </div>
                            <span class="text-muted">
                                <?php echo $data['system_status']['php_version'] ?? 'Unknown'; ?>
                            </span>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-clock me-2 text-secondary"></i>
                                <strong>Server Time:</strong>
                            </div>
                            <span class="text-muted">
                                <?php echo $data['system_status']['server_time'] ?? 'Unknown'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Additional Admin Actions -->
    <div class="row g-3 mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-tools me-2 text-brand-red"></i>
                        Administrative Tools
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <a href="<?php echo URLROOT; ?>/admin/announcements" class="btn btn-outline-primary w-100">
                                <i class="bi bi-megaphone me-1"></i>Announcements
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?php echo URLROOT; ?>/admin/logs" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-file-text me-1"></i>Audit Logs
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?php echo URLROOT; ?>/admin/settings" class="btn btn-outline-dark w-100">
                                <i class="bi bi-gear me-1"></i>System Settings
                            </a>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-info w-100" onclick="location.reload()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Refresh Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
