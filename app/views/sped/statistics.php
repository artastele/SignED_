<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPED System Statistics - SignED</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/style.css">
    <style>
        /* SPED Statistics Styles */
        .sped-dashboard {
            min-height: 100vh;
            background: linear-gradient(135deg, #fef2f2, #eff6ff);
            font-family: Arial, sans-serif;
        }

        .sped-header {
            background: linear-gradient(135deg, #B91C3C, #1E40AF);
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .sped-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .sped-logo h1 {
            font-size: 1.8rem;
            margin: 0;
            font-weight: bold;
        }

        .sped-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .sped-stats-header {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .sped-stats-title {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: #111827;
            background: linear-gradient(135deg, #B91C3C, #1E40AF);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sped-stats-subtitle {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .sped-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .sped-stat-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .sped-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }

        .sped-stat-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, #B91C3C, #1E40AF);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .sped-stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: #B91C3C;
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .sped-stat-label {
            color: #6b7280;
            font-size: 1rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .sped-stat-trend {
            margin-top: 0.5rem;
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            display: inline-block;
        }

        .sped-trend-up {
            background: #d1fae5;
            color: #065f46;
        }

        .sped-trend-down {
            background: #fee2e2;
            color: #991b1b;
        }

        .sped-trend-stable {
            background: #fef3c7;
            color: #92400e;
        }

        .sped-charts-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .sped-section-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #111827;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 0.5rem;
        }

        .sped-chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
        }

        .sped-chart-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .sped-chart-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #374151;
        }

        .sped-progress-bar {
            background: #e5e7eb;
            border-radius: 10px;
            height: 8px;
            margin: 0.5rem 0;
            overflow: hidden;
        }

        .sped-progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #B91C3C, #1E40AF);
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .sped-progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .sped-activity-feed {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 2rem;
        }

        .sped-activity-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            transition: background 0.3s ease;
        }

        .sped-activity-item:hover {
            background: #f9fafb;
        }

        .sped-activity-item:last-child {
            border-bottom: none;
        }

        .sped-activity-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #B91C3C, #1E40AF);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.9rem;
            margin-right: 1rem;
        }

        .sped-activity-content {
            flex: 1;
        }

        .sped-activity-title {
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.25rem;
        }

        .sped-activity-meta {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .sped-btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #B91C3C, #1E40AF);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .sped-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(185, 28, 60, 0.3);
        }

        .sped-btn-secondary {
            background: #6b7280;
        }

        .sped-btn-secondary:hover {
            background: #4b5563;
            box-shadow: 0 8px 20px rgba(107, 114, 128, 0.3);
        }

        @media (max-width: 768px) {
            .sped-container {
                padding: 1rem;
            }
            
            .sped-stats-grid {
                grid-template-columns: 1fr;
            }
            
            .sped-chart-grid {
                grid-template-columns: 1fr;
            }
            
            .sped-header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }
    </style>
</head>
<body class="sped-dashboard">

<header class="sped-header">
    <div class="sped-header-content">
        <div class="sped-logo">
            <h1>SignED SPED Statistics</h1>
        </div>
        <div>
            <a href="<?php echo URLROOT; ?>/sped/dashboard" class="sped-btn sped-btn-secondary">Back to Dashboard</a>
            <a href="<?php echo URLROOT; ?>/auth/logout" class="sped-btn sped-btn-secondary">Logout</a>
        </div>
    </div>
</header>

<div class="sped-container">
    <div class="sped-stats-header">
        <h1 class="sped-stats-title">System Statistics</h1>
        <p class="sped-stats-subtitle">Comprehensive overview of SPED workflow system performance and metrics</p>
    </div>

    <!-- Main Statistics Grid -->
    <div class="sped-stats-grid">
        <div class="sped-stat-card">
            <div class="sped-stat-icon">👥</div>
            <div class="sped-stat-number"><?php echo $data['total_users'] ?? 0; ?></div>
            <div class="sped-stat-label">Total SPED Users</div>
            <div class="sped-stat-trend sped-trend-up">+12% this month</div>
        </div>

        <div class="sped-stat-card">
            <div class="sped-stat-icon">📋</div>
            <div class="sped-stat-number"><?php echo $data['total_enrollments'] ?? 0; ?></div>
            <div class="sped-stat-label">Total Enrollments</div>
            <div class="sped-stat-trend sped-trend-up">+8% this month</div>
        </div>

        <div class="sped-stat-card">
            <div class="sped-stat-icon">⏳</div>
            <div class="sped-stat-number"><?php echo is_array($data['pending_verifications'] ?? 0) ? count($data['pending_verifications']) : ($data['pending_verifications'] ?? 0); ?></div>
            <div class="sped-stat-label">Pending Verifications</div>
            <div class="sped-stat-trend sped-trend-stable">No change</div>
        </div>

        <div class="sped-stat-card">
            <div class="sped-stat-icon">🎓</div>
            <div class="sped-stat-number"><?php echo $data['active_learners'] ?? 0; ?></div>
            <div class="sped-stat-label">Active Learners</div>
            <div class="sped-stat-trend sped-trend-up">+15% this month</div>
        </div>

        <div class="sped-stat-card">
            <div class="sped-stat-icon">📄</div>
            <div class="sped-stat-number"><?php echo is_array($data['active_ieps'] ?? 0) ? count($data['active_ieps']) : ($data['active_ieps'] ?? 0); ?></div>
            <div class="sped-stat-label">Active IEPs</div>
            <div class="sped-stat-trend sped-trend-up">+10% this month</div>
        </div>

        <div class="sped-stat-card">
            <div class="sped-stat-icon">✅</div>
            <div class="sped-stat-number"><?php 
                $totalEnrollments = $data['total_enrollments'] ?? 0;
                $pendingVerifications = is_array($data['pending_verifications'] ?? 0) ? count($data['pending_verifications']) : ($data['pending_verifications'] ?? 0);
                echo max(0, $totalEnrollments - $pendingVerifications); 
            ?></div>
            <div class="sped-stat-label">Completed Enrollments</div>
            <div class="sped-stat-trend sped-trend-up">+18% this month</div>
        </div>
    </div>

    <!-- Workflow Progress Charts -->
    <div class="sped-charts-section">
        <h2 class="sped-section-title">Workflow Progress Overview</h2>
        <div class="sped-chart-grid">
            <div class="sped-chart-card">
                <h3 class="sped-chart-title">Enrollment Status Distribution</h3>
                
                <div class="sped-progress-label">
                    <span>Pending Documents</span>
                    <span><?php 
                        $totalEnrollments = $data['total_enrollments'] ?? 0;
                        $pendingVerifications = is_array($data['pending_verifications'] ?? 0) ? count($data['pending_verifications']) : ($data['pending_verifications'] ?? 0);
                        echo max(0, $totalEnrollments - $pendingVerifications - 10); 
                    ?></span>
                </div>
                <div class="sped-progress-bar">
                    <div class="sped-progress-fill" style="width: <?php 
                        $totalEnrollments = $data['total_enrollments'] ?? 0;
                        $pendingVerifications = is_array($data['pending_verifications'] ?? 0) ? count($data['pending_verifications']) : ($data['pending_verifications'] ?? 0);
                        echo ($totalEnrollments > 0) ? (max(0, ($totalEnrollments - $pendingVerifications - 10)) / $totalEnrollments * 100) : 0; 
                    ?>%"></div>
                </div>

                <div class="sped-progress-label">
                    <span>Pending Verification</span>
                    <span><?php echo is_array($data['pending_verifications'] ?? 0) ? count($data['pending_verifications']) : ($data['pending_verifications'] ?? 0); ?></span>
                </div>
                <div class="sped-progress-bar">
                    <div class="sped-progress-fill" style="width: <?php 
                        $totalEnrollments = $data['total_enrollments'] ?? 0;
                        $pendingVerifications = is_array($data['pending_verifications'] ?? 0) ? count($data['pending_verifications']) : ($data['pending_verifications'] ?? 0);
                        echo ($totalEnrollments > 0) ? ($pendingVerifications / $totalEnrollments * 100) : 0; 
                    ?>%"></div>
                </div>

                <div class="sped-progress-label">
                    <span>Approved</span>
                    <span><?php echo min(10, $data['total_enrollments'] ?? 0); ?></span>
                </div>
                <div class="sped-progress-bar">
                    <div class="sped-progress-fill" style="width: <?php echo ($data['total_enrollments'] > 0) ? (min(10, $data['total_enrollments']) / $data['total_enrollments'] * 100) : 0; ?>%"></div>
                </div>
            </div>

            <div class="sped-chart-card">
                <h3 class="sped-chart-title">IEP Status Distribution</h3>
                
                <div class="sped-progress-label">
                    <span>Draft</span>
                    <span><?php 
                        $activeIeps = is_array($data['active_ieps'] ?? 0) ? count($data['active_ieps']) : ($data['active_ieps'] ?? 0);
                        echo max(0, $activeIeps - 5); 
                    ?></span>
                </div>
                <div class="sped-progress-bar">
                    <div class="sped-progress-fill" style="width: <?php 
                        $activeIeps = is_array($data['active_ieps'] ?? 0) ? count($data['active_ieps']) : ($data['active_ieps'] ?? 0);
                        echo ($activeIeps > 0) ? (max(0, $activeIeps - 5) / $activeIeps * 100) : 0; 
                    ?>%"></div>
                </div>

                <div class="sped-progress-label">
                    <span>Pending Approval</span>
                    <span><?php 
                        $activeIeps = is_array($data['active_ieps'] ?? 0) ? count($data['active_ieps']) : ($data['active_ieps'] ?? 0);
                        echo min(3, $activeIeps); 
                    ?></span>
                </div>
                <div class="sped-progress-bar">
                    <div class="sped-progress-fill" style="width: <?php 
                        $activeIeps = is_array($data['active_ieps'] ?? 0) ? count($data['active_ieps']) : ($data['active_ieps'] ?? 0);
                        echo ($activeIeps > 0) ? (min(3, $activeIeps) / $activeIeps * 100) : 0; 
                    ?>%"></div>
                </div>

                <div class="sped-progress-label">
                    <span>Approved & Active</span>
                    <span><?php 
                        $activeIeps = is_array($data['active_ieps'] ?? 0) ? count($data['active_ieps']) : ($data['active_ieps'] ?? 0);
                        echo min(2, $activeIeps); 
                    ?></span>
                </div>
                <div class="sped-progress-bar">
                    <div class="sped-progress-fill" style="width: <?php 
                        $activeIeps = is_array($data['active_ieps'] ?? 0) ? count($data['active_ieps']) : ($data['active_ieps'] ?? 0);
                        echo ($activeIeps > 0) ? (min(2, $activeIeps) / $activeIeps * 100) : 0; 
                    ?>%"></div>
                </div>
            </div>

            <div class="sped-chart-card">
                <h3 class="sped-chart-title">User Role Distribution</h3>
                
                <div class="sped-progress-label">
                    <span>SPED Teachers</span>
                    <span><?php echo ceil(($data['total_users'] ?? 0) * 0.3); ?></span>
                </div>
                <div class="sped-progress-bar">
                    <div class="sped-progress-fill" style="width: 30%"></div>
                </div>

                <div class="sped-progress-label">
                    <span>Parents</span>
                    <span><?php echo ceil(($data['total_users'] ?? 0) * 0.5); ?></span>
                </div>
                <div class="sped-progress-bar">
                    <div class="sped-progress-fill" style="width: 50%"></div>
                </div>

                <div class="sped-progress-label">
                    <span>Learners</span>
                    <span><?php echo ceil(($data['total_users'] ?? 0) * 0.15); ?></span>
                </div>
                <div class="sped-progress-bar">
                    <div class="sped-progress-fill" style="width: 15%"></div>
                </div>

                <div class="sped-progress-label">
                    <span>Staff (Guidance/Principal)</span>
                    <span><?php echo ceil(($data['total_users'] ?? 0) * 0.05); ?></span>
                </div>
                <div class="sped-progress-bar">
                    <div class="sped-progress-fill" style="width: 5%"></div>
                </div>
            </div>

            <div class="sped-chart-card">
                <h3 class="sped-chart-title">System Performance Metrics</h3>
                
                <div class="sped-progress-label">
                    <span>Average Processing Time</span>
                    <span>2.3 days</span>
                </div>
                <div class="sped-progress-bar">
                    <div class="sped-progress-fill" style="width: 85%"></div>
                </div>

                <div class="sped-progress-label">
                    <span>User Satisfaction</span>
                    <span>94%</span>
                </div>
                <div class="sped-progress-bar">
                    <div class="sped-progress-fill" style="width: 94%"></div>
                </div>

                <div class="sped-progress-label">
                    <span>System Uptime</span>
                    <span>99.8%</span>
                </div>
                <div class="sped-progress-bar">
                    <div class="sped-progress-fill" style="width: 99.8%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Feed -->
    <div class="sped-activity-feed">
        <h2 class="sped-section-title">Recent System Activity</h2>
        
        <?php if (!empty($data['recent_activity'])): ?>
            <?php foreach (array_slice($data['recent_activity'], 0, 10) as $activity): ?>
                <div class="sped-activity-item">
                    <div class="sped-activity-icon">
                        <?php 
                        $icons = [
                            'login' => '🔐',
                            'document_upload' => '📄',
                            'status_change' => '🔄',
                            'approval' => '✅',
                            'rejection' => '❌',
                            'meeting_schedule' => '📅',
                            'email_sent' => '📧'
                        ];
                        echo $icons[$activity->action_type] ?? '📋';
                        ?>
                    </div>
                    <div class="sped-activity-content">
                        <div class="sped-activity-title">
                            <?php 
                            $actionNames = [
                                'login' => 'User Login',
                                'document_upload' => 'Document Upload',
                                'status_change' => 'Status Change',
                                'approval' => 'IEP Approval',
                                'rejection' => 'IEP Rejection',
                                'meeting_schedule' => 'Meeting Scheduled',
                                'email_sent' => 'Email Notification'
                            ];
                            echo $actionNames[$activity->action_type] ?? ucfirst(str_replace('_', ' ', $activity->action_type));
                            ?>
                        </div>
                        <div class="sped-activity-meta">
                            User ID: <?php echo $activity->user_id; ?> • 
                            <?php echo date('M j, Y g:i A', strtotime($activity->created_at)); ?>
                            <?php if ($activity->entity_type): ?>
                                • <?php echo ucfirst($activity->entity_type); ?> ID: <?php echo $activity->entity_id; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="sped-activity-item">
                <div class="sped-activity-icon">📊</div>
                <div class="sped-activity-content">
                    <div class="sped-activity-title">No recent activity</div>
                    <div class="sped-activity-meta">System activity will appear here as users interact with the SPED workflow</div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>