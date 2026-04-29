<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/partials/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1>
            <i class="bi bi-file-text me-2"></i>
            Record Meeting Notes
        </h1>
        <p class="mb-0">IEP Meeting for <?php echo htmlspecialchars($data['meeting']->first_name . ' ' . $data['meeting']->last_name); ?></p>
    </div>

    <!-- Success/Error Messages -->
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
                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($data['meeting']->first_name . ' ' . $data['meeting']->last_name); ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Meeting Date</label>
                    <input type="text" class="form-control bg-light" value="<?php echo date('M j, Y', strtotime($data['meeting']->meeting_date)); ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Meeting Time</label>
                    <input type="text" class="form-control bg-light" value="<?php echo date('g:i A', strtotime($data['meeting']->meeting_time)); ?>" readonly>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-12">
                    <label class="form-label fw-bold">Location</label>
                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($data['meeting']->location); ?>" readonly>
                </div>
            </div>
        </div>
    </div>

    <!-- Participants -->
    <?php if (!empty($data['participants'])): ?>
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="bi bi-people me-2"></i>
                Meeting Participants
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['participants'] as $participant): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($participant->name); ?></td>
                            <td><?php echo ucwords(str_replace('_', ' ', $participant->participant_type)); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $participant->invitation_status === 'confirmed' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($participant->invitation_status); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Record Meeting Form -->
    <form method="POST" action="<?php echo URLROOT; ?>/iep/recordMeeting?meeting_id=<?php echo $data['meeting']->id; ?>">
        
        <!-- Meeting Notes -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-journal-text me-2"></i>
                    Meeting Notes
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="meeting_notes" class="form-label fw-bold">
                        Meeting Notes <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="meeting_notes" name="meeting_notes" rows="8" required 
                              placeholder="Record what was discussed during the meeting...&#10;&#10;Example:&#10;- Reviewed learner's current progress&#10;- Discussed IEP goals and objectives&#10;- Parent shared concerns about...&#10;- Team agreed on..."></textarea>
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Document key discussion points, concerns raised, and observations
                    </small>
                </div>
            </div>
        </div>

        <!-- Decisions Made -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-check2-square me-2"></i>
                    Decisions Made
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="decisions" class="form-label fw-bold">
                        Decisions & Action Items <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="decisions" name="decisions" rows="6" required 
                              placeholder="Record decisions made and action items...&#10;&#10;Example:&#10;- Approved IEP goals as presented&#10;- Modified service frequency to 3x per week&#10;- Parent requested additional speech therapy&#10;- Next review scheduled for..."></textarea>
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Document all decisions, modifications, and next steps
                    </small>
                </div>
            </div>
        </div>

        <!-- Feedback Incorporated -->
        <?php if (!empty($data['feedback'])): ?>
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-chat-left-text me-2"></i>
                    Feedback from Review
                </h5>
            </div>
            <div class="card-body">
                <?php foreach ($data['feedback'] as $fb): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong><?php echo htmlspecialchars($fb->user_name); ?></strong>
                                <span class="badge bg-<?php echo $fb->user_role === 'guidance' ? 'success' : 'primary'; ?> ms-2">
                                    <?php echo ucfirst($fb->user_role); ?>
                                </span>
                            </div>
                            <small class="text-muted">
                                <?php echo date('M j, Y', strtotime($fb->created_at)); ?>
                            </small>
                        </div>
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($fb->feedback)); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Ensure these feedback points were addressed during the meeting
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?php echo URLROOT; ?>/iep/meetings" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Meetings
                    </a>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-check-circle me-1"></i> Complete Meeting
                    </button>
                </div>
            </div>
        </div>

    </form>

</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
