<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/partials/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1>
            <i class="bi bi-file-earmark-text me-2"></i>
            Review IEP Draft
        </h1>
        <p class="mb-0">Review IEP draft for <?php echo htmlspecialchars($data['iep']->first_name . ' ' . $data['iep']->last_name); ?></p>
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

    <!-- IEP Draft Document -->
    <?php if ($data['iep']->draft_document_id): ?>
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="bi bi-file-earmark-pdf me-2"></i>
                IEP Draft Document
            </h5>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-file-earmark-pdf fs-1 text-danger me-3"></i>
                    <div>
                        <strong>IEP Draft Document</strong>
                        <br>
                        <small class="text-muted">Uploaded by SPED Teacher</small>
                    </div>
                </div>
                <a href="<?php echo URLROOT; ?>/document/view?id=<?php echo $data['iep']->draft_document_id; ?>" 
                   class="btn btn-primary" target="_blank">
                    <i class="bi bi-eye me-1"></i> View Document
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- IEP Draft Data Summary -->
    <?php
    $draftData = json_decode($data['iep']->draft_data, true);
    ?>
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="bi bi-list-check me-2"></i>
                IEP Draft Summary
            </h5>
        </div>
        <div class="card-body">
            <!-- Goals -->
            <?php if (!empty($draftData['goals'])): ?>
            <h6 class="fw-bold mb-3">Goals (<?php echo count($draftData['goals']); ?>)</h6>
            <div class="table-responsive mb-4">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Domain</th>
                            <th>Skill</th>
                            <th>Description</th>
                            <th>Performance Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($draftData['goals'] as $goal): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($goal['domain']); ?></td>
                            <td><?php echo htmlspecialchars($goal['skill']); ?></td>
                            <td><?php echo htmlspecialchars($goal['description']); ?></td>
                            <td><?php echo htmlspecialchars($goal['performance_level']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Services -->
            <?php if (!empty($draftData['services'])): ?>
            <h6 class="fw-bold mb-3">Services (<?php echo count($draftData['services']); ?>)</h6>
            <div class="table-responsive mb-4">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Service Type</th>
                            <th>Provider</th>
                            <th>Frequency</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($draftData['services'] as $service): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($service['service_type']); ?></td>
                            <td><?php echo htmlspecialchars($service['provider'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($service['frequency'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($service['duration'] ?? 'N/A'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Accommodations -->
            <?php if (!empty($draftData['accommodations'])): ?>
            <h6 class="fw-bold mb-3">Accommodations (<?php echo count($draftData['accommodations']); ?>)</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($draftData['accommodations'] as $accommodation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($accommodation['accommodation_type']); ?></td>
                            <td><?php echo htmlspecialchars($accommodation['description']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Existing Feedback -->
    <?php if (!empty($data['existing_feedback'])): ?>
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="bi bi-chat-left-text me-2"></i>
                Previous Feedback
            </h5>
        </div>
        <div class="card-body">
            <?php foreach ($data['existing_feedback'] as $feedback): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong><?php echo htmlspecialchars($feedback->user_name); ?></strong>
                            <span class="badge bg-<?php echo $feedback->user_role === 'guidance' ? 'success' : 'primary'; ?> ms-2">
                                <?php echo ucfirst($feedback->user_role); ?>
                            </span>
                        </div>
                        <small class="text-muted">
                            <?php echo date('M j, Y g:i A', strtotime($feedback->created_at)); ?>
                        </small>
                    </div>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($feedback->feedback)); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Add Feedback Form -->
    <form method="POST" action="<?php echo URLROOT; ?>/iep/reviewDraft?iep_id=<?php echo $data['iep']->id; ?>">
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-pencil-square me-2"></i>
                    Add Your Feedback
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="feedback" class="form-label fw-bold">
                        Feedback / Comments <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="feedback" name="feedback" rows="6" required 
                              placeholder="<?php 
                              if ($data['role'] === 'guidance') {
                                  echo 'Provide insights on behavioral needs, social-emotional development, and recommended support strategies...';
                              } else {
                                  echo 'Provide your review comments and recommendations...';
                              }
                              ?>"></textarea>
                    <small class="text-muted">
                        <?php if ($data['role'] === 'guidance'): ?>
                            <i class="bi bi-info-circle me-1"></i>
                            As Guidance Counselor, focus on behavioral insights and support strategies
                        <?php else: ?>
                            <i class="bi bi-info-circle me-1"></i>
                            Your feedback will help improve the IEP before the meeting
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?php echo URLROOT; ?>/<?php echo $data['role']; ?>/dashboard" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Submit Feedback
                    </button>
                </div>
            </div>
        </div>
    </form>

</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
