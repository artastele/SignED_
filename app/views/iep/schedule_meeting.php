<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/partials/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1>
            <i class="bi bi-calendar-event me-2"></i>
            Schedule IEP Meeting
        </h1>
        <p class="mb-0">Schedule meeting for <?php echo htmlspecialchars($data['iep']->first_name . ' ' . $data['iep']->last_name); ?></p>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($data['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo htmlspecialchars($data['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Meeting Scheduling Form -->
    <form method="POST" action="<?php echo URLROOT; ?>/iep/scheduleMeeting?iep_id=<?php echo $data['iep']->id; ?>">
        
        <!-- Learner Information -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-badge me-2"></i>
                    Learner Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($data['iep']->first_name . ' ' . ($data['iep']->middle_name ?? '') . ' ' . $data['iep']->last_name . ' ' . ($data['iep']->suffix ?? '')); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">LRN</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($data['iep']->lrn ?? 'N/A'); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Grade Level</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($data['iep']->grade_level ?? 'N/A'); ?>" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meeting Details -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-check me-2"></i>
                    Meeting Details
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Important:</strong> Minimum 3 days notice required. Meeting date must be at least 3 days from today.
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Meeting Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="meeting_date" id="meeting_date" required 
                               min="<?php echo date('Y-m-d', strtotime('+3 days')); ?>">
                        <small class="text-muted">Minimum 3 days from today</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Meeting Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" name="meeting_time" required>
                        <small class="text-muted">Select meeting time</small>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <label class="form-label fw-bold">Location <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="location" required placeholder="e.g. Conference Room, SPED Office">
                        <small class="text-muted">Where will the meeting take place?</small>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <label class="form-label fw-bold">Agenda</label>
                        <textarea class="form-control" name="agenda" rows="4" placeholder="Meeting agenda (optional)&#10;&#10;Example:&#10;1. Review IEP goals&#10;2. Discuss services and accommodations&#10;3. Parent/Guardian input&#10;4. Next steps"></textarea>
                        <small class="text-muted">Optional: Outline what will be discussed</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meeting Participants -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="bi bi-people me-2"></i>
                    Meeting Participants
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Select participants who will attend the IEP meeting. Required participants are pre-selected.
                </p>

                <!-- Required Participants -->
                <h6 class="fw-bold mb-3">Required Participants</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card border-danger">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="participants[]" value="parent" checked disabled>
                                    <input type="hidden" name="participants[]" value="parent">
                                    <label class="form-check-label fw-bold">
                                        <i class="bi bi-person-check text-danger me-2"></i>
                                        Parent/Guardian
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <?php echo htmlspecialchars($data['iep']->parent_name ?? 'Parent/Guardian'); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-danger">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="participants[]" value="sped_teacher" checked disabled>
                                    <input type="hidden" name="participants[]" value="sped_teacher">
                                    <label class="form-check-label fw-bold">
                                        <i class="bi bi-person-badge text-danger me-2"></i>
                                        SPED Teacher
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <?php echo htmlspecialchars($data['user_name']); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-danger">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="participants[]" value="guidance" checked disabled>
                                    <input type="hidden" name="participants[]" value="guidance">
                                    <label class="form-check-label fw-bold">
                                        <i class="bi bi-person-heart text-danger me-2"></i>
                                        Guidance Counselor
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">Required for behavioral insights</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-danger">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="participants[]" value="principal" checked disabled>
                                    <input type="hidden" name="participants[]" value="principal">
                                    <label class="form-check-label fw-bold">
                                        <i class="bi bi-person-badge-fill text-danger me-2"></i>
                                        Principal/Administrator
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">Required for approval</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Optional Participants -->
                <h6 class="fw-bold mb-3">Optional Participants</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border-secondary">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="participants[]" value="gen_ed_teacher" id="gen_ed_teacher">
                                    <label class="form-check-label" for="gen_ed_teacher">
                                        <i class="bi bi-person text-secondary me-2"></i>
                                        General Education Teacher
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">Optional: For classroom integration insights</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-secondary">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="participants[]" value="specialist" id="specialist">
                                    <label class="form-check-label" for="specialist">
                                        <i class="bi bi-person-plus text-secondary me-2"></i>
                                        Other Specialist
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">Optional: Speech therapist, OT, etc.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <i class="bi bi-bell me-2"></i>
                    <strong>Note:</strong> All selected participants will receive email invitations and must confirm their attendance.
                </div>
            </div>
        </div>

        <!-- Parent/Guardian Information -->
        <?php if (isset($data['iep']->parent_name)): ?>
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="bi bi-people me-2"></i>
                    Parent/Guardian Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Parent/Guardian Name</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($data['iep']->parent_name ?? 'N/A'); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($data['iep']->parent_email ?? 'N/A'); ?>" readonly>
                    </div>
                </div>
                <div class="alert alert-info mt-3 mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Note:</strong> A notification will be sent to the parent/guardian once the meeting is scheduled.
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?php echo URLROOT; ?>/iep/list" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-calendar-check me-1"></i> Schedule Meeting
                    </button>
                </div>
            </div>
        </div>

    </form>

</main>

<script>
// Set minimum date to 3 days from today
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.querySelector('input[name="meeting_date"]');
    const today = new Date();
    today.setDate(today.getDate() + 3); // Add 3 days
    const minDate = today.toISOString().split('T')[0];
    dateInput.setAttribute('min', minDate);
    
    // Validate date on change
    dateInput.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const minDateObj = new Date(minDate);
        
        if (selectedDate < minDateObj) {
            alert('Meeting date must be at least 3 days from today');
            this.value = '';
        }
    });
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
