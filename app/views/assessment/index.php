<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1>
            <i class="bi bi-clipboard-check me-2"></i>
            Assessment
        </h1>
        <p class="mb-0">Complete the assessment form for your child</p>
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

    <!-- Assessments List -->
    <?php if (empty($data['assessments'])): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-muted">No Assessments Available</h5>
                <p class="text-muted">Assessments will be unlocked after your child's enrollment is approved.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($data['assessments'] as $assessment): ?>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <?php echo htmlspecialchars($assessment->first_name . ' ' . $assessment->last_name); ?>
                                    </h5>
                                    <?php if ($assessment->lrn): ?>
                                        <small class="text-muted">LRN: <?php echo htmlspecialchars($assessment->lrn); ?></small>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php
                                    $statusBadge = '';
                                    $statusText = '';
                                    switch ($assessment->status) {
                                        case 'locked':
                                            $statusBadge = 'bg-secondary';
                                            $statusText = 'Locked';
                                            break;
                                        case 'unlocked':
                                            $statusBadge = 'bg-warning';
                                            $statusText = 'Not Started';
                                            break;
                                        case 'draft':
                                            $statusBadge = 'bg-info';
                                            $statusText = 'In Progress';
                                            break;
                                        case 'submitted':
                                            $statusBadge = 'bg-success';
                                            $statusText = 'Submitted';
                                            break;
                                        case 'reviewed':
                                            $statusBadge = 'bg-primary';
                                            $statusText = 'Reviewed';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $statusBadge; ?>">
                                        <?php echo $statusText; ?>
                                    </span>
                                </div>
                            </div>

                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="bi bi-mortarboard me-1"></i>
                                    Grade: <?php echo htmlspecialchars($assessment->grade_level); ?>
                                </small>
                            </p>

                            <?php if ($assessment->status === 'locked'): ?>
                                <div class="alert alert-warning mb-0">
                                    <i class="bi bi-lock me-2"></i>
                                    Assessment locked. Waiting for enrollment approval.
                                </div>
                            <?php elseif ($assessment->status === 'unlocked'): ?>
                                <a href="<?php echo URLROOT; ?>/assessment/form?learner_id=<?php echo $assessment->learner_id; ?>" 
                                   class="btn btn-primary w-100">
                                    <i class="bi bi-play-circle me-2"></i>
                                    Start Assessment
                                </a>
                            <?php elseif ($assessment->status === 'draft'): ?>
                                <a href="<?php echo URLROOT; ?>/assessment/form?learner_id=<?php echo $assessment->learner_id; ?>" 
                                   class="btn btn-info w-100">
                                    <i class="bi bi-pencil me-2"></i>
                                    Continue Assessment
                                </a>
                            <?php else: ?>
                                <a href="<?php echo URLROOT; ?>/assessment/viewAssessment?id=<?php echo $assessment->id; ?>" 
                                   class="btn btn-outline-primary w-100">
                                    <i class="bi bi-eye me-2"></i>
                                    View Assessment
                                </a>
                            <?php endif; ?>

                            <?php if ($assessment->parent_submitted_at): ?>
                                <small class="text-muted d-block mt-2 text-center">
                                    Submitted: <?php echo date('M j, Y g:i A', strtotime($assessment->parent_submitted_at)); ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
