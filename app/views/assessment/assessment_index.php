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
        <p class="mb-0">Complete the assessment form for your enrolled children to help us understand their learning needs.</p>
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
        
        <!-- Assessment Cards -->
        <div class="col-12">
            <?php if (!empty($data['learners'])): ?>
                <?php foreach ($data['learners'] as $learner): ?>
                    <?php
                    // Find assessment for this learner
                    $assessment = null;
                    foreach ($data['assessments'] as $a) {
                        if ($a->learner_id == $learner->id) {
                            $assessment = $a;
                            break;
                        }
                    }
                    
                    // Determine assessment status
                    $assessmentStatus = $assessment ? $assessment->status : 'locked';
                    $canStart = in_array($assessmentStatus, ['unlocked', 'draft']);
                    $isSubmitted = in_array($assessmentStatus, ['submitted', 'reviewed']);
                    ?>
                    
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">
                                    <i class="bi bi-person me-2"></i>
                                    <?php echo htmlspecialchars($learner->first_name . ' ' . $learner->last_name); ?>
                                </h5>
                                <small class="text-muted">
                                    LRN: <?php echo htmlspecialchars($learner->lrn ?? 'Not assigned'); ?> | 
                                    Grade: <?php echo htmlspecialchars($learner->grade_level ?? 'N/A'); ?>
                                </small>
                            </div>
                            <div>
                                <?php if ($assessmentStatus === 'locked'): ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-lock me-1"></i>
                                        Locked
                                    </span>
                                <?php elseif ($assessmentStatus === 'unlocked'): ?>
                                    <span class="badge bg-primary">
                                        <i class="bi bi-unlock me-1"></i>
                                        Ready to Start
                                    </span>
                                <?php elseif ($assessmentStatus === 'draft'): ?>
                                    <span class="badge bg-warning">
                                        <i class="bi bi-pencil me-1"></i>
                                        In Progress
                                    </span>
                                <?php elseif ($assessmentStatus === 'submitted'): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Submitted
                                    </span>
                                <?php elseif ($assessmentStatus === 'reviewed'): ?>
                                    <span class="badge bg-info">
                                        <i class="bi bi-eye me-1"></i>
                                        Reviewed
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($assessmentStatus === 'locked'): ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-lock fs-1 text-muted mb-3"></i>
                                    <h6 class="text-muted">Assessment Locked</h6>
                                    <p class="text-muted mb-0">
                                        The assessment will be unlocked after your child's enrollment is approved by the SPED teacher.
                                    </p>
                                </div>
                            <?php elseif ($canStart): ?>
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-2">Assessment Form</h6>
                                        <p class="text-muted mb-0">
                                            Please complete the assessment form to help us understand <?php echo htmlspecialchars($learner->first_name); ?>'s 
                                            learning needs and create an appropriate education plan.
                                        </p>
                                        <?php if ($assessmentStatus === 'draft'): ?>
                                            <small class="text-warning">
                                                <i class="bi bi-info-circle me-1"></i>
                                                You have a saved draft. You can continue where you left off.
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="<?php echo URLROOT; ?>/assessment/form?learner_id=<?php echo $learner->id; ?>" 
                                           class="btn btn-primary">
                                            <i class="bi bi-<?php echo $assessmentStatus === 'draft' ? 'pencil' : 'play'; ?> me-2"></i>
                                            <?php echo $assessmentStatus === 'draft' ? 'Continue Assessment' : 'Start Assessment'; ?>
                                        </a>
                                    </div>
                                </div>
                            <?php elseif ($isSubmitted): ?>
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-2">Assessment Completed</h6>
                                        <p class="text-muted mb-0">
                                            Assessment submitted successfully on 
                                            <?php echo date('M j, Y \a\t g:i A', strtotime($assessment->parent_submitted_at)); ?>.
                                        </p>
                                        <?php if ($assessmentStatus === 'reviewed'): ?>
                                            <small class="text-success">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Reviewed by SPED teacher on <?php echo date('M j, Y', strtotime($assessment->reviewed_at)); ?>
                                            </small>
                                        <?php else: ?>
                                            <small class="text-info">
                                                <i class="bi bi-clock me-1"></i>
                                                Waiting for SPED teacher review
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="<?php echo URLROOT; ?>/assessment/view?id=<?php echo $assessment->id; ?>" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-eye me-2"></i>
                                            View Assessment
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-person-x fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No Enrolled Children</h5>
                        <p class="text-muted mb-4">
                            You don't have any enrolled children yet. Please complete the enrollment process first.
                        </p>
                        <a href="<?php echo URLROOT; ?>/enrollment/beef" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Start Enrollment
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
    </div>
    
</main>

<!-- System Messages -->
<?php require_once '../app/views/partials/simple_popup.php'; ?>

<?php require_once '../app/views/layouts/footer.php'; ?>