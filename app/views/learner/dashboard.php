<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learner Dashboard - SignED SPED</title>
    <link rel="stylesheet" href="/public/assets/css/style.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #1E40AF;
        }
        
        .dashboard-card.progress {
            border-left-color: #059669;
        }
        
        .dashboard-card.overdue {
            border-left-color: #DC2626;
        }
        
        .dashboard-card.upcoming {
            border-left-color: #D97706;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1E40AF;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #6B7280;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #E5E7EB;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .progress-fill {
            height: 100%;
            background-color: #059669;
            transition: width 0.3s ease;
        }
        
        .materials-list {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .material-item {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #E5E7EB;
            transition: background-color 0.2s;
        }
        
        .material-item:hover {
            background-color: #F9FAFB;
        }
        
        .material-item:last-child {
            border-bottom: none;
        }
        
        .material-info {
            flex: 1;
        }
        
        .material-title {
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 5px;
        }
        
        .material-objective {
            color: #6B7280;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .material-due {
            font-size: 0.8rem;
            color: #9CA3AF;
        }
        
        .material-status {
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
        
        .status-overdue {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        .welcome-section {
            background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .welcome-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .welcome-subtitle {
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            background-color: #1E40AF;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.2s;
            display: inline-block;
        }
        
        .btn-primary:hover {
            background-color: #1D4ED8;
        }
        
        .btn-secondary {
            background-color: white;
            color: #1E40AF;
            padding: 12px 24px;
            border: 2px solid #1E40AF;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            display: inline-block;
        }
        
        .btn-secondary:hover {
            background-color: #1E40AF;
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6B7280;
        }
        
        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .material-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="nav-brand">
                <h1>SignED SPED</h1>
            </div>
            <div class="nav-links">
                <a href="/learner/dashboard" class="active">Dashboard</a>
                <a href="/learner/materials">My Materials</a>
                <a href="/learner/track-progress">Progress</a>
                <a href="/auth/logout">Logout</a>
            </div>
        </nav>

        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-title">Welcome back, <?= htmlspecialchars($learner->first_name) ?>!</div>
            <div class="welcome-subtitle">
                <?php if ($current_iep): ?>
                    Your IEP is active. Keep up the great work on your learning objectives!
                <?php else: ?>
                    Your learning plan is being prepared. Check back soon for new materials.
                <?php endif; ?>
            </div>
            <div class="action-buttons">
                <a href="/learner/materials" class="btn-primary">View My Materials</a>
                <a href="/learner/track-progress" class="btn-secondary">Check Progress</a>
            </div>
        </div>

        <!-- Statistics Dashboard -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="stat-number"><?= $stats['total_materials'] ?></div>
                <div class="stat-label">Total Materials</div>
            </div>
            
            <div class="dashboard-card progress">
                <div class="stat-number"><?= $stats['completion_percentage'] ?>%</div>
                <div class="stat-label">Completion Rate</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $stats['completion_percentage'] ?>%"></div>
                </div>
            </div>
            
            <?php if ($stats['overdue_count'] > 0): ?>
            <div class="dashboard-card overdue">
                <div class="stat-number"><?= $stats['overdue_count'] ?></div>
                <div class="stat-label">Overdue Items</div>
            </div>
            <?php endif; ?>
            
            <?php if ($stats['upcoming_count'] > 0): ?>
            <div class="dashboard-card upcoming">
                <div class="stat-number"><?= $stats['upcoming_count'] ?></div>
                <div class="stat-label">Due This Week</div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Recent Materials -->
        <div class="materials-list">
            <h2 style="margin-bottom: 20px; color: #1F2937;">Recent Learning Materials</h2>
            
            <?php if (empty($materials)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📚</div>
                    <h3>No materials assigned yet</h3>
                    <p>Your SPED teacher will assign learning materials based on your IEP objectives.</p>
                </div>
            <?php else: ?>
                <?php 
                $recentMaterials = array_slice($materials, 0, 5); // Show only 5 most recent
                foreach ($recentMaterials as $material): 
                    $isSubmitted = false;
                    foreach ($submissions as $submission) {
                        if ($submission->material_id == $material->id) {
                            $isSubmitted = true;
                            break;
                        }
                    }
                    
                    $isOverdue = false;
                    if ($material->due_date && !$isSubmitted) {
                        $isOverdue = strtotime($material->due_date) < time();
                    }
                ?>
                <div class="material-item">
                    <div class="material-info">
                        <div class="material-title"><?= htmlspecialchars($material->title) ?></div>
                        <div class="material-objective"><?= htmlspecialchars($material->iep_objective) ?></div>
                        <?php if ($material->due_date): ?>
                            <div class="material-due">
                                Due: <?= date('M j, Y', strtotime($material->due_date)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="material-status <?= $isSubmitted ? 'status-submitted' : ($isOverdue ? 'status-overdue' : 'status-pending') ?>">
                        <?= $isSubmitted ? 'Submitted' : ($isOverdue ? 'Overdue' : 'Pending') ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (count($materials) > 5): ?>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="/learner/materials" class="btn-primary">View All Materials</a>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Recent Submissions -->
        <?php if (!empty($submissions)): ?>
        <div class="materials-list" style="margin-top: 30px;">
            <h2 style="margin-bottom: 20px; color: #1F2937;">Recent Submissions</h2>
            
            <?php 
            $recentSubmissions = array_slice($submissions, 0, 3); // Show only 3 most recent
            foreach ($recentSubmissions as $submission): 
            ?>
            <div class="material-item">
                <div class="material-info">
                    <div class="material-title"><?= htmlspecialchars($submission->material_title) ?></div>
                    <div class="material-objective">
                        Submitted: <?= date('M j, Y \a\t g:i A', strtotime($submission->submitted_at)) ?>
                    </div>
                    <?php if ($submission->reviewed_at): ?>
                        <div class="material-due">
                            Reviewed: <?= date('M j, Y', strtotime($submission->reviewed_at)) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="material-status <?= $submission->reviewed_at ? 'status-submitted' : 'status-pending' ?>">
                    <?= $submission->reviewed_at ? 'Reviewed' : 'Under Review' ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- IEP Information -->
        <?php if ($current_iep): ?>
        <div class="materials-list" style="margin-top: 30px;">
            <h2 style="margin-bottom: 20px; color: #1F2937;">Current IEP Information</h2>
            <div class="material-item">
                <div class="material-info">
                    <div class="material-title">IEP Period</div>
                    <div class="material-objective">
                        <?= date('M j, Y', strtotime($current_iep->start_date)) ?> - 
                        <?= date('M j, Y', strtotime($current_iep->end_date)) ?>
                    </div>
                    <div class="material-due">
                        Created by: <?= htmlspecialchars($current_iep->created_by_name) ?>
                    </div>
                </div>
                <div class="material-status status-submitted">Active</div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <?php
        switch ($_GET['success']) {
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
            document.querySelector('.alert').style.display = 'none';
        }, 5000);
    </script>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <?php
        switch ($_GET['error']) {
            case 'learner_not_found':
                echo 'Learner account not found.';
                break;
            case 'access_denied':
                echo 'Access denied.';
                break;
            default:
                echo 'An error occurred. Please try again.';
        }
        ?>
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.alert').style.display = 'none';
        }, 5000);
    </script>
    <?php endif; ?>
</body>
</html>