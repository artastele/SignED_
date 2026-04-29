<?php require_once '../app/views/layouts/header.php'; ?>

<style>
/* Progress Steps Styling */
.progress-steps {
    position: relative;
}

.progress-step {
    position: relative;
}

.step-number {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e5e7eb;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    flex-shrink: 0;
}

.step-number.active {
    background: #3b82f6;
    color: white;
}

.step-number.completed {
    background: #10b981;
    color: white;
}

.step-number.completed::after {
    content: '✓';
    position: absolute;
}

/* Checklist Item Styling */
.checklist-item {
    padding: 12px;
    transition: all 0.3s ease;
}

.checklist-item:hover {
    background-color: #f9fafb;
}

.checklist-item.checked {
    background-color: #f0fdf4;
}
</style>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1><i class="bi bi-house-door me-2"></i>Welcome, <?php echo htmlspecialchars($data['user_name']); ?>! 👋</h1>
        <p class="mb-0">Manage your child's SPED enrollment and track their progress</p>
    </div>
    
    <!-- Alerts -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <?php echo htmlspecialchars($_GET['success']); ?>
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
    
    <div class="row g-4">
        
        <!-- Left Column -->
        <div class="col-lg-8">
            
            <!-- Enrollment Checklist (Hide if approved) -->
            <?php 
            $hasApprovedEnrollment = false;
            if ($data['has_enrollments']) {
                foreach ($data['enrollments'] as $enrollment) {
                    if ($enrollment->status == 'approved') {
                        $hasApprovedEnrollment = true;
                        break;
                    }
                }
            }
            ?>
            
            <?php if (!$hasApprovedEnrollment): ?>
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-list-check me-2 text-brand-red"></i>
                    <h5 class="mb-0">Enrollment Requirements for SPED Education</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        Please prepare the following documents before starting the enrollment process:
                    </p>
                    
                    <div class="checklist-item rounded mb-2 <?php echo $data['has_enrollments'] ? 'checked' : ''; ?>">
                        <div class="d-flex align-items-start">
                            <div class="check-icon me-3 mt-1">
                                <i class="bi bi-<?php echo $data['has_enrollments'] ? 'check-circle-fill text-success' : 'circle'; ?>"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold <?php echo $data['has_enrollments'] ? 'text-decoration-line-through text-muted' : ''; ?>">BEEF Form (Basic Education Enrollment Form)</div>
                                <small class="text-muted">Complete the online enrollment form with learner and parent information</small>
                            </div>
                        </div>
                    </div>
                    
                    <?php
                    // Check if PSA is uploaded for any enrollment
                    $psaUploaded = false;
                    $pwdUploaded = false;
                    $medicalUploaded = false;
                    
                    if ($data['has_enrollments']) {
                        foreach ($data['enrollments'] as $enrollment) {
                            if ($enrollment->document_count > 0) {
                                // Check if documents exist
                                $enrollmentModel = new Enrollment();
                                $docs = $enrollmentModel->getDocuments($enrollment->id);
                                foreach ($docs as $doc) {
                                    if ($doc->document_type === 'psa') {
                                        $psaUploaded = true;
                                    } elseif ($doc->document_type === 'pwd_id') {
                                        $pwdUploaded = true;
                                    } elseif ($doc->document_type === 'medical_record') {
                                        $medicalUploaded = true;
                                    }
                                }
                            }
                        }
                    }
                    ?>
                    
                    <div class="checklist-item rounded mb-2 <?php echo $psaUploaded ? 'checked' : ''; ?>">
                        <div class="d-flex align-items-start">
                            <div class="check-icon me-3 mt-1">
                                <i class="bi bi-<?php echo $psaUploaded ? 'check-circle-fill text-success' : 'circle'; ?>"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold <?php echo $psaUploaded ? 'text-decoration-line-through text-muted' : ''; ?>">PSA Birth Certificate <span class="text-danger">*</span></div>
                                <small class="text-muted">
                                    <?php if ($psaUploaded): ?>
                                        ✅ Uploaded - Original or certified true copy from PSA
                                    <?php else: ?>
                                        Required - Original or certified true copy from PSA
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="checklist-item rounded mb-2 <?php echo $pwdUploaded ? 'checked' : ''; ?>">
                        <div class="d-flex align-items-start">
                            <div class="check-icon me-3 mt-1">
                                <i class="bi bi-<?php echo $pwdUploaded ? 'check-circle-fill text-success' : 'circle'; ?>"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold <?php echo $pwdUploaded ? 'text-decoration-line-through text-muted' : ''; ?>">PWD ID Card</div>
                                <small class="text-muted">
                                    <?php if ($pwdUploaded): ?>
                                        ✅ Uploaded - Person with Disability identification card
                                    <?php else: ?>
                                        If available - Person with Disability identification card
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="checklist-item rounded mb-4 <?php echo $medicalUploaded ? 'checked' : ''; ?>">
                        <div class="d-flex align-items-start">
                            <div class="check-icon me-3 mt-1">
                                <i class="bi bi-<?php echo $medicalUploaded ? 'check-circle-fill text-success' : 'circle'; ?>"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold <?php echo $medicalUploaded ? 'text-decoration-line-through text-muted' : ''; ?>">Medical Records</div>
                                <small class="text-muted">
                                    <?php if ($medicalUploaded): ?>
                                        ✅ Uploaded - Recent medical assessment or diagnosis reports
                                    <?php else: ?>
                                        If available - Recent medical assessment or diagnosis reports
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!$data['has_enrollments']): ?>
                        <div class="d-grid gap-2">
                            <a href="<?php echo URLROOT; ?>/enrollment/beef" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle me-2"></i>
                                Start Enrollment Process
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="d-grid gap-2">
                            <a href="<?php echo URLROOT; ?>/parent/manageRequirements" class="btn btn-secondary btn-lg">
                                <i class="bi bi-folder me-2"></i>
                                Manage Requirements
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Enrollment Progress Tracker (shows after BEEF submission) -->
            <?php if ($data['has_enrollments']): ?>
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="bi bi-graph-up me-2 text-brand-red"></i>
                        <h5 class="mb-0">Enrollment Progress</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($data['enrollments'] as $enrollment): ?>
                            <div class="mb-4 pb-4 <?php echo $enrollment !== end($data['enrollments']) ? 'border-bottom' : ''; ?>">
                                <h6 class="mb-3 text-brand-blue">
                                    <i class="bi bi-person me-2"></i>
                                    <?php echo htmlspecialchars($enrollment->learner_first_name . ' ' . $enrollment->learner_last_name); ?>
                                </h6>
                                
                                <!-- Progress Steps -->
                                <div class="progress-steps">
                                    <!-- Step 1: BEEF Form -->
                                    <div class="progress-step mb-3">
                                        <div class="d-flex align-items-start">
                                            <div class="step-number <?php echo in_array($enrollment->status, ['pending_documents', 'pending_verification', 'approved', 'rejected']) ? 'completed' : ''; ?> me-3">
                                                1
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">BEEF Form Submitted</div>
                                                <small class="text-muted">Basic enrollment information completed</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Step 2: Upload Documents -->
                                    <div class="progress-step mb-3">
                                        <div class="d-flex align-items-start">
                                            <div class="step-number <?php echo in_array($enrollment->status, ['pending_verification', 'approved', 'rejected']) ? 'completed' : ($enrollment->status == 'pending_documents' ? 'active' : ''); ?> me-3">
                                                2
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">Upload Requirements</div>
                                                <small class="text-muted">
                                                    PSA Birth Certificate <?php echo $enrollment->document_count > 0 ? 'uploaded' : 'required'; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Step 3: SPED Verification -->
                                    <div class="progress-step mb-3">
                                        <div class="d-flex align-items-start">
                                            <div class="step-number <?php echo in_array($enrollment->status, ['approved', 'rejected']) ? 'completed' : ($enrollment->status == 'pending_verification' ? 'active' : ''); ?> me-3">
                                                3
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">SPED Verification</div>
                                                <small class="text-muted">Waiting for SPED teacher review</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Step 4: Enrollment Complete -->
                                    <div class="progress-step mb-3">
                                        <div class="d-flex align-items-start">
                                            <div class="step-number <?php echo $enrollment->status == 'approved' ? 'completed' : ''; ?> me-3">
                                                4
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">Enrollment Complete</div>
                                                <small class="text-muted">
                                                    <?php if ($enrollment->status == 'approved'): ?>
                                                        <span class="text-success"><i class="bi bi-check-circle me-1"></i>Approved - Ready for assessment</span>
                                                    <?php elseif ($enrollment->status == 'rejected'): ?>
                                                        <span class="text-danger"><i class="bi bi-x-circle me-1"></i>Rejected: <?php echo htmlspecialchars($enrollment->rejection_reason ?? 'No reason provided'); ?></span>
                                                    <?php else: ?>
                                                        Pending approval
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if ($enrollment->status == 'pending_documents'): ?>
                                    <div class="d-grid gap-2 mt-3">
                                        <a href="<?php echo URLROOT; ?>/enrollment/upload?id=<?php echo $enrollment->id; ?>" class="btn btn-success">
                                            <i class="bi bi-upload me-2"></i>
                                            Upload Documents
                                        </a>
                                    </div>
                                <?php elseif ($enrollment->status == 'approved'): ?>
                                    <div class="alert alert-success mb-3">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        <strong>Enrollment Approved!</strong> Your child's learner account has been created. Check your email for the login credentials.
                                    </div>
                                    <div class="d-grid gap-2 mt-3">
                                        <a href="<?php echo URLROOT; ?>/assessment/create" class="btn btn-primary btn-lg">
                                            <i class="bi bi-clipboard-check me-2"></i>
                                            Start Initial Assessment
                                        </a>
                                        <small class="text-muted text-center mt-2">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Complete the initial assessment to help us understand your child's learning needs and create a personalized education plan.
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
            
            <!-- Announcements -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-megaphone me-2 text-brand-red"></i>
                    <h5 class="mb-0">Announcements</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['announcements'])): ?>
                        <?php foreach ($data['announcements'] as $announcement): ?>
                            <div class="announcement-card card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title text-brand-blue">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <?php echo htmlspecialchars($announcement->title); ?>
                                    </h6>
                                    <p class="card-text text-muted small mb-2">
                                        <?php echo htmlspecialchars($announcement->content); ?>
                                    </p>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        Posted: <?php echo date('M j, Y', strtotime($announcement->created_at)); ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p>No announcements at this time</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Enrolled Children -->
            <?php if (!empty($data['learners'])): ?>
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <i class="bi bi-people me-2 text-brand-red"></i>
                        <h5 class="mb-0">My Enrolled Children</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($data['learners'] as $learner): ?>
                            <div class="card border-start border-4 border-brand-blue mb-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar-circle me-3">
                                            <?php echo strtoupper(substr($learner->first_name, 0, 1)); ?>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($learner->first_name . ' ' . $learner->last_name); ?></h6>
                                            <small class="text-muted">Grade <?php echo htmlspecialchars($learner->grade_level ?? 'N/A'); ?></small>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>
                                            <?php echo ucfirst($learner->status ?? 'active'); ?>
                                        </span>
                                        <a href="<?php echo URLROOT; ?>/parent/viewLearner/<?php echo $learner->id; ?>" class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
        
    </div>
    
</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
