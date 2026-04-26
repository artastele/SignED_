<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">
                <i class="bi bi-file-earmark-medical me-2 text-brand-red"></i>
                <?php 
                if ($data['role'] === 'principal') {
                    echo 'IEPs Pending Approval';
                } else {
                    echo 'IEP Documents';
                }
                ?>
            </h1>
            <p class="text-muted mb-0">
                <?php 
                if ($data['role'] === 'principal') {
                    echo 'Review and approve Individualized Education Plans';
                } else {
                    echo 'Manage Individualized Education Plans for SPED learners';
                }
                ?>
            </p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/<?php echo $data['role'] === 'admin' ? 'admin' : 'sped'; ?>/dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>
    </div>
    
    <!-- Alerts -->
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Error:</strong> 
            <?php 
            switch ($_GET['error']) {
                case 'invalid_iep': echo 'Invalid IEP ID provided.'; break;
                case 'iep_not_found': echo 'IEP document not found.'; break;
                case 'iep_not_ready': echo 'IEP is not ready for this action.'; break;
                case 'invalid_learner': echo 'Invalid learner ID provided.'; break;
                case 'learner_not_ready': echo 'Learner is not ready for IEP creation.'; break;
                default: echo htmlspecialchars($_GET['error']);
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Success:</strong> 
            <?php 
            switch ($_GET['success']) {
                case 'created': echo 'IEP has been created successfully.'; break;
                case 'approved': echo 'IEP has been approved successfully.'; break;
                case 'rejected': echo 'IEP has been sent back for revision.'; break;
                default: echo htmlspecialchars($_GET['success']);
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Statistics Card -->
    <?php if ($data['role'] === 'principal' && !empty($data['ieps'])): ?>
        <div class="alert alert-warning mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Action Required:</strong> <?php echo count($data['ieps']); ?> IEP document(s) require your approval.
        </div>
    <?php endif; ?>
    
    <!-- Filter (for SPED teachers) -->
    <?php if ($data['role'] === 'sped_teacher'): ?>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label mb-0">
                            <i class="bi bi-funnel me-1"></i>Filter by Status:
                        </label>
                    </div>
                    <div class="col-md-9">
                        <select id="statusFilter" class="form-select" onchange="filterIeps()">
                            <option value="">All IEPs</option>
                            <option value="draft">Draft</option>
                            <option value="pending_approval">Pending Approval</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="active">Active</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- IEP List -->
    <?php if (!empty($data['ieps'])): ?>
        <div class="row g-4">
            <?php foreach ($data['ieps'] as $iep): ?>
                <div class="col-12" data-status="<?php echo $iep->status; ?>">
                    <div class="card shadow-sm <?php echo ($data['role'] === 'principal' && $iep->status === 'pending_approval') ? 'border-warning border-2' : ''; ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">
                                        IEP - <?php echo htmlspecialchars($iep->first_name . ' ' . $iep->last_name); ?>
                                    </h5>
                                    <span class="badge 
                                        <?php 
                                        switch($iep->status) {
                                            case 'draft': echo 'bg-secondary'; break;
                                            case 'pending_approval': echo 'bg-warning text-dark'; break;
                                            case 'approved': echo 'bg-success'; break;
                                            case 'rejected': echo 'bg-danger'; break;
                                            case 'active': echo 'bg-primary'; break;
                                            case 'expired': echo 'bg-secondary'; break;
                                            default: echo 'bg-secondary';
                                        }
                                        ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $iep->status)); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <small class="text-muted d-block">
                                        <i class="bi bi-calendar3 me-1"></i>Created
                                    </small>
                                    <strong><?php echo date('M j, Y', strtotime($iep->created_at)); ?></strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">
                                        <i class="bi bi-person me-1"></i>Created by
                                    </small>
                                    <strong><?php echo htmlspecialchars($iep->created_by_name); ?></strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">
                                        <i class="bi bi-book me-1"></i>Grade
                                    </small>
                                    <strong><?php echo htmlspecialchars($iep->grade_level ?? 'N/A'); ?></strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">
                                        <i class="bi bi-clock me-1"></i>Duration
                                    </small>
                                    <strong>
                                        <?php echo date('M j, Y', strtotime($iep->start_date)); ?> - 
                                        <?php echo date('M j, Y', strtotime($iep->end_date)); ?>
                                    </strong>
                                </div>
                            </div>
                            
                            <?php if ($iep->status === 'rejected' && !empty($iep->rejection_reason)): ?>
                                <div class="alert alert-danger mb-3">
                                    <strong><i class="bi bi-x-circle me-1"></i>Rejection Reason:</strong> 
                                    <?php echo htmlspecialchars($iep->rejection_reason); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($iep->status === 'approved' && !empty($iep->approved_by_name)): ?>
                                <div class="alert alert-success mb-3">
                                    <strong><i class="bi bi-check-circle me-1"></i>Approved by:</strong> 
                                    <?php echo htmlspecialchars($iep->approved_by_name); ?> 
                                    on <?php echo date('M j, Y \a\t g:i A', strtotime($iep->approved_at)); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="<?php echo URLROOT; ?>/iep/viewIep?id=<?php echo $iep->id; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>View Details
                                </a>
                                
                                <?php if ($data['role'] === 'principal' && $iep->status === 'pending_approval'): ?>
                                    <a href="<?php echo URLROOT; ?>/iep/approve?id=<?php echo $iep->id; ?>" class="btn btn-sm btn-success">
                                        <i class="bi bi-check-circle me-1"></i>Approve
                                    </a>
                                    <a href="<?php echo URLROOT; ?>/iep/reject?id=<?php echo $iep->id; ?>" class="btn btn-sm btn-danger">
                                        <i class="bi bi-x-circle me-1"></i>Request Revision
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($data['role'] === 'sped_teacher'): ?>
                                    <?php if ($iep->status === 'draft'): ?>
                                        <a href="<?php echo URLROOT; ?>/iep/edit?id=<?php echo $iep->id; ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil me-1"></i>Continue Editing
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/iep/submit?id=<?php echo $iep->id; ?>" class="btn btn-sm btn-success">
                                            <i class="bi bi-send me-1"></i>Submit for Approval
                                        </a>
                                    <?php elseif ($iep->status === 'rejected'): ?>
                                        <a href="<?php echo URLROOT; ?>/iep/edit?id=<?php echo $iep->id; ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil me-1"></i>Revise IEP
                                        </a>
                                    <?php elseif ($iep->status === 'approved'): ?>
                                        <a href="<?php echo URLROOT; ?>/iep/activate?id=<?php echo $iep->id; ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-play-circle me-1"></i>Activate IEP
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
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
                <h5 class="text-muted">No IEP Documents Found</h5>
                <p class="text-muted mb-0">
                    <?php 
                    if ($data['role'] === 'principal') {
                        echo 'No IEPs are currently pending your approval.';
                    } else {
                        echo 'No IEP documents have been created yet.';
                    }
                    ?>
                </p>
            </div>
        </div>
    <?php endif; ?>
    
</main>

<script>
function filterIeps() {
    const filter = document.getElementById('statusFilter').value;
    const cards = document.querySelectorAll('[data-status]');
    
    cards.forEach(card => {
        const status = card.dataset.status;
        if (filter === '' || status === filter) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
