<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1>
            <i class="bi bi-speedometer2 me-2"></i>
            Learner Dashboard
        </h1>
        <p class="mb-0">Welcome back, <?= htmlspecialchars($data['learner']->first_name ?? 'Learner') ?>! Track your learning progress and materials.</p>
    </div>
    
    <!-- Welcome Section -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-2">
                                <i class="bi bi-person-circle me-2"></i>
                                Welcome back, <?= htmlspecialchars($data['learner']->first_name ?? 'Learner') ?>! 👋
                            </h4>
                            <p class="mb-0 opacity-75">
                                <?php if ($data['current_iep'] ?? null): ?>
                                    Your IEP is active. Keep up the great work on your learning objectives!
                                <?php else: ?>
                                    Your learning plan is being prepared. Check back soon for new materials.
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="/learner/materials" class="btn btn-light">
                                <i class="bi bi-book me-1"></i>
                                My Materials
                            </a>
                            <a href="/learner/track-progress" class="btn btn-outline-light">
                                <i class="bi bi-graph-up me-1"></i>
                                Progress
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Total Materials</div>
                            <div class="stat-number"><?= $data['stats']['total_materials'] ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-book"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Completion Rate</div>
                            <div class="stat-number"><?= $data['stats']['completion_percentage'] ?>%</div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: <?= $data['stats']['completion_percentage'] ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($data['stats']['overdue_count'] > 0): ?>
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Overdue Items</div>
                            <div class="stat-number text-danger"><?= $data['stats']['overdue_count'] ?></div>
                        </div>
                        <div class="stat-icon text-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($data['stats']['upcoming_count'] > 0): ?>
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Due This Week</div>
                            <div class="stat-number text-warning"><?= $data['stats']['upcoming_count'] ?></div>
                        </div>
                        <div class="stat-icon text-warning">
                            <i class="bi bi-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        
        <!-- Left Column -->
        <div class="col-lg-8">
            
            <!-- Recent Materials -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-book me-2 text-brand-red"></i>
                        Recent Learning Materials
                    </h5>
                    <?php if (count($data['materials']) > 5): ?>
                        <a href="/learner/materials" class="btn btn-sm btn-outline-primary">
                            View All (<?= count($data['materials']) ?>)
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($data['materials'])): ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-book fs-1 d-block mb-2"></i>
                            <h6>No materials assigned yet</h6>
                            <p class="mb-0">Your SPED teacher will assign learning materials based on your IEP objectives.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php 
                            $recentMaterials = array_slice($data['materials'], 0, 5); // Show only 5 most recent
                            foreach ($recentMaterials as $material): 
                                $isSubmitted = false;
                                foreach ($data['submissions'] as $submission) {
                                    if ($submission->material_id == $material->id) {
                                        $isSubmitted = true;
                                        break;
                                    }
                                }
                                
                                $isOverdue = false;
                                if ($material->due_date && !$isSubmitted) {
                                    $isOverdue = strtotime($material->due_date) < time();
                                }
                            ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= htmlspecialchars($material->title) ?></h6>
                                        <p class="mb-1 text-muted small"><?= htmlspecialchars($material->iep_objective) ?></p>
                                        <?php if ($material->due_date): ?>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                Due: <?= date('M j, Y', strtotime($material->due_date)) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <span class="badge bg-<?= $isSubmitted ? 'success' : ($isOverdue ? 'danger' : 'warning') ?>">
                                        <?= $isSubmitted ? 'Submitted' : ($isOverdue ? 'Overdue' : 'Pending') ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (count($data['materials']) > 5): ?>
                        <div class="text-center mt-3">
                            <a href="/learner/materials" class="btn btn-primary">
                                <i class="bi bi-arrow-right me-1"></i>
                                View All Materials
                            </a>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Recent Submissions -->
            <?php if (!empty($data['submissions'])): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-arrow-up me-2 text-brand-red"></i>
                        Recent Submissions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php 
                        $recentSubmissions = array_slice($data['submissions'], 0, 3); // Show only 3 most recent
                        foreach ($recentSubmissions as $submission): 
                        ?>
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?= htmlspecialchars($submission->material_title) ?></h6>
                                    <small class="text-muted d-block">
                                        <i class="bi bi-clock me-1"></i>
                                        Submitted: <?= date('M j, Y \a\t g:i A', strtotime($submission->submitted_at)) ?>
                                    </small>
                                    <?php if ($submission->reviewed_at): ?>
                                        <small class="text-muted">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Reviewed: <?= date('M j, Y', strtotime($submission->reviewed_at)) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-<?= $submission->reviewed_at ? 'success' : 'warning' ?>">
                                    <?= $submission->reviewed_at ? 'Reviewed' : 'Under Review' ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
            
            <!-- IEP Information -->
            <?php if ($data['current_iep']): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-medical me-2 text-brand-red"></i>
                        Current IEP Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">IEP Period</h6>
                            <p class="mb-1 text-muted">
                                <?= date('M j, Y', strtotime($data['current_iep']->start_date)) ?> - 
                                <?= date('M j, Y', strtotime($data['current_iep']->end_date)) ?>
                            </p>
                            <small class="text-muted">
                                <i class="bi bi-person me-1"></i>
                                Created by: <?= htmlspecialchars($data['current_iep']->created_by_name) ?>
                            </small>
                        </div>
                        <span class="badge bg-success">Active</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2 text-brand-red"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/learner/materials" class="btn btn-outline-primary">
                            <i class="bi bi-book me-2"></i>
                            View All Materials
                        </a>
                        <a href="/learner/track-progress" class="btn btn-outline-success">
                            <i class="bi bi-graph-up me-2"></i>
                            Track Progress
                        </a>
                        <?php if (!empty($data['materials'])): ?>
                        <a href="/learner/submitWork" class="btn btn-outline-warning">
                            <i class="bi bi-upload me-2"></i>
                            Submit Work
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>
    
</main>

<!-- System Messages -->
<?php require_once '../app/views/partials/simple_popup.php'; ?>

<!-- Success/Error Messages -->
<?php if (isset($_GET['success'])): ?>
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div class="toast show" role="alert">
        <div class="toast-header bg-success text-white">
            <i class="bi bi-check-circle me-2"></i>
            <strong class="me-auto">Success</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            <?php
            switch ($_GET['success']) {
                case 'work_submitted':
                    echo 'Work submitted successfully!';
                    break;
                default:
                    echo 'Action completed successfully!';
            }
            ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div class="toast show" role="alert">
        <div class="toast-header bg-danger text-white">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            <?php
            switch ($_GET['error']) {
                case 'learner_not_found':
                    echo 'Learner account not found.';
                    break;
                case 'access_denied':
                    echo 'Access denied.';
                    break;
                default:
                    echo 'An error occurred. Please try again.';
            }
            ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once '../app/views/layouts/footer.php'; ?>