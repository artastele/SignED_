<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1>
            <i class="bi bi-speedometer2 me-2"></i>
            Guidance Counselor Dashboard
        </h1>
        <p class="mb-0">Welcome back, <?php echo htmlspecialchars($data['user_name']); ?>. Here's your SPED workflow overview.</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Scheduled Meetings</div>
                            <div class="stat-number"><?php echo count($data['scheduled_meetings'] ?? []); ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Confirmed Meetings</div>
                            <div class="stat-number"><?php echo count($data['confirmed_meetings'] ?? []); ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Announcements Section -->
    <?php if (!empty($data['announcements'])): ?>
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-megaphone me-2 text-brand-red"></i>
                            Announcements
                        </h5>
                        <?php if (count($data['announcements']) > 3): ?>
                            <a href="<?php echo URLROOT; ?>/announcements" class="btn btn-sm btn-outline-primary">
                                View All (<?php echo count($data['announcements']); ?>)
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php foreach (array_slice($data['announcements'], 0, 3) as $announcement): ?>
                                <div class="list-group-item px-0" style="border-left: 4px solid <?php 
                                    echo $announcement->priority === 'urgent' ? '#dc3545' : 
                                        ($announcement->priority === 'high' ? '#fd7e14' : 
                                        ($announcement->priority === 'normal' ? '#0d6efd' : '#6c757d')); 
                                ?>;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span style="font-size: 1.2rem;">
                                                    <?php 
                                                    $icons = [
                                                        'urgent' => '🚨',
                                                        'high' => '⚠️',
                                                        'normal' => 'ℹ️',
                                                        'low' => '📢'
                                                    ];
                                                    echo $icons[$announcement->priority] ?? 'ℹ️';
                                                    ?>
                                                </span>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($announcement->title); ?></h6>
                                                <?php if ($announcement->priority === 'urgent' || $announcement->priority === 'high'): ?>
                                                    <span class="badge bg-<?php echo $announcement->priority === 'urgent' ? 'danger' : 'warning'; ?>">
                                                        <?php echo strtoupper($announcement->priority); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="mb-2 text-muted">
                                                <?php echo nl2br(htmlspecialchars(substr($announcement->content, 0, 150))); ?>
                                                <?php if (strlen($announcement->content) > 150): ?>...<?php endif; ?>
                                            </p>
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                Posted: <?php echo date('M j, Y g:i A', strtotime($announcement->created_at)); ?>
                                                <?php if ($announcement->created_by_name): ?>
                                                    by <?php echo htmlspecialchars($announcement->created_by_name); ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Upcoming IEP Meetings -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-event me-2 text-brand-red"></i>
                        Upcoming IEP Meetings
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['upcoming_meetings'])): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Learner</th>
                                        <th>Date & Time</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['upcoming_meetings'] as $meeting): ?>
                                        <tr>
                                            <td>Learner ID: <?php echo $meeting->learner_id; ?></td>
                                            <td><?php echo date('M j, Y g:i A', strtotime($meeting->meeting_date)); ?></td>
                                            <td><?php echo htmlspecialchars($meeting->location); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $meeting->status === 'confirmed' ? 'success' : 'warning'; ?>">
                                                    <?php echo ucfirst($meeting->status); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                            <p>No upcoming meetings</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
