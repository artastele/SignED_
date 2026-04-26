<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IEP Meetings - SignED SPED</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .meetings-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #1E40AF;
        }
        
        .page-title {
            color: #1E40AF;
            margin: 0;
        }
        
        .header-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #1E40AF;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1D4ED8;
        }
        
        .btn-secondary {
            background: #6B7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4B5563;
        }
        
        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .success-message {
            background: #D1FAE5;
            color: #065F46;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .error-message {
            background: #FEE2E2;
            color: #B91C1C;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .meetings-grid {
            display: grid;
            gap: 20px;
        }
        
        .meeting-card {
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: box-shadow 0.2s;
        }
        
        .meeting-card:hover {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .meeting-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .meeting-title {
            margin: 0;
            color: #1F2937;
            font-size: 18px;
        }
        
        .meeting-status {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-scheduled {
            background: #FEF3C7;
            color: #92400E;
        }
        
        .status-confirmed {
            background: #D1FAE5;
            color: #065F46;
        }
        
        .status-completed {
            background: #E0E7FF;
            color: #3730A3;
        }
        
        .status-cancelled {
            background: #FEE2E2;
            color: #991B1B;
        }
        
        .meeting-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        
        .detail-icon {
            width: 16px;
            height: 16px;
            color: #6B7280;
        }
        
        .detail-label {
            font-weight: bold;
            color: #374151;
        }
        
        .detail-value {
            color: #6B7280;
        }
        
        .meeting-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #E5E7EB;
        }
        
        .participants-list {
            margin-top: 10px;
        }
        
        .participants-summary {
            font-size: 12px;
            color: #6B7280;
        }
        
        .participant-item {
            display: inline-block;
            background: #F3F4F6;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            margin: 2px;
        }
        
        .no-meetings {
            text-align: center;
            padding: 60px 20px;
            color: #6B7280;
        }
        
        .no-meetings-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .filters {
            background: #F9FAFB;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .filter-group label {
            font-size: 14px;
            font-weight: bold;
            color: #374151;
        }
        
        .filter-group select {
            padding: 6px 10px;
            border: 1px solid #D1D5DB;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>IEP Meetings</h1>
            <nav>
                <a href="/dashboard">Dashboard</a>
                <a href="/iep/list">IEP Documents</a>
                <a href="/logout">Logout</a>
            </nav>
        </div>

        <div class="meetings-container">
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">
                    <?php
                    switch ($_GET['success']) {
                        case 'scheduled':
                            echo 'Meeting has been successfully scheduled and participants have been notified.';
                            break;
                        case 'meeting_recorded':
                            echo 'Meeting completion has been recorded successfully.';
                            break;
                        case 'attendance_confirmed':
                            echo 'Your attendance has been confirmed.';
                            break;
                        case 'attendance_declined':
                            echo 'Your response has been recorded. The organizer has been notified.';
                            break;
                        default:
                            echo 'Action completed successfully.';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php
                    switch ($_GET['error']) {
                        case 'invalid_meeting':
                            echo 'Invalid meeting ID provided.';
                            break;
                        case 'meeting_not_found':
                            echo 'Meeting not found.';
                            break;
                        case 'not_participant':
                            echo 'You are not a participant in this meeting.';
                            break;
                        case 'meeting_not_ready':
                            echo 'Meeting is not ready for this action.';
                            break;
                        default:
                            echo 'An error occurred. Please try again.';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <div class="page-header">
                <h2 class="page-title">
                    <?php if ($user_role === 'sped_teacher'): ?>
                        Scheduled Meetings
                    <?php else: ?>
                        My Meeting Invitations
                    <?php endif; ?>
                </h2>
                
                <div class="header-actions">
                    <?php if ($user_role === 'sped_teacher'): ?>
                        <a href="/iep/schedule-meeting" class="btn btn-primary">
                            + Schedule New Meeting
                        </a>
                    <?php endif; ?>
                    <a href="/dashboard" class="btn btn-secondary">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <?php if ($user_role === 'sped_teacher'): ?>
                <div class="filters">
                    <div class="filter-group">
                        <label for="statusFilter">Filter by Status:</label>
                        <select id="statusFilter" onchange="filterMeetings()">
                            <option value="">All Meetings</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
            <?php endif; ?>

            <div class="meetings-grid">
                <?php if (empty($meetings)): ?>
                    <div class="no-meetings">
                        <div class="no-meetings-icon">📅</div>
                        <h3>No Meetings Found</h3>
                        <p>
                            <?php if ($user_role === 'sped_teacher'): ?>
                                No meetings have been scheduled yet. Click "Schedule New Meeting" to get started.
                            <?php else: ?>
                                You have no meeting invitations at this time.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <?php foreach ($meetings as $meeting): ?>
                        <div class="meeting-card" data-status="<?= $meeting->status ?>">
                            <div class="meeting-header">
                                <h3 class="meeting-title">
                                    IEP Meeting - <?= htmlspecialchars($meeting->first_name . ' ' . $meeting->last_name) ?>
                                </h3>
                                <span class="meeting-status status-<?= $meeting->status ?>">
                                    <?= ucfirst($meeting->status) ?>
                                </span>
                            </div>

                            <div class="meeting-details">
                                <div class="detail-item">
                                    <span class="detail-icon">📅</span>
                                    <span class="detail-label">Date:</span>
                                    <span class="detail-value">
                                        <?= date('M j, Y', strtotime($meeting->meeting_date)) ?>
                                    </span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-icon">🕐</span>
                                    <span class="detail-label">Time:</span>
                                    <span class="detail-value">
                                        <?= date('g:i A', strtotime($meeting->meeting_date)) ?>
                                    </span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-icon">📍</span>
                                    <span class="detail-label">Location:</span>
                                    <span class="detail-value">
                                        <?= htmlspecialchars($meeting->location) ?>
                                    </span>
                                </div>
                                
                                <?php if (isset($meeting->scheduled_by_name)): ?>
                                    <div class="detail-item">
                                        <span class="detail-icon">👤</span>
                                        <span class="detail-label">Organizer:</span>
                                        <span class="detail-value">
                                            <?= htmlspecialchars($meeting->scheduled_by_name) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (isset($meeting->role)): ?>
                                <div class="participants-list">
                                    <div class="participants-summary">
                                        Your role: <span class="participant-item"><?= ucfirst($meeting->role) ?></span>
                                        Status: <span class="participant-item"><?= ucfirst($meeting->attendance_status) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="meeting-actions">
                                <?php if ($user_role === 'sped_teacher'): ?>
                                    <?php if ($meeting->status === 'confirmed'): ?>
                                        <a href="/iep/record-meeting/<?= $meeting->id ?>" class="btn btn-primary btn-small">
                                            Record Completion
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($meeting->status === 'completed'): ?>
                                        <a href="/iep/create/<?= $meeting->learner_id ?>" class="btn btn-primary btn-small">
                                            Create IEP
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="/iep/meeting-details/<?= $meeting->id ?>" class="btn btn-secondary btn-small">
                                        View Details
                                    </a>
                                <?php else: ?>
                                    <?php if ($meeting->attendance_status === 'invited'): ?>
                                        <a href="/iep/confirm-attendance/<?= $meeting->id ?>" class="btn btn-primary btn-small">
                                            Respond to Invitation
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="/iep/meeting-details/<?= $meeting->id ?>" class="btn btn-secondary btn-small">
                                        View Details
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function filterMeetings() {
            const filter = document.getElementById('statusFilter').value;
            const cards = document.querySelectorAll('.meeting-card');
            
            cards.forEach(card => {
                const status = card.dataset.status;
                if (filter === '' || status === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Auto-refresh page every 5 minutes to show updated meeting statuses
        setTimeout(() => {
            window.location.reload();
        }, 300000); // 5 minutes
    </script>
</body>
</html>