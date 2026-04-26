<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Enrollment - SignED SPED</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/style.css">
    <style>
        .review-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .enrollment-info {
            background-color: #F9FAFB;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 0.875rem;
            color: #6B7280;
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-weight: 600;
            color: #374151;
        }
        
        .documents-section {
            margin-bottom: 2rem;
        }
        
        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .document-card {
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 1.5rem;
            background: white;
        }
        
        .document-card.uploaded {
            border-color: #10B981;
            background-color: #F0FDF4;
        }
        
        .document-card.missing {
            border-color: #EF4444;
            background-color: #FEF2F2;
        }
        
        .document-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .document-title {
            font-weight: 600;
            color: #374151;
        }
        
        .document-status {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status-uploaded {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .status-missing {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        .document-details {
            margin-bottom: 1rem;
        }
        
        .document-meta {
            font-size: 0.875rem;
            color: #6B7280;
            margin-bottom: 0.5rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            margin-right: 0.5rem;
        }
        
        .btn-primary {
            background-color: #1E40AF;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1D4ED8;
        }
        
        .btn-success {
            background-color: #10B981;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #059669;
        }
        
        .btn-danger {
            background-color: #EF4444;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #DC2626;
        }
        
        .btn-secondary {
            background-color: #6B7280;
            color: white;
        }
        
        .actions-section {
            background-color: #F9FAFB;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
        }
        
        .actions-section h3 {
            margin-bottom: 1rem;
            color: #374151;
        }
        
        .completion-status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            border-radius: 4px;
        }
        
        .completion-status.complete {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .completion-status.incomplete {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .close {
            font-size: 1.5rem;
            cursor: pointer;
            color: #6B7280;
        }
        
        .close:hover {
            color: #374151;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #D1D5DB;
            border-radius: 4px;
            resize: vertical;
            min-height: 100px;
        }
        
        .document-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: 4px;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="review-container">
        <div class="header-section">
            <div>
                <h1>Review Enrollment Application</h1>
                <p>Verify documents and approve or reject the enrollment</p>
            </div>
            <div>
                <a href="<?php echo URLROOT; ?>/enrollment/verify" class="btn btn-secondary">Back to Verification</a>
            </div>
        </div>
        
        <div class="enrollment-info">
            <h3>Student Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Student Name</div>
                    <div class="info-value"><?php echo htmlspecialchars($enrollment->learner_first_name . ' ' . $enrollment->learner_last_name); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Date of Birth</div>
                    <div class="info-value"><?php echo date('F j, Y', strtotime($enrollment->learner_dob)); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Grade Level</div>
                    <div class="info-value"><?php echo htmlspecialchars($enrollment->learner_grade); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Parent/Guardian</div>
                    <div class="info-value"><?php echo htmlspecialchars($enrollment->parent_name); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Parent Email</div>
                    <div class="info-value"><?php echo htmlspecialchars($enrollment->parent_email); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Submission Date</div>
                    <div class="info-value"><?php echo date('F j, Y g:i A', strtotime($enrollment->created_at)); ?></div>
                </div>
            </div>
        </div>
        
        <div class="documents-section">
            <h3>Required Documents</h3>
            
            <?php
            $requiredTypes = [
                'psa' => 'PSA Birth Certificate',
                'pwd_id' => 'PWD ID Card',
                'medical_record' => 'Medical Records',
                'beef' => 'Basic Education Enrollment Form (BEEF)'
            ];
            
            $uploadedTypes = [];
            foreach ($documents as $doc) {
                $uploadedTypes[$doc->document_type] = $doc;
            }
            
            $completedCount = count($documents);
            $totalRequired = 4;
            ?>
            
            <div class="completion-status <?php echo $completedCount >= $totalRequired ? 'complete' : 'incomplete'; ?>">
                <?php if ($completedCount >= $totalRequired): ?>
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                    </svg>
                    <strong>All required documents uploaded (<?php echo $completedCount; ?>/<?php echo $totalRequired; ?>)</strong>
                <?php else: ?>
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                    <strong>Missing documents (<?php echo $completedCount; ?>/<?php echo $totalRequired; ?> uploaded)</strong>
                <?php endif; ?>
            </div>
            
            <div class="documents-grid">
                <?php foreach ($requiredTypes as $type => $title): ?>
                    <div class="document-card <?php echo isset($uploadedTypes[$type]) ? 'uploaded' : 'missing'; ?>">
                        <div class="document-header">
                            <div class="document-title"><?php echo htmlspecialchars($title); ?></div>
                            <div class="document-status <?php echo isset($uploadedTypes[$type]) ? 'status-uploaded' : 'status-missing'; ?>">
                                <?php echo isset($uploadedTypes[$type]) ? 'Uploaded' : 'Missing'; ?>
                            </div>
                        </div>
                        
                        <?php if (isset($uploadedTypes[$type])): ?>
                            <div class="document-details">
                                <div class="document-meta">
                                    <strong>Filename:</strong> <?php echo htmlspecialchars($uploadedTypes[$type]->original_filename); ?><br>
                                    <strong>Size:</strong> <?php echo number_format($uploadedTypes[$type]->file_size / 1024, 1); ?> KB<br>
                                    <strong>Type:</strong> <?php echo htmlspecialchars($uploadedTypes[$type]->mime_type); ?><br>
                                    <strong>Uploaded:</strong> <?php echo date('M j, Y g:i A', strtotime($uploadedTypes[$type]->uploaded_at)); ?>
                                </div>
                                
                                <div>
                                    <a href="<?php echo URLROOT; ?>/enrollment/download?doc_id=<?php echo $uploadedTypes[$type]->id; ?>" 
                                       class="btn btn-primary" target="_blank">
                                        Download & Review
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="document-details">
                                <div class="document-meta" style="color: #991B1B;">
                                    This required document has not been uploaded by the parent.
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div style="font-size: 0.875rem; color: #6B7280; margin-top: 1rem;">
                            <?php
                            switch($type) {
                                case 'psa':
                                    echo 'Official birth certificate from Philippine Statistics Authority';
                                    break;
                                case 'pwd_id':
                                    echo 'Person with Disability identification card';
                                    break;
                                case 'medical_record':
                                    echo 'Medical documentation supporting disability status';
                                    break;
                                case 'beef':
                                    echo 'Basic Education Enrollment Form from DepEd';
                                    break;
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="actions-section">
            <h3>Verification Decision</h3>
            
            <?php if ($completedCount >= $totalRequired): ?>
                <p>All required documents have been uploaded. You can now approve or reject this enrollment.</p>
                
                <div style="margin-top: 1.5rem;">
                    <form method="POST" action="<?php echo URLROOT; ?>/enrollment/approve" style="display: inline;">
                        <input type="hidden" name="enrollment_id" value="<?php echo $enrollment->id; ?>">
                        <button type="submit" class="btn btn-success" 
                                onclick="return confirm('Are you sure you want to approve this enrollment? This will create a learner record and send a notification email.')">
                            ✓ Approve Enrollment
                        </button>
                    </form>
                    
                    <button class="btn btn-danger" 
                            onclick="showRejectModal(<?php echo $enrollment->id; ?>, '<?php echo htmlspecialchars($enrollment->learner_first_name . ' ' . $enrollment->learner_last_name); ?>')">
                        ✗ Reject Enrollment
                    </button>
                </div>
            <?php else: ?>
                <p style="color: #991B1B;">This enrollment cannot be processed until all required documents are uploaded.</p>
                <p>Please contact the parent to upload the missing documents.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Rejection Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Reject Enrollment</h3>
                <span class="close" onclick="closeRejectModal()">&times;</span>
            </div>
            
            <form method="POST" action="<?php echo URLROOT; ?>/enrollment/reject" id="rejectForm">
                <input type="hidden" name="enrollment_id" id="rejectEnrollmentId">
                
                <p>You are about to reject the enrollment for <strong id="rejectStudentName"></strong>.</p>
                
                <div class="form-group">
                    <label for="rejection_reason">Reason for Rejection <span style="color: #EF4444;">*</span></label>
                    <textarea name="rejection_reason" id="rejection_reason" required 
                              placeholder="Please provide a detailed reason for rejecting this enrollment. This will be sent to the parent via email."></textarea>
                </div>
                
                <div style="text-align: right; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger" style="margin-left: 0.5rem;">Reject Enrollment</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function showRejectModal(enrollmentId, studentName) {
            document.getElementById('rejectEnrollmentId').value = enrollmentId;
            document.getElementById('rejectStudentName').textContent = studentName;
            document.getElementById('rejection_reason').value = '';
            document.getElementById('rejectModal').style.display = 'block';
        }
        
        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('rejectModal');
            if (event.target === modal) {
                closeRejectModal();
            }
        }
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeRejectModal();
            }
        });
    </script>
</body>
</html>