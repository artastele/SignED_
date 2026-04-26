<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Work - SignED SPED</title>
    <link rel="stylesheet" href="/public/assets/css/style.css">
    <style>
        .submit-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .page-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .page-subtitle {
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .material-info {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            border-left: 4px solid #1E40AF;
        }
        
        .material-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 10px;
        }
        
        .material-objective {
            color: #6B7280;
            font-size: 1rem;
            margin-bottom: 15px;
            padding: 10px;
            background: #F9FAFB;
            border-radius: 4px;
        }
        
        .material-description {
            color: #374151;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .material-meta {
            display: flex;
            gap: 20px;
            font-size: 0.9rem;
            color: #6B7280;
            padding-top: 15px;
            border-top: 1px solid #E5E7EB;
        }
        
        .due-date {
            font-weight: 500;
        }
        
        .due-soon {
            color: #D97706;
        }
        
        .overdue {
            color: #DC2626;
        }
        
        .submission-form {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #E5E7EB;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #1E40AF;
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }
        
        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .file-upload {
            border: 2px dashed #D1D5DB;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            transition: all 0.2s;
            cursor: pointer;
        }
        
        .file-upload:hover {
            border-color: #1E40AF;
            background-color: #F8FAFC;
        }
        
        .file-upload.dragover {
            border-color: #1E40AF;
            background-color: #EFF6FF;
        }
        
        .file-upload-icon {
            font-size: 3rem;
            color: #9CA3AF;
            margin-bottom: 15px;
        }
        
        .file-upload-text {
            color: #6B7280;
            margin-bottom: 10px;
        }
        
        .file-upload-hint {
            font-size: 0.9rem;
            color: #9CA3AF;
        }
        
        .file-selected {
            background: #F0F9FF;
            border: 2px solid #1E40AF;
            padding: 15px;
            border-radius: 6px;
            margin-top: 10px;
        }
        
        .file-selected-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .file-selected-name {
            font-weight: 500;
            color: #1E40AF;
        }
        
        .file-selected-size {
            color: #6B7280;
            font-size: 0.9rem;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            display: inline-block;
            font-size: 1rem;
        }
        
        .btn-primary {
            background-color: #1E40AF;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1D4ED8;
        }
        
        .btn-primary:disabled {
            background-color: #9CA3AF;
            cursor: not-allowed;
        }
        
        .btn-secondary {
            background-color: #6B7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #4B5563;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .existing-submissions {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .submissions-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 20px;
        }
        
        .submission-item {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .submission-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .submission-info {
            font-weight: 500;
            color: #1F2937;
        }
        
        .submission-date {
            color: #6B7280;
            font-size: 0.9rem;
        }
        
        .submission-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-submitted {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }
        
        .submission-notes {
            color: #6B7280;
            font-size: 0.9rem;
            margin-top: 10px;
        }
        
        .error-message {
            background: #FEE2E2;
            color: #991B1B;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #DC2626;
        }
        
        @media (max-width: 768px) {
            .material-meta {
                flex-direction: column;
                gap: 5px;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .submission-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="submit-container">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="nav-brand">
                <h1>SignED SPED</h1>
            </div>
            <div class="nav-links">
                <a href="/learner/dashboard">Dashboard</a>
                <a href="/learner/materials">Materials</a>
                <a href="/learner/track-progress">Progress</a>
                <a href="/auth/logout">Logout</a>
            </div>
        </nav>

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">Submit Your Work</div>
            <div class="page-subtitle">Upload your completed assignment for review</div>
        </div>

        <!-- Material Information -->
        <div class="material-info">
            <div class="material-title"><?= htmlspecialchars($material->title) ?></div>
            
            <div class="material-objective">
                <strong>IEP Objective:</strong> <?= htmlspecialchars($material->iep_objective) ?>
            </div>
            
            <?php if ($material->description): ?>
                <div class="material-description">
                    <?= nl2br(htmlspecialchars($material->description)) ?>
                </div>
            <?php endif; ?>
            
            <div class="material-meta">
                <div>Uploaded by: <?= htmlspecialchars($material->uploaded_by_name) ?></div>
                <?php if ($material->due_date): ?>
                    <?php
                    $dueTime = strtotime($material->due_date);
                    $now = time();
                    $isOverdue = $dueTime < $now;
                    $isDueSoon = !$isOverdue && $dueTime < ($now + 7 * 24 * 60 * 60);
                    ?>
                    <div class="due-date <?= $isOverdue ? 'overdue' : ($isDueSoon ? 'due-soon' : '') ?>">
                        Due: <?= date('M j, Y', strtotime($material->due_date)) ?>
                        <?php if ($isOverdue): ?>
                            (Overdue)
                        <?php elseif ($isDueSoon): ?>
                            (Due Soon)
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Error Message -->
        <?php if (isset($error)): ?>
        <div class="error-message">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Submission Form -->
        <div class="submission-form">
            <form method="POST" enctype="multipart/form-data" id="submissionForm">
                <div class="form-group">
                    <label for="submission_file" class="form-label">
                        Upload Your Work <span style="color: #DC2626;">*</span>
                    </label>
                    <div class="file-upload" id="fileUpload">
                        <div class="file-upload-icon">📁</div>
                        <div class="file-upload-text">
                            <strong>Click to select a file</strong> or drag and drop
                        </div>
                        <div class="file-upload-hint">
                            Supported formats: PDF, DOC, DOCX, JPG, PNG (Max 5MB)
                        </div>
                        <input type="file" id="submission_file" name="submission_file" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" 
                               style="display: none;" required>
                    </div>
                    <div id="fileSelected" class="file-selected" style="display: none;">
                        <div class="file-selected-info">
                            <span>📄</span>
                            <div>
                                <div class="file-selected-name" id="fileName"></div>
                                <div class="file-selected-size" id="fileSize"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="submission_notes" class="form-label">
                        Notes (Optional)
                    </label>
                    <textarea id="submission_notes" name="submission_notes" 
                              class="form-input form-textarea" 
                              placeholder="Add any notes about your submission..."><?= isset($form_data['submission_notes']) ? htmlspecialchars($form_data['submission_notes']) : '' ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        📤 Submit Work
                    </button>
                    <a href="/learner/materials" class="btn btn-secondary">
                        ← Back to Materials
                    </a>
                </div>
            </form>
        </div>

        <!-- Existing Submissions -->
        <?php if (!empty($existing_submissions)): ?>
        <div class="existing-submissions">
            <div class="submissions-title">Previous Submissions</div>
            
            <?php foreach ($existing_submissions as $submission): ?>
            <div class="submission-item">
                <div class="submission-header">
                    <div>
                        <div class="submission-info"><?= htmlspecialchars($submission->original_filename) ?></div>
                        <div class="submission-date">
                            Submitted: <?= date('M j, Y \a\t g:i A', strtotime($submission->submitted_at)) ?>
                        </div>
                    </div>
                    <div class="submission-status <?= $submission->reviewed_at ? 'status-submitted' : 'status-pending' ?>">
                        <?= $submission->reviewed_at ? 'Reviewed' : 'Under Review' ?>
                    </div>
                </div>
                
                <?php if ($submission->submission_notes): ?>
                <div class="submission-notes">
                    <strong>Your Notes:</strong> <?= htmlspecialchars($submission->submission_notes) ?>
                </div>
                <?php endif; ?>
                
                <?php if ($submission->reviewed_at && $submission->review_notes): ?>
                <div style="margin-top: 10px; padding: 10px; background: #F0F9FF; border-radius: 4px; font-size: 0.9rem;">
                    <strong>Teacher Feedback:</strong><br>
                    <?= nl2br(htmlspecialchars($submission->review_notes)) ?>
                    <br><small>Reviewed on <?= date('M j, Y', strtotime($submission->reviewed_at)) ?></small>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // File upload handling
        const fileUpload = document.getElementById('fileUpload');
        const fileInput = document.getElementById('submission_file');
        const fileSelected = document.getElementById('fileSelected');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const submitBtn = document.getElementById('submitBtn');

        // Click to select file
        fileUpload.addEventListener('click', () => {
            fileInput.click();
        });

        // Drag and drop handling
        fileUpload.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUpload.classList.add('dragover');
        });

        fileUpload.addEventListener('dragleave', () => {
            fileUpload.classList.remove('dragover');
        });

        fileUpload.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUpload.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelection(files[0]);
            }
        });

        // File selection handling
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelection(e.target.files[0]);
            }
        });

        function handleFileSelection(file) {
            // Validate file type
            const allowedTypes = ['application/pdf', 'application/msword', 
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'image/jpeg', 'image/jpg', 'image/png'];
            
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Please select a PDF, DOC, DOCX, JPG, or PNG file.');
                fileInput.value = '';
                return;
            }

            // Validate file size (5MB)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('File size exceeds 5MB limit. Please select a smaller file.');
                fileInput.value = '';
                return;
            }

            // Display selected file
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileSelected.style.display = 'block';
            fileUpload.style.display = 'none';
            submitBtn.disabled = false;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Form submission handling
        document.getElementById('submissionForm').addEventListener('submit', (e) => {
            if (!fileInput.files.length) {
                e.preventDefault();
                alert('Please select a file to upload.');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = '⏳ Uploading...';
        });
    </script>
</body>
</html>