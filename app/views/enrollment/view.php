<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>
                    <i class="bi bi-file-earmark-check me-2"></i>
                    Review Enrollment Application
                </h1>
                <p class="mb-0">Verify documents and approve or reject the enrollment</p>
            </div>
            <a href="<?php echo URLROOT; ?>/enrollment/verify" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Verification
            </a>
        </div>
    </div>
    
    <!-- Student Information Card -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-person-badge me-2"></i>Student Information
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="mb-0">
                        <small class="text-muted d-block mb-1">Student Name</small>
                        <strong><?php echo htmlspecialchars($data['enrollment']->learner_first_name . ' ' . $data['enrollment']->learner_last_name); ?></strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-0">
                        <small class="text-muted d-block mb-1">Date of Birth</small>
                        <strong><?php echo date('F j, Y', strtotime($data['enrollment']->learner_dob)); ?></strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-0">
                        <small class="text-muted d-block mb-1">Grade Level</small>
                        <strong><?php echo htmlspecialchars($data['enrollment']->learner_grade); ?></strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-0">
                        <small class="text-muted d-block mb-1">Parent/Guardian</small>
                        <strong><?php echo htmlspecialchars($data['enrollment']->parent_name); ?></strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-0">
                        <small class="text-muted d-block mb-1">Parent Email</small>
                        <strong><?php echo htmlspecialchars($data['enrollment']->parent_email); ?></strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-0">
                        <small class="text-muted d-block mb-1">Submission Date</small>
                        <strong><?php echo date('F j, Y g:i A', strtotime($data['enrollment']->created_at)); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Required Documents Card -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-files me-2"></i>Required Documents
        </div>
        <div class="card-body">
            
            <?php
            // Define document types with their requirements
            $requiredTypes = [
                'beef' => [
                    'title' => 'Basic Education Enrollment Form (BEEF)',
                    'required' => true,
                    'description' => 'Basic Education Enrollment Form from DepEd',
                    'already_submitted' => true // BEEF is submitted during enrollment
                ],
                'psa' => [
                    'title' => 'PSA Birth Certificate',
                    'required' => true,
                    'description' => 'Official birth certificate from Philippine Statistics Authority'
                ],
                'pwd_id' => [
                    'title' => 'PWD ID Card',
                    'required' => false,
                    'description' => 'Person with Disability identification card (if available)'
                ],
                'medical_record' => [
                    'title' => 'Medical Records',
                    'required' => false,
                    'description' => 'Medical documentation supporting disability status (if available)'
                ]
            ];
            
            // Create array of uploaded document types
            $uploadedTypes = [];
            foreach ($data['documents'] as $doc) {
                $uploadedTypes[$doc->document_type] = $doc;
            }
            
            // Count only required documents
            $requiredCount = 0;
            $uploadedRequiredCount = 0;
            foreach ($requiredTypes as $type => $info) {
                if ($info['required']) {
                    $requiredCount++;
                    if (isset($uploadedTypes[$type]) || ($type === 'beef' && $info['already_submitted'])) {
                        $uploadedRequiredCount++;
                    }
                }
            }
            
            $allRequiredUploaded = ($uploadedRequiredCount >= $requiredCount);
            ?>
            
            <!-- Completion Status -->
            <div class="alert <?php echo $allRequiredUploaded ? 'alert-success' : 'alert-warning'; ?> d-flex align-items-center mb-4">
                <i class="bi <?php echo $allRequiredUploaded ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?> fs-4 me-3"></i>
                <div>
                    <?php if ($allRequiredUploaded): ?>
                        <strong>All required documents submitted</strong> (<?php echo $uploadedRequiredCount; ?>/<?php echo $requiredCount; ?>)
                        <br><small>This enrollment is ready for verification.</small>
                    <?php else: ?>
                        <strong>Missing required documents</strong> (<?php echo $uploadedRequiredCount; ?>/<?php echo $requiredCount; ?>)
                        <br><small>Please contact the parent to upload the missing required documents.</small>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Documents Grid -->
            <div class="row g-3">
                <?php foreach ($requiredTypes as $type => $info): ?>
                    <?php 
                    $isUploaded = isset($uploadedTypes[$type]);
                    $isBeefSubmitted = ($type === 'beef' && $info['already_submitted']);
                    $hasDocument = $isUploaded || $isBeefSubmitted;
                    ?>
                    
                    <div class="col-md-6">
                        <div class="card h-100 <?php echo $hasDocument ? 'border-success' : ($info['required'] ? 'border-danger' : 'border-secondary'); ?>" style="border-width: 2px;">
                            <div class="card-body">
                                <!-- Document Header -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <?php echo htmlspecialchars($info['title']); ?>
                                            <?php if (!$info['required']): ?>
                                                <span class="badge bg-secondary ms-2">Optional</span>
                                            <?php endif; ?>
                                        </h6>
                                        <small class="text-muted"><?php echo $info['description']; ?></small>
                                    </div>
                                    <div class="ms-2">
                                        <?php if ($hasDocument): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>
                                                <?php echo $isBeefSubmitted ? 'Submitted' : 'Uploaded'; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-<?php echo $info['required'] ? 'danger' : 'secondary'; ?>">
                                                <i class="bi bi-x-circle me-1"></i>Missing
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Document Details -->
                                <?php if ($isUploaded): ?>
                                    <div class="bg-light p-3 rounded mb-3">
                                        <div class="small">
                                            <div class="mb-1">
                                                <i class="bi bi-file-earmark text-brand-blue me-2"></i>
                                                <strong>Filename:</strong> <?php echo htmlspecialchars($uploadedTypes[$type]->original_filename); ?>
                                            </div>
                                            <div class="mb-1">
                                                <i class="bi bi-hdd text-brand-blue me-2"></i>
                                                <strong>Size:</strong> <?php echo number_format($uploadedTypes[$type]->file_size / 1024, 1); ?> KB
                                            </div>
                                            <div class="mb-1">
                                                <i class="bi bi-filetype-pdf text-brand-blue me-2"></i>
                                                <strong>Type:</strong> <?php echo htmlspecialchars($uploadedTypes[$type]->mime_type); ?>
                                            </div>
                                            <div>
                                                <i class="bi bi-calendar-check text-brand-blue me-2"></i>
                                                <strong>Uploaded:</strong> <?php echo date('M j, Y g:i A', strtotime($uploadedTypes[$type]->uploaded_at)); ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <a href="<?php echo URLROOT; ?>/enrollment/viewDocument?doc_id=<?php echo $uploadedTypes[$type]->id; ?>" 
                                       class="btn btn-primary btn-sm w-100" target="_blank">
                                        <i class="bi bi-eye me-2"></i>View Document
                                    </a>
                                    
                                <?php elseif ($isBeefSubmitted): ?>
                                    <div class="bg-light p-3 rounded mb-3">
                                        <div class="text-center text-success">
                                            <i class="bi bi-check-circle-fill fs-3 mb-2"></i>
                                            <p class="mb-0 small">BEEF form was completed during enrollment submission.</p>
                                        </div>
                                    </div>
                                    
                                    <a href="<?php echo URLROOT; ?>/enrollment/viewBeef?id=<?php echo $data['enrollment']->id; ?>" 
                                       class="btn btn-primary btn-sm w-100" target="_blank">
                                        <i class="bi bi-eye me-2"></i>View BEEF Form
                                    </a>
                                    
                                <?php else: ?>
                                    <div class="bg-light p-3 rounded">
                                        <div class="text-center text-<?php echo $info['required'] ? 'danger' : 'muted'; ?>">
                                            <i class="bi bi-<?php echo $info['required'] ? 'exclamation-circle' : 'info-circle'; ?>-fill fs-3 mb-2"></i>
                                            <p class="mb-0 small">
                                                <?php if ($info['required']): ?>
                                                    This required document has not been uploaded by the parent.
                                                <?php else: ?>
                                                    This optional document has not been uploaded.
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Action Buttons -->
            <?php if ($allRequiredUploaded && $data['enrollment']->status !== 'approved'): ?>
                <div class="d-flex gap-2 justify-content-center mt-4">
                    <form method="POST" action="<?php echo URLROOT; ?>/enrollment/approve" style="display: inline;">
                        <input type="hidden" name="enrollment_id" value="<?php echo $data['enrollment']->id; ?>">
                        <button type="submit" class="btn btn-success" 
                                onclick="return confirm('Approve this enrollment?')">
                            <i class="bi bi-check-circle me-2"></i>Approve
                        </button>
                    </form>
                    
                    <button class="btn btn-danger" 
                            data-bs-toggle="modal" 
                            data-bs-target="#rejectModal"
                            onclick="setRejectData(<?php echo $data['enrollment']->id; ?>, '<?php echo htmlspecialchars($data['enrollment']->learner_first_name . ' ' . $data['enrollment']->learner_last_name); ?>')">
                        <i class="bi bi-x-circle me-2"></i>Reject
                    </button>
                </div>
            <?php elseif ($data['enrollment']->status === 'approved'): ?>
                <div class="alert alert-success text-center mt-4">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>This enrollment has been approved</strong>
                    <?php if ($data['enrollment']->verified_at): ?>
                        <br><small>Approved on <?php echo date('F j, Y g:i A', strtotime($data['enrollment']->verified_at)); ?></small>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="text-center mt-4">
                    <p class="text-warning mb-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Missing required documents. Please contact the parent.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
</main>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="bi bi-x-circle me-2"></i>Reject Enrollment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <textarea name="rejection_reason" 
                                  id="rejection_reason" 
                                  class="form-control" 
                                  rows="4" 
                                  required 
                                  placeholder="Please provide a detailed reason for rejecting this enrollment. This will be sent to the parent via email."></textarea>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            The parent will receive this reason via email notification.
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-2"></i>Reject Enrollment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function setRejectData(enrollmentId, studentName) {
    document.getElementById('rejectEnrollmentId').value = enrollmentId;
    document.getElementById('rejectStudentName').textContent = studentName;
    document.getElementById('rejection_reason').value = '';
}
</script>

</body>
</html>
