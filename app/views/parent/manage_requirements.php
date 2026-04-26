<?php require_once '../app/views/layouts/header.php'; ?>

<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="bi bi-file-earmark-text text-primary me-2"></i>
            Manage Requirements
        </h1>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <?php
            switch ($_GET['success']) {
                case 'uploaded':
                    echo 'Document uploaded successfully!';
                    break;
                case 'deleted':
                    echo 'Document deleted successfully!';
                    break;
                default:
                    echo 'Operation completed successfully!';
            }
            ?>
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

    <?php if (empty($data['enrollments'])): ?>
        <!-- No Enrollments -->
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                <h4 class="text-muted">No Enrollments Yet</h4>
                <p class="text-muted mb-4">You haven't enrolled any children yet. Start by enrolling your child.</p>
                <a href="<?php echo URLROOT; ?>/enrollment/beef" class="btn btn-primary">
                    <i class="bi bi-person-plus me-2"></i>Enroll Child
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Enrollments List -->
        <?php foreach ($data['enrollments'] as $enrollment): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-person me-2"></i>
                            <?php echo htmlspecialchars($enrollment->learner_first_name . ' ' . $enrollment->learner_last_name); ?>
                        </h5>
                        <span class="badge <?php
                            switch ($enrollment->status) {
                                case 'approved':
                                    echo 'bg-success';
                                    break;
                                case 'pending_verification':
                                    echo 'bg-warning text-dark';
                                    break;
                                case 'pending_documents':
                                    echo 'bg-info text-dark';
                                    break;
                                case 'rejected':
                                    echo 'bg-danger';
                                    break;
                                default:
                                    echo 'bg-secondary';
                            }
                        ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $enrollment->status)); ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <small class="text-muted">Date of Birth</small>
                            <p class="mb-0"><?php echo date('F j, Y', strtotime($enrollment->learner_dob)); ?></p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Grade Level</small>
                            <p class="mb-0"><?php echo htmlspecialchars($enrollment->learner_grade); ?></p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Submitted</small>
                            <p class="mb-0"><?php echo date('F j, Y', strtotime($enrollment->created_at)); ?></p>
                        </div>
                    </div>

                    <?php if ($enrollment->status === 'rejected' && !empty($enrollment->rejection_reason)): ?>
                        <div class="alert alert-danger mb-3">
                            <strong>Rejection Reason:</strong><br>
                            <?php echo htmlspecialchars($enrollment->rejection_reason); ?>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <a href="<?php echo URLROOT; ?>/enrollment/view?id=<?php echo $enrollment->id; ?>" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye me-1"></i>View Details
                        </a>
                        
                        <?php if ($enrollment->status === 'pending_documents'): ?>
                            <a href="<?php echo URLROOT; ?>/enrollment/upload?id=<?php echo $enrollment->id; ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="bi bi-upload me-1"></i>Upload Documents
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
