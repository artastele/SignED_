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
                Enrollment Verification
            </h1>
            <p class="text-muted mb-0">Review and verify pending SPED enrollment applications</p>
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
    
    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Pending Verification</div>
                            <div class="stat-number"><?php echo count($data['enrollments'] ?? []); ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Complete Applications</div>
                            <div class="stat-number">
                                <?php 
                                $completeCount = 0;
                                foreach (($data['enrollments'] ?? []) as $enrollment) {
                                    if ($enrollment->document_count >= 4) $completeCount++;
                                }
                                echo $completeCount;
                                ?>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Incomplete Applications</div>
                            <div class="stat-number"><?php echo count($data['enrollments'] ?? []) - $completeCount; ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-x-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Enrollments Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-list-check me-2"></i>
                Pending Enrollments
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($data['enrollments'])): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                    <h5 class="text-muted">No Pending Enrollments</h5>
                    <p class="text-muted">All enrollment applications have been processed.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student Name</th>
                                <th>Parent</th>
                                <th>Grade Level</th>
                                <th>Date of Birth</th>
                                <th>Documents</th>
                                <th>Submitted</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['enrollments'] as $enrollment): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($enrollment->learner_first_name . ' ' . $enrollment->learner_last_name); ?></strong>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($enrollment->parent_name); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($enrollment->parent_email); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($enrollment->learner_grade); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($enrollment->learner_dob)); ?></td>
                                    <td>
                                        <?php if ($enrollment->document_count >= 4): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i><?php echo $enrollment->document_count; ?>/4
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-exclamation-circle me-1"></i><?php echo $enrollment->document_count; ?>/4
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?php echo date('M j, Y', strtotime($enrollment->created_at)); ?></small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo URLROOT; ?>/enrollment/viewEnrollment?id=<?php echo $enrollment->id; ?>" 
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye me-1"></i>Review
                                            </a>
                                            
                                            <?php if ($enrollment->document_count >= 4): ?>
                                                <form method="POST" action="<?php echo URLROOT; ?>/enrollment/approve" style="display: inline;">
                                                    <input type="hidden" name="enrollment_id" value="<?php echo $enrollment->id; ?>">
                                                    <button type="submit" class="btn btn-sm btn-success" 
                                                            onclick="return confirm('Are you sure you want to approve this enrollment?')">
                                                        <i class="bi bi-check-circle me-1"></i>Approve
                                                    </button>
                                                </form>
                                                
                                                <button class="btn btn-sm btn-danger" 
                                                        onclick="showRejectModal(<?php echo $enrollment->id; ?>, '<?php echo htmlspecialchars($enrollment->learner_first_name . ' ' . $enrollment->learner_last_name); ?>')">
                                                    <i class="bi bi-x-circle me-1"></i>Reject
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary" disabled>
                                                    <i class="bi bi-hourglass-split me-1"></i>Incomplete
                                                </button>
                                            <?php endif; ?>
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

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="bi bi-x-circle me-2 text-danger"></i>
                    Reject Enrollment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form method="POST" action="<?php echo URLROOT; ?>/enrollment/reject" id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" name="enrollment_id" id="rejectEnrollmentId">
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        You are about to reject the enrollment for <strong id="rejectStudentName"></strong>.
                    </div>
                    
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">
                            Reason for Rejection <span class="text-danger">*</span>
                        </label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="4" required 
                                  placeholder="Please provide a detailed reason for rejecting this enrollment..."></textarea>
                        <div class="form-text">This reason will be sent to the parent via email.</div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>Reject Enrollment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRejectModal(enrollmentId, studentName) {
    document.getElementById('rejectEnrollmentId').value = enrollmentId;
    document.getElementById('rejectStudentName').textContent = studentName;
    document.getElementById('rejection_reason').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
