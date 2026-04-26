<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1><i class="bi bi-house-door me-2"></i>Welcome, <?php echo htmlspecialchars($data['user_name']); ?>! 👋</h1>
        <p class="mb-0">Manage your child's SPED enrollment and track their progress</p>
    </div>
    
    <!-- Alerts -->
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
    
    <div class="row g-4">
        
        <!-- Left Column -->
        <div class="col-lg-8">
            
            <!-- Enrollment Checklist -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-list-check me-2 text-brand-red"></i>
                    <h5 class="mb-0">Enrollment Requirements for SPED Education</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        Please prepare the following documents before starting the enrollment process:
                    </p>
                    
                    <div class="checklist-item rounded mb-2">
                        <div class="d-flex align-items-start">
                            <div class="check-icon me-3 mt-1">
                                <i class="bi bi-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">BEEF Form (Basic Education Enrollment Form)</div>
                                <small class="text-muted">Complete the online enrollment form with learner and parent information</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="checklist-item rounded mb-2">
                        <div class="d-flex align-items-start">
                            <div class="check-icon me-3 mt-1">
                                <i class="bi bi-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">PSA Birth Certificate</div>
                                <small class="text-muted">Required - Original or certified true copy from PSA</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="checklist-item rounded mb-2">
                        <div class="d-flex align-items-start">
                            <div class="check-icon me-3 mt-1">
                                <i class="bi bi-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">PWD ID Card</div>
                                <small class="text-muted">If available - Person with Disability identification card</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="checklist-item rounded mb-4">
                        <div class="d-flex align-items-start">
                            <div class="check-icon me-3 mt-1">
                                <i class="bi bi-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">Medical Records</div>
                                <small class="text-muted">If available - Recent medical assessment or diagnosis reports</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/enrollment/beef" class="btn btn-primary btn-lg">
                            <i class="bi bi-plus-circle me-2"></i>
                            Start Enrollment Process
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
            
            <!-- Announcements -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-megaphone me-2 text-brand-red"></i>
                    <h5 class="mb-0">Announcements</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['announcements'])): ?>
                        <?php foreach ($data['announcements'] as $announcement): ?>
                            <div class="announcement-card card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title text-brand-blue">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <?php echo htmlspecialchars($announcement->title); ?>
                                    </h6>
                                    <p class="card-text text-muted small mb-2">
                                        <?php echo htmlspecialchars($announcement->content); ?>
                                    </p>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        Posted: <?php echo date('M j, Y', strtotime($announcement->created_at)); ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p>No announcements at this time</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Enrolled Children -->
            <?php if (!empty($data['learners'])): ?>
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <i class="bi bi-people me-2 text-brand-red"></i>
                        <h5 class="mb-0">My Enrolled Children</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($data['learners'] as $learner): ?>
                            <div class="card border-start border-4 border-brand-blue mb-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar-circle me-3">
                                            <?php echo strtoupper(substr($learner->first_name, 0, 1)); ?>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($learner->first_name . ' ' . $learner->last_name); ?></h6>
                                            <small class="text-muted">Grade <?php echo htmlspecialchars($learner->grade_level ?? 'N/A'); ?></small>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>
                                            <?php echo ucfirst($learner->status ?? 'active'); ?>
                                        </span>
                                        <a href="<?php echo URLROOT; ?>/learner/view?id=<?php echo $learner->id; ?>" class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
        
    </div>
    
</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
