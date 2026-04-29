<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1>
            <i class="bi bi-folder me-2"></i>
            Student Records
        </h1>
        <p class="mb-0">List of all enrolled students in the SPED program</p>
    </div>
    
    <!-- Statistics Card -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Total Enrolled</div>
                            <div class="stat-number"><?php echo count($data['learners']); ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Student Records Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-table me-2"></i>Enrollment List
            </div>
            <div>
                <button class="btn btn-sm btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-2"></i>Print List
                </button>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($data['learners'])): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                    <p class="text-muted">No enrolled students found.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>LRN</th>
                                <th>Student Name</th>
                                <th>Date of Birth</th>
                                <th>Age</th>
                                <th>Grade Level</th>
                                <th>Parent/Guardian</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $counter = 1;
                            foreach ($data['learners'] as $learner): 
                                // Calculate age from date of birth
                                $age = '';
                                if ($learner->date_of_birth) {
                                    $dob = new DateTime($learner->date_of_birth);
                                    $now = new DateTime();
                                    $age = $dob->diff($now)->y;
                                }
                            ?>
                                <tr>
                                    <td><?php echo $counter++; ?></td>
                                    <td>
                                        <?php if ($learner->lrn): ?>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($learner->lrn); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">Not assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($learner->last_name); ?>, <?php echo htmlspecialchars($learner->first_name); ?></strong>
                                        <?php if ($learner->middle_name): ?>
                                            <span class="text-muted"><?php echo htmlspecialchars(substr($learner->middle_name, 0, 1)); ?>.</span>
                                        <?php endif; ?>
                                        <?php if ($learner->suffix): ?>
                                            <span class="text-muted"><?php echo htmlspecialchars($learner->suffix); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $learner->date_of_birth ? date('M j, Y', strtotime($learner->date_of_birth)) : '<span class="text-muted">N/A</span>'; ?></td>
                                    <td><?php echo $age ? $age . ' years' : '<span class="text-muted">N/A</span>'; ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($learner->grade_level ?? 'N/A'); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($learner->parent_name ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if ($learner->parent_email): ?>
                                            <small><?php echo htmlspecialchars($learner->parent_email); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted small">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Enrolled
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?php echo URLROOT; ?>/learner/viewProfile?id=<?php echo $learner->id; ?>" 
                                               class="btn btn-outline-primary" 
                                               title="View Profile">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?php echo URLROOT; ?>/assessment/create?learner_id=<?php echo $learner->id; ?>" 
                                               class="btn btn-outline-secondary" 
                                               title="Assessment">
                                                <i class="bi bi-clipboard-check"></i>
                                            </a>
                                            <a href="<?php echo URLROOT; ?>/iep/create?learner_id=<?php echo $learner->id; ?>" 
                                               class="btn btn-outline-success" 
                                               title="Create IEP">
                                                <i class="bi bi-file-earmark-medical"></i>
                                            </a>
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

<!-- Print Styles -->
<style media="print">
    .sidebar, .navbar, .btn, .page-header p, .stat-card {
        display: none !important;
    }
    
    .page-header h1 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .card {
        border: none;
        box-shadow: none;
    }
    
    .table {
        font-size: 0.85rem;
    }
    
    .table thead {
        background-color: #1e4072 !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
</style>

</body>
</html>
