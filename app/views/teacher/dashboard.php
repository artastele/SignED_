<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">
                <i class="bi bi-person-workspace me-2 text-brand-red"></i>
                Teacher Dashboard
            </h1>
            <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($data['user_name'] ?? 'Teacher'); ?>!</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
            </button>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">My Learners</div>
                            <div class="stat-number"><?php echo $data['total_learners'] ?? 0; ?></div>
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
                            <div class="stat-label">Active IEPs</div>
                            <div class="stat-number"><?php echo $data['active_ieps'] ?? 0; ?></div>
                        </div>
                        <div class="stat-icon text-success">
                            <i class="bi bi-file-earmark-medical"></i>
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
                            <div class="stat-label">Pending Submissions</div>
                            <div class="stat-number"><?php echo $data['pending_submissions'] ?? 0; ?></div>
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
                            <div class="stat-label">Materials Uploaded</div>
                            <div class="stat-number"><?php echo $data['materials_count'] ?? 0; ?></div>
                        </div>
                        <div class="stat-icon text-info">
                            <i class="bi bi-book"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Access Cards -->
    <h5 class="mb-3">Quick Access</h5>
    <div class="row g-3 mb-4">
        <!-- My Learners -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon bg-primary text-white rounded-circle me-3">
                            <i class="bi bi-people"></i>
                        </div>
                        <h5 class="card-title mb-0">My Learners</h5>
                    </div>
                    <p class="card-text text-muted">View and manage your assigned learners</p>
                    <div class="d-grid">
                        <a href="<?php echo URLROOT; ?>/learner/records" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-right-circle me-1"></i>View Learners
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
                        <div class="feature-icon bg-success text-white rounded-circle me-3">
                            <i class="bi bi-book"></i>
                        </div>
                        <h5 class="card-title mb-0">Learning Materials</h5>
                    </div>
                    <p class="card-text text-muted">Upload and manage educational resources</p>
                    <div class="d-grid">
                        <a href="<?php echo URLROOT; ?>/learner/uploadMaterial" class="btn btn-outline-success">
                            <i class="bi bi-arrow-right-circle me-1"></i>Manage Materials
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Student Submissions -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon bg-warning text-white rounded-circle me-3">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <h5 class="card-title mb-0">Student Submissions</h5>
                    </div>
                    <p class="card-text text-muted">Review and grade student work</p>
                    <div class="d-grid">
                        <a href="<?php echo URLROOT; ?>/learner/submissions" class="btn btn-outline-warning">
                            <i class="bi bi-arrow-right-circle me-1"></i>View Submissions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2 text-brand-red"></i>
                        Recent Submissions
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['recent_submissions'])): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($data['recent_submissions'] as $submission): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($submission->learner_name); ?></h6>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($submission->material_title); ?>
                                            </small>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y', strtotime($submission->submitted_at)); ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No recent submissions</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-megaphone me-2 text-brand-red"></i>
                        Announcements
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['announcements'])): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($data['announcements'] as $announcement): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($announcement->title); ?></h6>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars(substr($announcement->content, 0, 60)) . '...'; ?>
                                            </small>
                                        </div>
                                        <?php
                                        $priorityColors = [
                                            'urgent' => 'danger',
                                            'high' => 'warning',
                                            'normal' => 'info',
                                            'low' => 'secondary'
                                        ];
                                        $color = $priorityColors[$announcement->priority] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $color; ?>">
                                            <?php echo ucfirst($announcement->priority); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No announcements</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
