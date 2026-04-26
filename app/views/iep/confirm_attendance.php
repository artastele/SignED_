<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Meeting Attendance - SignED SPED</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .meeting-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .meeting-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #1E40AF;
        }
        
        .meeting-details {
            background: #F3F4F6;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .detail-label {
            font-weight: bold;
            color: #1E40AF;
            width: 120px;
            flex-shrink: 0;
        }
        
        .detail-value {
            flex: 1;
        }
        
        .participants-list {
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
        }
        
        .participant-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .participant-item:last-child {
            border-bottom: none;
        }
        
        .participant-info {
            flex: 1;
        }
        
        .participant-name {
            font-weight: bold;
        }
        
        .participant-role {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-invited {
            background: #FEF3C7;
            color: #92400E;
        }
        
        .status-confirmed {
            background: #D1FAE5;
            color: #065F46;
        }
        
        .status-declined {
            background: #FEE2E2;
            color: #991B1B;
        }
        
        .attendance-form {
            background: #F9FAFB;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            margin: 0 10px;
        }
        
        .btn-confirm {
            background: #10B981;
            color: white;
        }
        
        .btn-confirm:hover {
            background: #059669;
        }
        
        .btn-decline {
            background: #EF4444;
            color: white;
        }
        
        .btn-decline:hover {
            background: #DC2626;
        }
        
        .btn-secondary {
            background: #6B7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4B5563;
        }
        
        .error {
            background: #FEE2E2;
            color: #B91C1C;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .success {
            background: #D1FAE5;
            color: #065F46;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .already-responded {
            background: #E0E7FF;
            color: #3730A3;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>IEP Meeting Attendance</h1>
            <nav>
                <a href="/dashboard">Dashboard</a>
                <a href="/iep/meetings">Meetings</a>
                <a href="/logout">Logout</a>
            </nav>
        </div>

        <div class="meeting-container">
            <?php if (isset($error)): ?>
                <div class="error">
                    <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="meeting-header">
                <h2>IEP Meeting Invitation</h2>
                <p>Learner: <strong><?= htmlspecialchars($meeting->first_name . ' ' . $meeting->last_name) ?></strong></p>
            </div>

            <div class="meeting-details">
                <h3 style="margin-top: 0; color: #1E40AF;">Meeting Details</h3>
                
                <div class="detail-row">
                    <div class="detail-label">Date & Time:</div>
                    <div class="detail-value">
                        <?= date('l, F j, Y \a\t g:i A', strtotime($meeting->meeting_date)) ?>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Location:</div>
                    <div class="detail-value"><?= htmlspecialchars($meeting->location) ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Scheduled by:</div>
                    <div class="detail-value"><?= htmlspecialchars($meeting->scheduled_by_name) ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value">
                        <span class="status-badge status-<?= $meeting->status ?>">
                            <?= ucfirst($meeting->status) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="participants-list">
                <h3 style="margin-top: 0; color: #1E40AF;">Meeting Participants</h3>
                
                <?php 
                $currentUserStatus = null;
                foreach ($participants as $participant): 
                    if ($participant->user_id == $_SESSION['user_id']) {
                        $currentUserStatus = $participant->attendance_status;
                    }
                ?>
                    <div class="participant-item">
                        <div class="participant-info">
                            <div class="participant-name">
                                <?= htmlspecialchars($participant->fullname) ?>
                                <?php if ($participant->user_id == $_SESSION['user_id']): ?>
                                    <em>(You)</em>
                                <?php endif; ?>
                            </div>
                            <div class="participant-role"><?= htmlspecialchars($participant->role) ?></div>
                        </div>
                        <div class="status-badge status-<?= $participant->attendance_status ?>">
                            <?= ucfirst($participant->attendance_status) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($currentUserStatus === 'confirmed' || $currentUserStatus === 'declined'): ?>
                <div class="already-responded">
                    <h3>Response Recorded</h3>
                    <p>You have already <?= $currentUserStatus ?> attendance for this meeting.</p>
                    <?php if ($currentUserStatus === 'confirmed'): ?>
                        <p>Thank you for confirming your attendance. We look forward to seeing you at the meeting.</p>
                    <?php else: ?>
                        <p>If you need to change your response, please contact the meeting organizer.</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="attendance-form">
                    <h3>Please Confirm Your Attendance</h3>
                    <p>Will you be able to attend this IEP meeting?</p>
                    
                    <form method="POST" style="margin-top: 20px;">
                        <button type="submit" name="status" value="confirmed" class="btn btn-confirm">
                            ✓ I will attend
                        </button>
                        <button type="submit" name="status" value="declined" class="btn btn-decline">
                            ✗ I cannot attend
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 30px;">
                <a href="/dashboard" class="btn btn-secondary">Return to Dashboard</a>
            </div>
        </div>
    </div>

    <script>
        // Confirm before declining
        document.addEventListener('DOMContentLoaded', function() {
            const declineBtn = document.querySelector('button[value="declined"]');
            if (declineBtn) {
                declineBtn.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you cannot attend this meeting? This will notify the organizer.')) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
</body>
</html>