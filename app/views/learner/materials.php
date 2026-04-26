<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Materials - SignED SPED</title>
    <link rel="stylesheet" href="/public/assets/css/style.css">
    <style>
        .materials-container {
            max-width: 1200px;
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
        
        .materials-grid {
            display: grid;
            gap: 20px;
        }
        
        .objective-section {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #1E40AF;
        }
        
        .objective-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #E5E7EB;
        }
        
        .material-card {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.2s;
        }
        
        .material-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .material-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .material-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 5px;
        }
        
        .material-description {
            color: #6B7280;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .material-meta {
            display: flex;
            gap: 20px;
            margin-top: 15px;
            font-size: 0.9rem;
            color: #6B7280;
        }
        
        .material-due {
            font-weight: 500;
        }
        
        .due-soon {
            color: #D97706;
        }
        
        .overdue {
            color: #DC2626;
        }
        
        .material-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .status-submitted {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }
        
        .status-overdue {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        .material-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: #1E40AF;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1D4ED8;
        }
        
        .btn-secondary {
            background-color: #6B7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #4B5563;
        }
        
        .btn-success {
            background-color: #059669;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #047857;
        }
        
        .submissions-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
        }
        
        .submissions-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 15px;
        }
        
        .submission-item {
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 10px;
        }
        
        .submission-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .submission-info {
            font-size: 0.9rem;
            color: #6B7280;
        }
        
        .submission-status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6B7280;
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            margin-bottom: 10px;
            color: #374151;
        }
        
        .teacher-actions {
            background: #EFF6FF;
            border: 1px solid #DBEAFE;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .teacher-actions h3 {
            margin-bottom: 15px;
            color: #1E40AF;
        }
        
        @media (max-width: 768px) {
            .material-header {
                flex-direction: column;
                gap: 10px;
            }
            
            .material-meta {
                flex-direction: column;
                gap: 5px;
            }
            
            .material-actions {
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
    <div class="materials-container">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="nav-brand">
                <h1>SignED SPED</h1>
            </div>
            <div class="nav-links">
                <?php if ($is_teacher_view): ?>
                    <a href="/teacher/dashboard">Dashboard</a>
                    <a href="/learner/upload-material">Upload Material</a>
                <?php else: ?>
                    <a href="/learner/dashboard">Dashboard</a>
                <?php endif; ?>
                <a href="/learner/materials" class="active">Materials</a>
                <a href="/learner/track-progress">Progress</a>
                <a href="/auth/logout">Logout</a>
            </div>
        </nav>

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <?php if ($is_teacher_view): ?>
                    Learning Materials for <?= htmlspecialchars($learner->first_name . ' ' . $learner->last_name) ?>
                <?php else: ?>
                    My Learning Materials
                <?php endif; ?>
            </div>
            <div class="page-subtitle">
                <?php if ($is_teacher_view): ?>
                    Manage and review materials assigned to this learner
                <?php else: ?>
                    Access your assigned materials and submit completed work
                <?php endif; ?>
            </div>
        </div>

        <!-- Teacher Actions (if teacher view) -->
        <?php if ($is_teacher_view): ?>
        <div class="teacher-actions">
            <h3>Teacher Actions</h3>
            <div class="material-actions">
                <a href="/learner/upload-material" class="btn btn-primary">Upload New Material</a>
                <a href="/learner/track-progress/<?= $learner->id ?>" class="btn btn-secondary">View Progress</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Materials by Objective -->
        <div class="materials-grid">
            <?php if (empty($organized_materials)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📚</div>
                    <h3>No materials assigned yet</h3>
                    <p>
                        <?php if ($is_teacher_view): ?>
                            Upload learning materials to help this learner achieve their IEP objectives.
                        <?php else: ?>
                            Your SPED teacher will assign learning materials based on your IEP objectives.
                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($organized_materials as $objective => $objectiveMaterials): ?>
                <div class="objective-section">
                    <div class="objective-title"><?= htmlspecialchars($objective) ?></div>
                    
                    <?php foreach ($objectiveMaterials as $material): ?>
                    <div class="material-card">
                        <div class="material-header">
                            <div>
                                <div class="material-title"><?= htmlspecialchars($material->title) ?></div>
                                <?php if ($material->description): ?>
                                    <div class="material-description"><?= htmlspecialchars($material->description) ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <?php
                            $isSubmitted = isset($submissions_data[$material->id]) && !empty($submissions_data[$material->id]);
                            $isOverdue = false;
                            $isDueSoon = false;
                            
                            if ($material->due_date && !$isSubmitted) {
                                $dueTime = strtotime($material->due_date);
                                $now = time();
                                $isOverdue = $dueTime < $now;
                                $isDueSoon = !$isOverdue && $dueTime < ($now + 7 * 24 * 60 * 60);
                            }
                            ?>
                            
                            <div class="material-status <?= $isSubmitted ? 'status-submitted' : ($isOverdue ? 'status-overdue' : 'status-pending') ?>">
                                <?= $isSubmitted ? 'Submitted' : ($isOverdue ? 'Overdue' : 'Pending') ?>
                            </div>
                        </div>
                        
                        <div class="material-meta">
                            <div>Uploaded by: <?= htmlspecialchars($material->uploaded_by_name) ?></div>
                            <?php if ($material->due_date): ?>
                                <div class="material-due <?= $isOverdue ? 'overdue' : ($isDueSoon ? 'due-soon' : '') ?>">
                                    Due: <?= date('M j, Y', strtotime($material->due_date)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="material-actions">
                            <a href="/learner/download-material/<?= $material->id ?>" class="btn btn-secondary">
                                📥 Download Material
                            </a>
                            
                            <?php if (!$is_teacher_view && !$isSubmitted): ?>
                                <a href="/learner/submit-work/<?= $material->id ?>" class="btn btn-primary">
                                    📤 Submit Work
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($isSubmitted): ?>
                                <span class="btn btn-success" style="cursor: default;">
                                    ✅ Work Submitted
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Submissions Section -->
                        <?php if (isset($submissions_data[$material->id]) && !empty($submissions_data[$material->id])): ?>
                        <div class="submissions-section">
                            <div class="submissions-title">Submissions</div>
                            <?php foreach ($submissions_data[$material->id] as $submission): ?>
                            <div class="submission-item">
                                <div class="submission-header">
                                    <div class="submission-info">
                                        <strong><?= htmlspecialchars($submission->original_filename) ?></strong><br>
                                        Submitted: <?= date('M j, Y \a\t g:i A', strtotime($submission->submitted_at)) ?>
                                        <?php if ($submission->submission_notes): ?>
                                            <br>Notes: <?= htmlspecialchars($submission->submission_notes) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="submission-status <?= $submission->reviewed_at ? 'status-submitted' : 'status-pending' ?>">
                                        <?= $submission->reviewed_at ? 'Reviewed' : 'Under Review' ?>
                                    </div>
                                </div>
                                
                                <?php if ($submission->reviewed_at && $submission->review_notes): ?>
                                <div style="margin-top: 10px; padding: 10px; background: #F0F9FF; border-radius: 4px; font-size: 0.9rem;">
                                    <strong>Teacher Feedback:</strong><br>
                                    <?= htmlspecialchars($submission->review_notes) ?>
                                    <br><small>Reviewed on <?= date('M j, Y', strtotime($submission->reviewed_at)) ?></small>
                                </div>
                                <?php endif; ?>
                                
                                <div style="margin-top: 10px;">
                                    <a href="/learner/download-submission/<?= $submission->id ?>" class="btn btn-secondary" style="font-size: 0.8rem;">
                                        📥 Download Submission
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success" style="position: fixed; top: 20px; right: 20px; z-index: 1000; background: #D1FAE5; color: #065F46; padding: 15px; border-radius: 6px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <?php
        switch ($_GET['success']) {
            case 'material_uploaded':
                echo 'Learning material uploaded successfully!';
                break;
            case 'work_submitted':
                echo 'Work submitted successfully!';
                break;
            default:
                echo 'Action completed successfully!';
        }
        ?>
    </div>
    <script>
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) alert.style.display = 'none';
        }, 5000);
    </script>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error" style="position: fixed; top: 20px; right: 20px; z-index: 1000; background: #FEE2E2; color: #991B1B; padding: 15px; border-radius: 6px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <?php
        switch ($_GET['error']) {
            case 'material_not_found':
                echo 'Learning material not found.';
                break;
            case 'access_denied':
                echo 'Access denied.';
                break;
            case 'download_failed':
                echo 'File download failed. Please try again.';
                break;
            default:
                echo 'An error occurred. Please try again.';
        }
        ?>
    </div>
    <script>
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) alert.style.display = 'none';
        }, 5000);
    </script>
    <?php endif; ?>
</body>
</html>