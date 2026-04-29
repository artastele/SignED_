<?php require_once '../app/views/layouts/header.php'; ?>

<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">
                <i class="bi bi-person-badge text-primary me-2"></i>
                <?php echo htmlspecialchars($data['learner']->first_name . ' ' . $data['learner']->last_name); ?>
            </h1>
            <p class="text-muted mb-0">Learner Account Details</p>
        </div>
        <a href="<?php echo URLROOT; ?>/parent/children" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to My Children
        </a>
    </div>

    <!-- Learner Information Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Learner Information</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted small">Full Name</label>
                    <p class="fw-bold"><?php echo htmlspecialchars($data['learner']->first_name . ' ' . ($data['learner']->middle_name ?? '') . ' ' . $data['learner']->last_name . ' ' . ($data['learner']->suffix ?? '')); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small">Date of Birth</label>
                    <p class="fw-bold"><?php echo date('F j, Y', strtotime($data['learner']->date_of_birth)); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small">Grade Level</label>
                    <p class="fw-bold"><?php echo htmlspecialchars($data['learner']->grade_level ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Status</label>
                    <p>
                        <span class="badge <?php
                            switch ($data['learner']->status) {
                                case 'active':
                                    echo 'bg-success';
                                    break;
                                case 'enrolled':
                                    echo 'bg-info';
                                    break;
                                case 'assessment_pending':
                                case 'assessment_complete':
                                    echo 'bg-warning text-dark';
                                    break;
                                case 'iep_approved':
                                    echo 'bg-success';
                                    break;
                                default:
                                    echo 'bg-secondary';
                            }
                        ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $data['learner']->status)); ?>
                        </span>
                    </p>
                </div>
                <?php if (!empty($data['learner']->disability_type)): ?>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Disability Type</label>
                    <p class="fw-bold"><?php echo htmlspecialchars($data['learner']->disability_type); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Account Credentials Card -->
    <?php if (!empty($data['learner']->lrn)): ?>
    <div class="card mb-4 border-success">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-key me-2"></i>Account Credentials</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Important:</strong> Your child can use these credentials to log in to the learner portal.
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted small">Learner Reference Number (LRN)</label>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-lg fw-bold" value="<?php echo htmlspecialchars($data['learner']->lrn); ?>" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('<?php echo $data['learner']->lrn; ?>')">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                    <small class="text-muted">This is the username for logging in</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Default Password</label>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-lg" value="default123" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('default123')">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                    <small class="text-danger">⚠️ Please change this password after first login</small>
                </div>
            </div>

            <hr class="my-4">

            <div class="bg-light p-3 rounded">
                <h6 class="mb-3"><i class="bi bi-shield-check me-2"></i>Security Instructions</h6>
                <ol class="mb-0">
                    <li class="mb-2">Use the LRN as the username when logging in</li>
                    <li class="mb-2">Use "default123" as the initial password</li>
                    <li class="mb-2"><strong>Change the password immediately after first login</strong></li>
                    <li class="mb-0">Keep the credentials secure and do not share with others</li>
                </ol>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Enrollment Details Card -->
    <?php if ($data['enrollment']): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-file-earmark-check me-2"></i>Enrollment Details</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label text-muted small">Enrollment Status</label>
                    <p>
                        <span class="badge <?php
                            switch ($data['enrollment']->status) {
                                case 'approved':
                                    echo 'bg-success';
                                    break;
                                case 'pending_verification':
                                    echo 'bg-warning text-dark';
                                    break;
                                case 'rejected':
                                    echo 'bg-danger';
                                    break;
                                default:
                                    echo 'bg-secondary';
                            }
                        ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $data['enrollment']->status)); ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small">Submitted Date</label>
                    <p class="fw-bold"><?php echo date('F j, Y g:i A', strtotime($data['enrollment']->created_at)); ?></p>
                </div>
                <?php if ($data['enrollment']->status === 'approved' && $data['enrollment']->verified_at): ?>
                <div class="col-md-4">
                    <label class="form-label text-muted small">Approved Date</label>
                    <p class="fw-bold"><?php echo date('F j, Y g:i A', strtotime($data['enrollment']->verified_at)); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</main>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Copied to clipboard: ' + text);
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
