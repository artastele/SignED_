<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Tracking - SignED SPED</title>
    <link rel="stylesheet" href="/public/assets/css/style.css">
    <style>
        .progress-container {
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
        
        .progress-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .progress-card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            border-left: 4px solid #1E40AF;
        }
        
        .progress-card.completion {
            border-left-color: #059669;
        }
        
        .progress-card.overdue {
            border-left-color: #DC2626;
        }
        
        .progress-card.upcoming {
            border-left-color: #D97706;
        }
        
        .progress-number {
            font-size: 3rem;
            font-weight: bold;
            color: #1E40AF;
            margin-bottom: 10px;
        }
        
        .progress-number.completion {
            color: #059669;
        }
        
        .progress-number.overdue {
            color: #DC2626;
        }
        
        .progress-number.upcoming {
            color: #D97706;
        }
        
        .progress-label {
            color: #6B7280;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background-color: #E5E7EB;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background-color: #059669;
            transition: width 0.3s ease;
        }
        
        .iep-info {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            border-left: 4px solid #7C3AED;
        }
        
        .iep-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 15px;
        }
        
        .iep-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            color: #6B7280;
        }
        
        .iep-detail {
            display: flex;
            flex-direction: column;
        }
        
        .iep-detail-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .iep-detail-value {
            font-weight: 500;
            color: #1F2937;
        }
        
        .objectives-progress {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .objectives-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 25px;
        }
        
        .objective-item {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .objective-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .objective-name {
            font-weight: 600;
            color: #1F2937;
            flex: 1;
        }
        
        .objective-percentage {
            font-size: 1.2rem;
            font-weight: bold;
            color: #059669;
            margin-left: 15px;
        }
        
        .objective-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #6B7280;
        }
        
        .objective-progress-bar {
            width: 100%;
            height: 12px;
            background-color: #E5E7EB;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .objective-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #059669 0%, #10B981 100%);
            transition: width 0.3s ease;
        }
        
        .materials-timeline {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .timeline-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 25px;
        }
        
        .timeline-item {
            display: flex;
            margin-bottom: 20px;
            position: relative;
        }
        
        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 15px;
            top: 40px;
            width: 2px;
            height: calc(100% + 20px);
            background-color: #E5E7EB;
        }
        
        .timeline-marker {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            flex-shrink: 0;
            font-size: 0.8rem;
            font-weight: bold;
            color: white;
        }
        
        .timeline-marker.submitted {
            background-color: #059669;
        }
        
        .timeline-marker.pending {
            background-color: #D97706;
        }
        
        .timeline-marker.overdue {
            background-color: #DC2626;
        }
        
        .timeline-content {
            flex: 1;
            padding-top: 5px;
        }
        
        .timeline-material {
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 5px;
        }
        
        .timeline-objective {
            color: #6B7280;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .timeline-date {
            color: #9CA3AF;
            font-size: 0.8rem;
        }
        
        .teacher-actions {
            background: #EFF6FF;
            border: 1px solid #DBEAFE;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .teacher-actions h3 {
            margin-bottom: 15px;
            color: #1E40AF;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
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
        
        @media (max-width: 768px) {
            .progress-overview {
                grid-template-columns: 1fr;
            }
            
            .objective-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .objective-stats {
                flex-direction: column;
                gap: 5px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .timeline-item {
                flex-direction: column;
                margin-left: 20px;
            }
            
            .timeline-marker {
                margin-bottom: 10px;
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <div class="progress-container">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="nav-brand">
                <h1>SignED SPED</h1>
            </div>
            <div class="nav-links">
                <?php if ($is_teacher_view): ?>
                    <a href="/teacher/dashboard">Dashboard</a>
                    <a href="/learner/materials/<?= $learner->id ?>">Materials</a>
                <?php else: ?>
                    <a href="/learner/dashboard">Dashboard</a>
                    <a href="/learner/materials">Materials</a>
                <?php endif; ?>
                <a href="/learner/track-progress" class="active">Progress</a>
                <a href="/auth/logout">Logout</a>
            </div>
        </nav>

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <?php if ($is_teacher_view): ?>
                    Progress Tracking for <?= htmlspecialchars($learner->first_name . ' ' . $learner->last_name) ?>
                <?php else: ?>
                    My Learning Progress
                <?php endif; ?>
            </div>
            <div class="page-subtitle">
                <?php if ($is_teacher_view): ?>
                    Monitor learner progress and completion rates
                <?php else: ?>
                    Track your completion of IEP objectives and assignments
                <?php endif; ?>
            </div>
        </div>

        <!-- Teacher Actions (if teacher view) -->
        <?php if ($is_teacher_view): ?>
        <div class="teacher-actions">
            <h3>Teacher Actions</h3>
            <div class="action-buttons">
                <a href="/learner/upload-material" class="btn btn-primary">Upload New Material</a>
                <a href="/learner/materials/<?= $learner->id ?>" class="btn btn-secondary">View Materials</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Progress Overview -->
        <div class="progress-overview">
            <div class="progress-card">
                <div class="progress-number"><?= count($materials) ?></div>
                <div class="progress-label">Total Materials</div>
            </div>
            
            <div class="progress-card completion">
                <div class="progress-number completion">
                    <?= $progress_data['objective_progress'] ? 
                        round(array_sum(array_column($progress_data['objective_progress'], 'submitted_materials')) / 
                              max(array_sum(array_column($progress_data['objective_progress'], 'total_materials')), 1) * 100) : 0 ?>%
                </div>
                <div class="progress-label">Overall Completion</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $progress_data['objective_progress'] ? 
                        round(array_sum(array_column($progress_data['objective_progress'], 'submitted_materials')) / 
                              max(array_sum(array_column($progress_data['objective_progress'], 'total_materials')), 1) * 100) : 0 ?>%"></div>
                </div>
            </div>
            
            <?php
            $overdueCount = 0;
            $upcomingCount = 0;
            foreach ($materials as $material) {
                $isSubmitted = isset($progress_data['submissions_by_material'][$material->id]);
                if ($material->due_date && !$isSubmitted) {
                    $dueTime = strtotime($material->due_date);
                    $now = time();
                    if ($dueTime < $now) {
                        $overdueCount++;
                    } elseif ($dueTime < ($now + 7 * 24 * 60 * 60)) {
                        $upcomingCount++;
                    }
                }
            }
            ?>
            
            <?php if ($overdueCount > 0): ?>
            <div class="progress-card overdue">
                <div class="progress-number overdue"><?= $overdueCount ?></div>
                <div class="progress-label">Overdue Items</div>
            </div>
            <?php endif; ?>
            
            <?php if ($upcomingCount > 0): ?>
            <div class="progress-card upcoming">
                <div class="progress-number upcoming"><?= $upcomingCount ?></div>
                <div class="progress-label">Due This Week</div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Current IEP Information -->
        <?php if ($current_iep): ?>
        <div class="iep-info">
            <div class="iep-title">Current IEP Information</div>
            <div class="iep-details">
                <div class="iep-detail">
                    <div class="iep-detail-label">IEP Period</div>
                    <div class="iep-detail-value">
                        <?= date('M j, Y', strtotime($current_iep->start_date)) ?> - 
                        <?= date('M j, Y', strtotime($current_iep->end_date)) ?>
                    </div>
                </div>
                <div class="iep-detail">
                    <div class="iep-detail-label">Created By</div>
                    <div class="iep-detail-value"><?= htmlspecialchars($current_iep->created_by_name) ?></div>
                </div>
                <div class="iep-detail">
                    <div class="iep-detail-label">Status</div>
                    <div class="iep-detail-value">Active</div>
                </div>
                <div class="iep-detail">
                    <div class="iep-detail-label">Days Remaining</div>
                    <div class="iep-detail-value">
                        <?= max(0, ceil((strtotime($current_iep->end_date) - time()) / (24 * 60 * 60))) ?> days
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Progress by Objective -->
        <?php if (!empty($progress_data['objective_progress'])): ?>
        <div class="objectives-progress">
            <div class="objectives-title">Progress by IEP Objective</div>
            
            <?php foreach ($progress_data['objective_progress'] as $objective => $objectiveData): ?>
            <div class="objective-item">
                <div class="objective-header">
                    <div class="objective-name"><?= htmlspecialchars($objective) ?></div>
                    <div class="objective-percentage"><?= $objectiveData['completion_percentage'] ?>%</div>
                </div>
                
                <div class="objective-stats">
                    <div>Materials: <?= $objectiveData['total_materials'] ?></div>
                    <div>Completed: <?= $objectiveData['submitted_materials'] ?></div>
                    <div>Remaining: <?= $objectiveData['total_materials'] - $objectiveData['submitted_materials'] ?></div>
                </div>
                
                <div class="objective-progress-bar">
                    <div class="objective-progress-fill" style="width: <?= $objectiveData['completion_percentage'] ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Materials Timeline -->
        <?php if (!empty($materials)): ?>
        <div class="materials-timeline">
            <div class="timeline-title">Materials Timeline</div>
            
            <?php
            // Sort materials by due date and creation date
            $sortedMaterials = $materials;
            usort($sortedMaterials, function($a, $b) {
                if ($a->due_date && $b->due_date) {
                    return strtotime($a->due_date) - strtotime($b->due_date);
                } elseif ($a->due_date) {
                    return -1;
                } elseif ($b->due_date) {
                    return 1;
                } else {
                    return strtotime($b->created_at) - strtotime($a->created_at);
                }
            });
            
            foreach ($sortedMaterials as $material):
                $isSubmitted = isset($progress_data['submissions_by_material'][$material->id]);
                $isOverdue = false;
                
                if ($material->due_date && !$isSubmitted) {
                    $isOverdue = strtotime($material->due_date) < time();
                }
                
                $status = $isSubmitted ? 'submitted' : ($isOverdue ? 'overdue' : 'pending');
                $statusIcon = $isSubmitted ? '✓' : ($isOverdue ? '!' : '○');
            ?>
            <div class="timeline-item">
                <div class="timeline-marker <?= $status ?>">
                    <?= $statusIcon ?>
                </div>
                <div class="timeline-content">
                    <div class="timeline-material"><?= htmlspecialchars($material->title) ?></div>
                    <div class="timeline-objective"><?= htmlspecialchars($material->iep_objective) ?></div>
                    <?php if ($material->due_date): ?>
                        <div class="timeline-date">
                            Due: <?= date('M j, Y', strtotime($material->due_date)) ?>
                            <?php if ($isSubmitted && isset($progress_data['submissions_by_material'][$material->id])): ?>
                                <?php $submission = $progress_data['submissions_by_material'][$material->id][0]; ?>
                                | Submitted: <?= date('M j, Y', strtotime($submission->submitted_at)) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="materials-timeline">
            <div class="empty-state">
                <div class="empty-state-icon">📊</div>
                <h3>No materials assigned yet</h3>
                <p>
                    <?php if ($is_teacher_view): ?>
                        Upload learning materials to start tracking progress.
                    <?php else: ?>
                        Your SPED teacher will assign materials based on your IEP objectives.
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>