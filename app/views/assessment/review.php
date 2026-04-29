<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1>
            <i class="bi bi-clipboard-check me-2"></i>
            Assessment Review
        </h1>
        <p class="mb-0">Review submitted assessments from parents</p>
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

    <!-- Statistics Card -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Submitted Assessments</div>
                            <div class="stat-number"><?php echo count($data['assessments']); ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessments Table -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-table me-2"></i>Submitted Assessments
        </div>
        <div class="card-body">
            <?php if (empty($data['assessments'])): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                    <p class="text-muted">No submitted assessments found.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>LRN</th>
                                <th>Student Name</th>
                                <th>Grade</th>
                                <th>Parent</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $counter = 1;
                            foreach ($data['assessments'] as $assessment): 
                            ?>
                                <tr>
                                    <td><?php echo $counter++; ?></td>
                                    <td>
                                        <?php if ($assessment->lrn): ?>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($assessment->lrn); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($assessment->last_name); ?>, <?php echo htmlspecialchars($assessment->first_name); ?></strong>
                                        <?php if ($assessment->middle_name): ?>
                                            <span class="text-muted"><?php echo htmlspecialchars(substr($assessment->middle_name, 0, 1)); ?>.</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($assessment->grade_level); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($assessment->parent_name); ?></td>
                                    <td>
                                        <small><?php echo date('M j, Y', strtotime($assessment->parent_submitted_at)); ?></small><br>
                                        <small class="text-muted"><?php echo date('g:i A', strtotime($assessment->parent_submitted_at)); ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?php echo URLROOT; ?>/assessment/viewAssessment?id=<?php echo $assessment->id; ?>" 
                                               class="btn btn-outline-primary" 
                                               title="View Assessment">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <form method="POST" action="<?php echo URLROOT; ?>/assessment/markReviewed" style="display: inline;">
                                                <input type="hidden" name="assessment_id" value="<?php echo $assessment->id; ?>">
                                                <button type="submit" 
                                                        class="btn btn-outline-success" 
                                                        title="Mark as Reviewed"
                                                        onclick="return confirm('Mark this assessment as reviewed?')">
                                                    <i class="bi bi-check-circle"></i> Mark Reviewed
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</main>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
