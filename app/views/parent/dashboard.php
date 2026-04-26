<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard - SignED</title>
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

        .dashboard-header {
            margin-bottom: 30px;
        }

        .dashboard-header h1 {
            font-size: 28px;
            color: #111827;
            margin-bottom: 5px;
        }

        .dashboard-header p {
            color: #6b7280;
            font-size: 14px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .card-header h3 {
            font-size: 18px;
            color: #111827;
        }

        .badge {
            background: #3b82f6;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge.urgent {
            background: #ef4444;
        }

        .badge.high {
            background: #f59e0b;
        }

        .announcement-item {
            padding: 12px;
            border-left: 3px solid #3b82f6;
            background: #eff6ff;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .announcement-item.urgent {
            border-left-color: #ef4444;
            background: #fef2f2;
        }

        .announcement-item.high {
            border-left-color: #f59e0b;
            background: #fffbeb;
        }

        .announcement-item h4 {
            font-size: 14px;
            color: #111827;
            margin-bottom: 5px;
        }

        .announcement-item p {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.5;
        }

        .announcement-item .date {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 5px;
        }

        .checklist {
            list-style: none;
        }

        .checklist-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .checklist-item:last-child {
            border-bottom: none;
        }

        .checklist-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #d1d5db;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .checklist-icon.checked {
            background: #10b981;
            border-color: #10b981;
            color: white;
        }

        .checklist-text {
            flex: 1;
            font-size: 14px;
            color: #374151;
        }

        .checklist-text.completed {
            text-decoration: line-through;
            color: #9ca3af;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-secondary {
            background: #6b7280;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-success {
            background: #10b981;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-full {
            width: 100%;
            margin-top: 15px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .empty-state h3 {
            font-size: 18px;
            color: #374151;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 14px;
            margin-bottom: 20px;
        }

        .progress-tracker {
            display: none;
        }

        .progress-tracker.active {
            display: block;
        }

        .progress-step {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .progress-number {
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
            margin-right: 12px;
        }

        .progress-number.active {
            background: #3b82f6;
            color: white;
        }

        .progress-number.completed {
            background: #10b981;
            color: white;
        }

        .progress-info {
            flex: 1;
        }

        .progress-info h4 {
            font-size: 14px;
            color: #111827;
            margin-bottom: 2px;
        }

        .progress-info p {
            font-size: 12px;
            color: #6b7280;
        }

        .learner-card {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .learner-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 600;
            margin-right: 15px;
        }

        .learner-info {
            flex: 1;
        }

        .learner-info h4 {
            font-size: 16px;
            color: #111827;
            margin-bottom: 3px;
        }

        .learner-info p {
            font-size: 13px;
            color: #6b7280;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.enrolled {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.pending {
            background: #fef3c7;
            color: #92400e;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<?php include '../app/views/partials/simple_popup.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<div class="dashboard-container">
    <div class="main-content">
        <div class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?>! 👋</h1>
            <p>Manage your child's SPED enrollment and track their progress</p>
        </div>

        <div class="dashboard-grid">
            <!-- Announcements Card -->
            <div class="card">
                <div class="card-header">
                    <h3>📢 Announcements</h3>
                </div>
                <?php if (!empty($data['announcements'])): ?>
                    <?php foreach ($data['announcements'] as $announcement): ?>
                        <div class="announcement-item <?php echo strtolower($announcement->priority); ?>">
                            <h4><?php echo htmlspecialchars($announcement->title); ?></h4>
                            <p><?php echo htmlspecialchars($announcement->content); ?></p>
                            <div class="date">Posted: <?php echo date('M d, Y', strtotime($announcement->created_at)); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No announcements at this time</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Enrollment Checklist Card -->
            <div class="card">
                <div class="card-header">
                    <h3>📋 Enrollment Checklist</h3>
                </div>
                <ul class="checklist">
                    <li class="checklist-item">
                        <div class="checklist-icon">📄</div>
                        <div class="checklist-text">BEEF Form (Basic Education Enrollment Form)</div>
                    </li>
                    <li class="checklist-item">
                        <div class="checklist-icon">🎂</div>
                        <div class="checklist-text">PSA Birth Certificate</div>
                    </li>
                    <li class="checklist-item">
                        <div class="checklist-icon">🆔</div>
                        <div class="checklist-text">PWD ID Card (if available)</div>
                    </li>
                    <li class="checklist-item">
                        <div class="checklist-icon">🏥</div>
                        <div class="checklist-text">Medical Records (if available)</div>
                    </li>
                </ul>

                <?php if (!$data['has_enrollments']): ?>
                    <a href="<?php echo URLROOT; ?>/enrollment/beef" class="btn btn-full">
                        🎒 Enroll Child
                    </a>
                <?php else: ?>
                    <a href="<?php echo URLROOT; ?>/parent/manageRequirements" class="btn btn-secondary btn-full">
                        📁 Manage Requirements
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Progress Tracker (shows after enrollment starts) -->
        <?php if ($data['has_enrollments']): ?>
            <div class="card progress-tracker active">
                <div class="card-header">
                    <h3>📊 Enrollment Progress</h3>
                </div>
                <?php foreach ($data['enrollments'] as $enrollment): ?>
                    <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb;">
                        <h4 style="margin-bottom: 15px; color: #374151;">
                            <?php echo htmlspecialchars($enrollment->learner_first_name . ' ' . $enrollment->learner_last_name); ?>
                        </h4>
                        
                        <div class="progress-step">
                            <div class="progress-number <?php echo in_array($enrollment->status, ['pending_documents', 'pending_verification', 'approved', 'rejected']) ? 'completed' : ''; ?>">1</div>
                            <div class="progress-info">
                                <h4>BEEF Form Submitted</h4>
                                <p>Basic enrollment information completed</p>
                            </div>
                        </div>

                        <div class="progress-step">
                            <div class="progress-number <?php echo in_array($enrollment->status, ['pending_verification', 'approved', 'rejected']) ? 'completed' : ($enrollment->status == 'pending_documents' ? 'active' : ''); ?>">2</div>
                            <div class="progress-info">
                                <h4>Upload Requirements</h4>
                                <p><?php echo $enrollment->document_count; ?> of 4 documents uploaded</p>
                            </div>
                        </div>

                        <div class="progress-step">
                            <div class="progress-number <?php echo in_array($enrollment->status, ['approved', 'rejected']) ? 'completed' : ($enrollment->status == 'pending_verification' ? 'active' : ''); ?>">3</div>
                            <div class="progress-info">
                                <h4>SPED Verification</h4>
                                <p>Waiting for SPED teacher review</p>
                            </div>
                        </div>

                        <div class="progress-step">
                            <div class="progress-number <?php echo $enrollment->status == 'approved' ? 'completed' : ''; ?>">4</div>
                            <div class="progress-info">
                                <h4>Enrollment Complete</h4>
                                <p>
                                    <?php if ($enrollment->status == 'approved'): ?>
                                        ✅ Approved
                                    <?php elseif ($enrollment->status == 'rejected'): ?>
                                        ❌ Rejected: <?php echo htmlspecialchars($enrollment->rejection_reason ?? 'No reason provided'); ?>
                                    <?php else: ?>
                                        Pending approval
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <?php if ($enrollment->status == 'pending_documents'): ?>
                            <a href="<?php echo URLROOT; ?>/enrollment/upload?id=<?php echo $enrollment->id; ?>" class="btn btn-success btn-full">
                                📤 Upload Documents
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Enrolled Learners Card -->
        <?php if (!empty($data['learners'])): ?>
            <div class="card">
                <div class="card-header">
                    <h3>👨‍👩‍👧 My Children</h3>
                </div>
                <?php foreach ($data['learners'] as $learner): ?>
                    <div class="learner-card">
                        <div class="learner-avatar">
                            <?php echo strtoupper(substr($learner->first_name, 0, 1)); ?>
                        </div>
                        <div class="learner-info">
                            <h4><?php echo htmlspecialchars($learner->first_name . ' ' . $learner->last_name); ?></h4>
                            <p>Grade <?php echo htmlspecialchars($learner->grade_level); ?> • <?php echo date('M d, Y', strtotime($learner->date_of_birth)); ?></p>
                        </div>
                        <span class="status-badge enrolled">Enrolled</span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
