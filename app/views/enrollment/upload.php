<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Documents - SignED</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f3f4f6;
            color: #1f2937;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 30px;
            margin-left: 250px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;
            color: #111827;
            margin-bottom: 5px;
        }

        .page-header p {
            color: #6b7280;
            font-size: 14px;
        }

        .enrollment-info-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .enrollment-info-card h3 {
            font-size: 16px;
            color: #111827;
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 14px;
            color: #111827;
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.pending_documents {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.pending_verification {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-badge.approved {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .progress-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .progress-header h3 {
            font-size: 16px;
            color: #111827;
        }

        .progress-count {
            font-size: 14px;
            color: #6b7280;
            font-weight: 600;
        }

        .progress-bar {
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .progress-fill {
            height: 100%;
            background: #10b981;
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 12px;
            color: #6b7280;
        }

        .requirements-box {
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .requirements-box h4 {
            font-size: 14px;
            color: #92400e;
            margin-bottom: 10px;
        }

        .requirements-box ul {
            margin-left: 20px;
            font-size: 13px;
            color: #78350f;
        }

        .requirements-box li {
            margin: 5px 0;
        }

        .success-message {
            background: #d1fae5;
            border-left: 3px solid #10b981;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #065f46;
        }

        .document-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .document-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .document-card.uploaded {
            border-color: #10b981;
            background: #f0fdf4;
        }

        .document-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .document-title {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
        }

        .upload-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .upload-badge.uploaded {
            background: #d1fae5;
            color: #065f46;
        }

        .upload-badge.required {
            background: #fee2e2;
            color: #991b1b;
        }

        .document-description {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .file-upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 6px;
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .file-upload-area:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .file-upload-area.dragover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .upload-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .upload-text {
            font-size: 14px;
            color: #374151;
            margin-bottom: 5px;
        }

        .upload-hint {
            font-size: 12px;
            color: #9ca3af;
        }

        .uploaded-file-info {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .uploaded-file-name {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 8px;
            word-break: break-all;
        }

        .uploaded-file-meta {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 3px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-success {
            background: #10b981;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-secondary {
            background: #6b7280;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-danger {
            background: #ef4444;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-small {
            padding: 8px 16px;
            font-size: 13px;
        }

        .btn-full {
            width: 100%;
            margin-top: 10px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .page-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .document-grid {
                grid-template-columns: 1fr;
            }

            .page-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<?php include '../app/views/partials/simple_popup.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<div class="dashboard-container">
    <div class="main-content">
        <div class="page-header">
            <h1>📤 Upload Enrollment Documents</h1>
            <p>Upload all required documents to complete your enrollment</p>
        </div>

        <!-- Enrollment Information Card -->
        <div class="enrollment-info-card">
            <h3>📋 Enrollment Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Student Name</span>
                    <span class="info-value"><?php echo htmlspecialchars($data['enrollment']->learner_first_name . ' ' . $data['enrollment']->learner_last_name); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Grade Level</span>
                    <span class="info-value">Grade <?php echo htmlspecialchars($data['enrollment']->learner_grade); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Date of Birth</span>
                    <span class="info-value"><?php echo date('M d, Y', strtotime($data['enrollment']->learner_dob)); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status</span>
                    <span class="status-badge <?php echo $data['enrollment']->status; ?>">
                        <?php echo ucwords(str_replace('_', ' ', $data['enrollment']->status)); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Progress Card -->
        <?php
        $uploadedCount = count($data['documents']);
        $totalRequired = 4;
        $progressPercent = ($uploadedCount / $totalRequired) * 100;
        ?>
        <div class="progress-card">
            <div class="progress-header">
                <h3>📊 Upload Progress</h3>
                <span class="progress-count"><?php echo $uploadedCount; ?> / <?php echo $totalRequired; ?> documents</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $progressPercent; ?>%"></div>
            </div>
            <p class="progress-text">
                <?php if ($uploadedCount == $totalRequired): ?>
                    ✅ All documents uploaded! Your enrollment is now pending verification.
                <?php else: ?>
                    <?php echo (4 - $uploadedCount); ?> document(s) remaining
                <?php endif; ?>
            </p>
        </div>

        <!-- Requirements Box -->
        <div class="requirements-box">
            <h4>📌 File Requirements:</h4>
            <ul>
                <li>Accepted file types: PDF, JPG, PNG</li>
                <li>Maximum file size: 5MB per document</li>
                <li>All four document types are required</li>
                <li>Files are encrypted and stored securely</li>
            </ul>
        </div>

        <!-- Success Message -->
        <?php if ($data['enrollment']->status === 'pending_verification'): ?>
            <div class="success-message">
                <strong>✅ All documents uploaded successfully!</strong><br>
                Your enrollment is now pending verification by SPED staff. You will be notified once the verification is complete.
            </div>
        <?php endif; ?>

        <!-- Document Grid -->
        <div class="document-grid">
            <?php foreach ($data['required_types'] as $type => $title): ?>
                <div class="document-card <?php echo isset($data['uploaded_types'][$type]) ? 'uploaded' : ''; ?>">
                    <div class="document-header">
                        <div class="document-title"><?php echo htmlspecialchars($title); ?></div>
                        <div class="upload-badge <?php echo isset($data['uploaded_types'][$type]) ? 'uploaded' : 'required'; ?>">
                            <?php echo isset($data['uploaded_types'][$type]) ? '✓ Uploaded' : 'Required'; ?>
                        </div>
                    </div>

                    <div class="document-description">
                        <?php
                        switch($type) {
                            case 'psa':
                                echo '📄 Official birth certificate from Philippine Statistics Authority';
                                break;
                            case 'pwd_id':
                                echo '🆔 Person with Disability identification card (optional)';
                                break;
                            case 'medical_record':
                                echo '🏥 Medical documentation supporting disability status (optional)';
                                break;
                            case 'beef':
                                echo '📋 Basic Education Enrollment Form from DepEd';
                                break;
                        }
                        ?>
                    </div>

                    <?php if (isset($data['uploaded_types'][$type])): ?>
                        <!-- Uploaded File Info -->
                        <div class="uploaded-file-info">
                            <div class="uploaded-file-name">
                                <?php echo htmlspecialchars($data['uploaded_types'][$type]->original_filename); ?>
                            </div>
                            <div class="uploaded-file-meta">
                                📅 Uploaded: <?php echo date('M d, Y g:i A', strtotime($data['uploaded_types'][$type]->uploaded_at)); ?>
                            </div>
                            <div class="uploaded-file-meta">
                                📦 Size: <?php echo number_format($data['uploaded_types'][$type]->file_size / 1024, 1); ?> KB
                            </div>
                        </div>
                        <div class="action-buttons">
                            <button class="btn btn-success btn-small" onclick="replaceDocument('<?php echo $type; ?>', '<?php echo $data['enrollment']->id; ?>')">
                                🔄 Replace
                            </button>
                        </div>
                    <?php else: ?>
                        <!-- Upload Form -->
                        <form method="POST" action="<?php echo URLROOT; ?>/enrollment/upload?id=<?php echo $data['enrollment']->id; ?>" 
                              enctype="multipart/form-data" id="form-<?php echo $type; ?>">
                            <input type="hidden" name="document_type" value="<?php echo $type; ?>">
                            
                            <div class="file-upload-area" id="upload-area-<?php echo $type; ?>" 
                                 onclick="document.getElementById('file-<?php echo $type; ?>').click()">
                                <div class="upload-icon">📁</div>
                                <div class="upload-text">Click to select file</div>
                                <div class="upload-hint">or drag and drop here</div>
                            </div>
                            <input type="file" id="file-<?php echo $type; ?>" name="document" 
                                   accept=".pdf,.jpg,.jpeg,.png" style="display: none;" 
                                   onchange="handleFileSelect(this, '<?php echo $type; ?>')">
                            
                            <button type="submit" class="btn btn-full" id="submit-<?php echo $type; ?>" style="display: none;">
                                Upload Document
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Page Actions -->
        <div class="page-actions">
            <a href="<?php echo URLROOT; ?>/parent/dashboard" class="btn btn-secondary">
                ← Back to Dashboard
            </a>
            <a href="<?php echo URLROOT; ?>/enrollment/status" class="btn btn-secondary">
                View All Enrollments
            </a>
        </div>
    </div>
</div>

<script>
function handleFileSelect(input, documentType) {
    const file = input.files[0];
    if (!file) return;
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        showPopup('File size exceeds 5MB limit. Please select a smaller file.', 'error');
        input.value = '';
        return;
    }
    
    // Validate file type
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
        showPopup('Invalid file type. Please select a PDF, JPG, or PNG file.', 'error');
        input.value = '';
        return;
    }
    
    // Update upload area to show selected file
    const uploadArea = document.getElementById('upload-area-' + documentType);
    uploadArea.innerHTML = `
        <div class="upload-icon">✓</div>
        <div class="upload-text"><strong>${file.name}</strong></div>
        <div class="upload-hint">Size: ${(file.size / 1024).toFixed(1)} KB</div>
    `;
    
    // Show submit button
    document.getElementById('submit-' + documentType).style.display = 'block';
}

function replaceDocument(documentType, enrollmentId) {
    if (confirm('Are you sure you want to replace this document?')) {
        // Trigger file input
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = '.pdf,.jpg,.jpeg,.png';
        fileInput.onchange = function() {
            const file = this.files[0];
            if (!file) return;
            
            // Validate file
            if (file.size > 5 * 1024 * 1024) {
                showPopup('File size exceeds 5MB limit.', 'error');
                return;
            }
            
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo URLROOT; ?>/enrollment/upload?id=' + enrollmentId;
            form.enctype = 'multipart/form-data';
            
            const typeInput = document.createElement('input');
            typeInput.type = 'hidden';
            typeInput.name = 'document_type';
            typeInput.value = documentType;
            
            const fileInputClone = document.createElement('input');
            fileInputClone.type = 'file';
            fileInputClone.name = 'document';
            fileInputClone.files = this.files;
            
            form.appendChild(typeInput);
            form.appendChild(fileInputClone);
            document.body.appendChild(form);
            form.submit();
        };
        fileInput.click();
    }
}

// Drag and drop functionality
document.querySelectorAll('.file-upload-area').forEach(area => {
    area.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('dragover');
    });
    
    area.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('dragover');
    });
    
    area.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const documentType = this.id.replace('upload-area-', '');
            const fileInput = document.getElementById('file-' + documentType);
            fileInput.files = files;
            handleFileSelect(fileInput, documentType);
        }
    });
});
</script>

</body>
</html>
