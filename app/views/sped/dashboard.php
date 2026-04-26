<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPED Dashboard - SignED</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/style.css">
    <style>
        /* SPED Dashboard Styles with Logo-based Color Scheme */
        .sped-dashboard {
            min-height: 100vh;
            background: linear-gradient(135deg, #fef2f2, #eff6ff);
            font-family: Arial, sans-serif;
        }

        .sped-header {
            background: linear-gradient(135deg, #a01422, #1e4072);
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

        .sped-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sped-logo h1 {
            font-size: 1.8rem;
            margin: 0;
            font-weight: bold;
        }

        .sped-logo .subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
        }

        .sped-user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sped-role-badge {
            background: rgba(255,255,255,0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: bold;
        }

        .sped-main {
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            gap: 2rem;
            padding: 2rem;
        }

        .sped-sidebar {
            width: 280px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 1.5rem;
            height: fit-content;
        }

        .sped-content {
            flex: 1;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 2rem;
        }

        .sped-nav-item {
            display: block;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            text-decoration: none;
            color: #374151;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }

        .sped-nav-item:hover {
            background: #f3f4f6;
            color: #a01422;
        }

        .sped-nav-item.active {
            background: linear-gradient(135deg, #a01422, #1e4072);
            color: white;
        }

        .sped-nav-badge {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            background: #ef4444;
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            min-width: 1.2rem;
            text-align: center;
        }

        .sped-nav-item.active .sped-nav-badge {
            background: rgba(255,255,255,0.3);
        }

        .sped-dashboard-title {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sped-dashboard-subtitle {
            color: #6b7280;
            margin-bottom: 2rem;
            font-size: 1rem;
        }

        .sped-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .sped-stat-card {
            background: linear-gradient(135deg, #fef2f2, #eff6ff);
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .sped-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .sped-stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #a01422;
            margin-bottom: 0.5rem;
        }

        .sped-stat-label {
            color: #6b7280;
            font-size: 0.9rem;
            text-transform: uppercase;
            font-weight: 600;
        }

        .sped-section {
            margin-bottom: 2rem;
        }

        .sped-section-title {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: #111827;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 0.5rem;
        }

        .sped-list {
            list-style: none;
            padding: 0;
        }

        .sped-list-item {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .sped-list-item-content {
            flex: 1;
        }

        .sped-list-item-title {
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.25rem;
        }

        .sped-list-item-meta {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .sped-status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .sped-status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .sped-status-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .sped-status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .sped-btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #a01422, #1e4072);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .sped-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(160, 20, 34, 0.3);
        }

        .sped-btn-secondary {
            background: #6b7280;
        }

        .sped-btn-secondary:hover {
            background: #4b5563;
            box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
        }

        .sped-empty-state {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
        }

        .sped-empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .sped-main {
                flex-direction: column;
                padding: 1rem;
            }
            
            .sped-sidebar {
                width: 100%;
            }
            
            .sped-header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .sped-stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="sped-dashboard">

<header class="sped-header">
    <div class="sped-header-content">
        <div class="sped-logo">
            <div>
                <h1>SignED SPED</h1>
                <p class="subtitle">Special Education Management</p>
            </div>
        </div>
        <div class="sped-user-info">
            <span class="sped-role-badge"><?php echo ucfirst(str_replace('_', ' ', $data['role'])); ?></span>
            <span><?php echo htmlspecialchars($data['user_name']); ?></span>
            <a href="<?php echo URLROOT; ?>/auth/logout" class="sped-btn sped-btn-secondary">Logout</a>
        </div>
    </div>
</header>

<main class="sped-main">
    <aside class="sped-sidebar">
        <nav>
            <?php if (isset($data['navigation'])): ?>
                <?php foreach ($data['navigation'] as $item): ?>
                    <a href="<?php echo $item['url']; ?>" 
                       class="sped-nav-item <?php echo isset($item['active']) && $item['active'] ? 'active' : ''; ?> <?php echo isset($item['class']) ? $item['class'] : ''; ?>">
                        <?php echo htmlspecialchars($item['title']); ?>
                        <?php if (isset($item['badge']) && $item['badge'] > 0): ?>
                            <span class="sped-nav-badge"><?php echo $item['badge']; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </nav>
    </aside>

    <section class="sped-content">
        <h1 class="sped-dashboard-title">
            <?php 
            $roleNames = [
                'sped_teacher' => 'SPED Teacher Dashboard',
                'guidance' => 'Guidance Counselor Dashboard', 
                'principal' => 'Principal Dashboard',
                'admin' => 'Administrator Dashboard'
            ];
            echo $roleNames[$data['role']] ?? 'SPED Dashboard';
            ?>
        </h1>
        <p class="sped-dashboard-subtitle">Welcome back, <?php echo htmlspecialchars($data['user_name']); ?>. Here's your SPED workflow overview.</p>

        <?php if ($data['role'] === 'admin'): ?>
            <!-- Admin Statistics -->
            <div class="sped-stats-grid">
                <div class="sped-stat-card">
                    <div class="sped-stat-number"><?php echo $data['total_enrollments'] ?? 0; ?></div>
                    <div class="sped-stat-label">Total Enrollments</div>
                </div>
                <div class="sped-stat-card">
                    <div class="sped-stat-number"><?php echo $data['pending_verifications'] ?? 0; ?></div>
                    <div class="sped-stat-label">Pending Verifications</div>
                </div>
                <div class="sped-stat-card">
                    <div class="sped-stat-number"><?php echo $data['active_learners'] ?? 0; ?></div>
                    <div class="sped-stat-label">Active Learners</div>
                </div>
                <div class="sped-stat-card">
                    <div class="sped-stat-number"><?php echo $data['active_ieps'] ?? 0; ?></div>
                    <div class="sped-stat-label">Active IEPs</div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($data['role'] === 'sped_teacher'): ?>
            <!-- SPED Teacher Dashboard -->
            <div class="sped-stats-grid">
                <div class="sped-stat-card">
                    <div class="sped-stat-number"><?php echo count($data['pending_verifications'] ?? []); ?></div>
                    <div class="sped-stat-label">Pending Verifications</div>
                </div>
                <div class="sped-stat-card">
                    <div class="sped-stat-number"><?php echo count($data['pending_assessments'] ?? []); ?></div>
                    <div class="sped-stat-label">Pending Assessments</div>
                </div>
                <div class="sped-stat-card">
                    <div class="sped-stat-number"><?php echo count($data['active_ieps'] ?? []); ?></div>
                    <div class="sped-stat-label">Active IEPs</div>
                </div>
            </div>

            <div class="sped-section">
                <h2 class="sped-section-title">Pending Verifications</h2>
                <?php if (!empty($data['pending_verifications'])): ?>
                    <ul class="sped-list">
                        <?php foreach (array_slice($data['pending_verifications'], 0, 5) as $enrollment): ?>
                            <li class="sped-list-item">
                                <div class="sped-list-item-content">
                                    <div class="sped-list-item-title">
                                        <?php echo htmlspecialchars($enrollment->learner_first_name . ' ' . $enrollment->learner_last_name); ?>
                                    </div>
                                    <div class="sped-list-item-meta">
                                        Submitted: <?php echo date('M j, Y', strtotime($enrollment->created_at)); ?>
                                    </div>
                                </div>
                                <a href="<?php echo URLROOT; ?>/enrollment/verify?id=<?php echo $enrollment->id; ?>" class="sped-btn">Review</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (count($data['pending_verifications']) > 5): ?>
                        <p><a href="<?php echo URLROOT; ?>/enrollment/verify" class="sped-btn">View All (<?php echo count($data['pending_verifications']); ?>)</a></p>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="sped-empty-state">
                        <div class="sped-empty-state-icon">✓</div>
                        <p>No pending verifications</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="sped-section">
                <h2 class="sped-section-title">Recent Submissions</h2>
                <?php if (!empty($data['recent_submissions'])): ?>
                    <ul class="sped-list">
                        <?php foreach ($data['recent_submissions'] as $submission): ?>
                            <li class="sped-list-item">
                                <div class="sped-list-item-content">
                                    <div class="sped-list-item-title">
                                        <?php echo htmlspecialchars($submission->first_name . ' ' . $submission->last_name); ?>
                                    </div>
                                    <div class="sped-list-item-meta">
                                        Material: <?php echo htmlspecialchars($submission->material_title); ?> • 
                                        <?php echo date('M j, Y g:i A', strtotime($submission->submitted_at)); ?>
                                    </div>
                                </div>
                                <a href="<?php echo URLROOT; ?>/learner/submissions?id=<?php echo $submission->id; ?>" class="sped-btn">Review</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="sped-empty-state">
                        <div class="sped-empty-state-icon">📝</div>
                        <p>No recent submissions</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($data['role'] === 'guidance'): ?>
            <!-- Guidance Counselor Dashboard -->
            <div class="sped-stats-grid">
                <div class="sped-stat-card">
                    <div class="sped-stat-number"><?php echo count($data['scheduled_meetings'] ?? []); ?></div>
                    <div class="sped-stat-label">Scheduled Meetings</div>
                </div>
                <div class="sped-stat-card">
                    <div class="sped-stat-number"><?php echo count($data['confirmed_meetings'] ?? []); ?></div>
                    <div class="sped-stat-label">Confirmed Meetings</div>
                </div>
            </div>

            <div class="sped-section">
                <h2 class="sped-section-title">Upcoming IEP Meetings</h2>
                <?php if (!empty($data['upcoming_meetings'])): ?>
                    <ul class="sped-list">
                        <?php foreach ($data['upcoming_meetings'] as $meeting): ?>
                            <li class="sped-list-item">
                                <div class="sped-list-item-content">
                                    <div class="sped-list-item-title">
                                        IEP Meeting - Learner ID: <?php echo $meeting->learner_id; ?>
                                    </div>
                                    <div class="sped-list-item-meta">
                                        <?php echo date('M j, Y g:i A', strtotime($meeting->meeting_date)); ?> • 
                                        <?php echo htmlspecialchars($meeting->location); ?>
                                    </div>
                                </div>
                                <span class="sped-status-badge sped-status-<?php echo $meeting->status; ?>">
                                    <?php echo ucfirst($meeting->status); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="sped-empty-state">
                        <div class="sped-empty-state-icon">📅</div>
                        <p>No upcoming meetings</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($data['role'] === 'principal'): ?>
            <!-- Principal Dashboard -->
            <div class="sped-stats-grid">
                <div class="sped-stat-card">
                    <div class="sped-stat-number"><?php echo count($data['pending_approvals'] ?? []); ?></div>
                    <div class="sped-stat-label">Pending Approvals</div>
                </div>
                <div class="sped-stat-card">
                    <div class="sped-stat-number"><?php echo count($data['recent_approvals'] ?? []); ?></div>
                    <div class="sped-stat-label">Recent Approvals</div>
                </div>
            </div>

            <div class="sped-section">
                <h2 class="sped-section-title">IEPs Pending Approval</h2>
                <?php if (!empty($data['pending_approvals'])): ?>
                    <ul class="sped-list">
                        <?php foreach ($data['pending_approvals'] as $iep): ?>
                            <li class="sped-list-item">
                                <div class="sped-list-item-content">
                                    <div class="sped-list-item-title">
                                        IEP for Learner ID: <?php echo $iep->learner_id; ?>
                                    </div>
                                    <div class="sped-list-item-meta">
                                        Created: <?php echo date('M j, Y', strtotime($iep->created_at)); ?> • 
                                        Period: <?php echo date('M j, Y', strtotime($iep->start_date)); ?> - <?php echo date('M j, Y', strtotime($iep->end_date)); ?>
                                    </div>
                                </div>
                                <a href="<?php echo URLROOT; ?>/iep/approve?id=<?php echo $iep->id; ?>" class="sped-btn">Review</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="sped-empty-state">
                        <div class="sped-empty-state-icon">✓</div>
                        <p>No IEPs pending approval</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($data['role'] === 'admin' && !empty($data['recent_activity'])): ?>
            <div class="sped-section">
                <h2 class="sped-section-title">Recent System Activity</h2>
                <ul class="sped-list">
                    <?php foreach (array_slice($data['recent_activity'], 0, 10) as $activity): ?>
                        <li class="sped-list-item">
                            <div class="sped-list-item-content">
                                <div class="sped-list-item-title">
                                    <?php echo htmlspecialchars($activity->action_type); ?>
                                </div>
                                <div class="sped-list-item-meta">
                                    User ID: <?php echo $activity->user_id; ?> • 
                                    <?php echo date('M j, Y g:i A', strtotime($activity->created_at)); ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </section>
</main>

</body>
</html>