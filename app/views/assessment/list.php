<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">
                <i class="bi bi-clipboard-check me-2 text-brand-red"></i>
                Learners Ready for Assessment
            </h1>
            <p class="text-muted mb-0">Select a learner to begin their initial assessment. All learners listed have completed enrollment verification.</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/sped/dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>
    </div>
    
    <!-- Alerts -->
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Error:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Success:</strong> <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Statistics Card -->
    <div class="row g-3 mb-4">
        <div class="col-md-12">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Learners Ready for Assessment</div>
                            <div class="stat-number"><?php echo count($data['learners'] ?? []); ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Learners List -->
    <?php if (!empty($data['learners'])): ?>
        <div class="row g-4">
            <?php foreach ($data['learners'] as $learner): ?>
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <?php echo htmlspecialchars($learner->first_name . ' ' . $learner->last_name); ?>
                                    </h5>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Ready for Assessment
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-book me-2 text-muted"></i>
                                    <span><strong>Grade:</strong> <?php echo htmlspecialchars($learner->grade_level); ?></span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-person me-2 text-muted"></i>
                                    <span><strong>Parent:</strong> <?php echo htmlspecialchars($learner->parent_name ?? 'N/A'); ?></span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-calendar3 me-2 text-muted"></i>
                                    <span><strong>Enrolled:</strong> <?php echo date('M j, Y', strtotime($learner->created_at)); ?></span>
                                </div>
                                <?php if (!empty($learner->disability_type)): ?>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-info-circle me-2 text-muted"></i>
                                        <span><strong>Disability:</strong> <?php echo htmlspecialchars($learner->disability_type); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="d-grid">
                                <a href="<?php echo URLROOT; ?>/assessment/create?learner_id=<?php echo $learner->id; ?>" 
                                   class="btn btn-primary">
                                    <i class="bi bi-clipboard-check me-1"></i>Begin Assessment
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">No Learners Ready for Assessment</h5>
                <p class="text-muted mb-4">All enrolled learners have either completed their assessments or are still pending enrollment verification.</p>
                <a href="<?php echo URLROOT; ?>/enrollment/verify" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Check Pending Enrollments
                </a>
            </div>
        </div>
    <?php endif; ?>
    
</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
