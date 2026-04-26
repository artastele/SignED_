<?php require_once '../app/views/layouts/header.php'; ?>

<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="bi bi-people text-primary me-2"></i>
            My Children
        </h1>
        <a href="<?php echo URLROOT; ?>/enrollment/beef" class="btn btn-primary">
            <i class="bi bi-person-plus me-2"></i>Enroll New Child
        </a>
    </div>

    <?php if (empty($data['learners'])): ?>
        <!-- No Children -->
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-people display-1 text-muted mb-3"></i>
                <h4 class="text-muted">No Enrolled Children</h4>
                <p class="text-muted mb-4">You don't have any enrolled children yet. Start by enrolling your child in the SPED program.</p>
                <a href="<?php echo URLROOT; ?>/enrollment/beef" class="btn btn-primary">
                    <i class="bi bi-person-plus me-2"></i>Enroll Child
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Children List -->
        <div class="row">
            <?php foreach ($data['learners'] as $learner): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-circle me-3">
                                    <?php echo strtoupper(substr($learner->first_name, 0, 1)); ?>
                                </div>
                                <div>
                                    <h5 class="mb-0"><?php echo htmlspecialchars($learner->first_name . ' ' . $learner->last_name); ?></h5>
                                    <small class="text-muted">Grade <?php echo htmlspecialchars($learner->grade_level ?? 'N/A'); ?></small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <span class="badge <?php
                                    switch ($learner->status) {
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
                                    <?php echo ucfirst(str_replace('_', ' ', $learner->status)); ?>
                                </span>
                            </div>

                            <hr>

                            <div class="small mb-2">
                                <i class="bi bi-calendar me-2 text-muted"></i>
                                <strong>DOB:</strong> <?php echo date('M j, Y', strtotime($learner->date_of_birth)); ?>
                            </div>

                            <?php if (!empty($learner->disability_type)): ?>
                                <div class="small mb-2">
                                    <i class="bi bi-info-circle me-2 text-muted"></i>
                                    <strong>Type:</strong> <?php echo htmlspecialchars($learner->disability_type); ?>
                                </div>
                            <?php endif; ?>

                            <div class="small mb-3">
                                <i class="bi bi-clock me-2 text-muted"></i>
                                <strong>Enrolled:</strong> <?php echo date('M j, Y', strtotime($learner->created_at)); ?>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="<?php echo URLROOT; ?>/learner/view/<?php echo $learner->id; ?>" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
