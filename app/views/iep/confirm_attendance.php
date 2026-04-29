<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/partials/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1>
            <i class="bi bi-calendar-check me-2"></i>
            Confirm Meeting Attendance
        </h1>
        <p class="mb-0">IEP Meeting Invitation</p>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($data['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo htmlspecialchars($data['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Meeting Information -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-info-circle me-2"></i>
                Meeting Information
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Learner</label>
                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($data['participant']->first_name . ' ' . $data['participant']->last_name); ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Your Role</label>
                    <input type="text" class="form-control bg-light" value="<?php echo ucwords(str_replace('_', ' ', $data['participant']->participant_type)); ?>" readonly>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Meeting Date</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                        <input type="text" class="form-control bg-light" value="<?php echo date('F j, Y', strtotime($data['participant']->meeting_date)); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Meeting Time</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-clock"></i></span>
                        <input type="text" class="form-control bg-light" value="<?php echo date('g:i A', strtotime($data['participant']->meeting_time)); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Location</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($data['participant']->location); ?>" readonly>
                    </div>
                </div>
            </div>

            <?php if ($data['participant']->is_required): ?>
            <div class="alert alert-warning mt-3 mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Required Participant:</strong> Your attendance is required for this meeting.
                <?php if ($data['participant']->participant_type === 'parent'): ?>
                <br><small>If you cannot attend, the meeting will need to be rescheduled.</small>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Current Status -->
    <div class="card mb-4">
        <div class="card-header bg-<?php 
            echo $data['participant']->invitation_status === 'confirmed' ? 'success' : 
                ($data['participant']->invitation_status === 'declined' ? 'danger' : 'warning'); 
        ?> text-white">
            <h5 class="mb-0">
                <i class="bi bi-<?php 
                    echo $data['participant']->invitation_status === 'confirmed' ? 'check-circle' : 
                        ($data['participant']->invitation_status === 'declined' ? 'x-circle' : 'hourglass-split'); 
                ?> me-2"></i>
                Current Status: <?php echo ucfirst($data['participant']->invitation_status); ?>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($data['participant']->invitation_status === 'confirmed'): ?>
                <p class="mb-0">
                    <i class="bi bi-check-circle text-success me-2"></i>
                    You have confirmed your attendance. Thank you!
                </p>
                <?php if ($data['participant']->confirmed_at): ?>
                <small class="text-muted">
                    Confirmed on <?php echo date('M j, Y g:i A', strtotime($data['participant']->confirmed_at)); ?>
                </small>
                <?php endif; ?>
            <?php elseif ($data['participant']->invitation_status === 'declined'): ?>
                <p class="mb-2">
                    <i class="bi bi-x-circle text-danger me-2"></i>
                    You have declined this invitation.
                </p>
                <?php if ($data['participant']->decline_reason): ?>
                <p class="mb-0"><strong>Reason:</strong> <?php echo htmlspecialchars($data['participant']->decline_reason); ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p class="mb-0">
                    <i class="bi bi-hourglass-split text-warning me-2"></i>
                    Your response is pending. Please confirm or decline below.
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Confirmation Form -->
    <?php if ($data['participant']->invitation_status === 'pending'): ?>
    <div class="row g-3">
        <!-- Confirm -->
        <div class="col-md-6">
            <form method="POST" action="<?php echo URLROOT; ?>/iep/confirmAttendance?participant_id=<?php echo $data['participant']->id; ?>">
                <input type="hidden" name="action" value="confirm">
                <div class="card h-100 border-success">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle fs-1 text-success mb-3"></i>
                        <h5 class="card-title">Confirm Attendance</h5>
                        <p class="card-text">I will attend this meeting</p>
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-check-circle me-2"></i> Confirm
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Decline -->
        <div class="col-md-6">
            <div class="card h-100 border-danger">
                <div class="card-body text-center">
                    <i class="bi bi-x-circle fs-1 text-danger mb-3"></i>
                    <h5 class="card-title">Decline Invitation</h5>
                    <p class="card-text">I cannot attend this meeting</p>
                    <button type="button" class="btn btn-danger btn-lg w-100" data-bs-toggle="modal" data-bs-target="#declineModal">
                        <i class="bi bi-x-circle me-2"></i> Decline
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Back Button -->
    <div class="card">
        <div class="card-body">
            <a href="<?php echo URLROOT; ?>/<?php echo $data['role']; ?>/dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>
    <?php endif; ?>

</main>

<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo URLROOT; ?>/iep/confirmAttendance?participant_id=<?php echo $data['participant']->id; ?>">
                <input type="hidden" name="action" value="decline">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-x-circle me-2"></i>
                        Decline Meeting Invitation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if ($data['participant']->participant_type === 'parent'): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> As a parent/guardian, your attendance is required. 
                        If you decline, the meeting will need to be rescheduled.
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="decline_reason" class="form-label fw-bold">
                            Reason for Declining <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="decline_reason" name="decline_reason" rows="4" required 
                                  placeholder="Please provide a reason for declining this invitation..."></textarea>
                        <small class="text-muted">This will help us reschedule the meeting</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i> Decline Invitation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
