<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1>
            <i class="bi bi-speedometer2 me-2"></i>
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
        <p class="mb-0">Welcome back, <?php echo htmlspecialchars($data['user_name']); ?>. Here's your SPED workflow overview.</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        
        <?php if ($data['role'] === 'sped_teacher'): ?>
            <div class="col-md-4">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">Pending Verifications</div>
                                <div class="stat-number"><?php echo count($data['pending_verifications'] ?? []); ?></div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">Pending Assessments</div>
                                <div class="stat-number"><?php echo count($data['pending_assessments'] ?? []); ?></div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">Active IEPs</div>
                                <div class="stat-number"><?php echo count($data['active_ieps'] ?? []); ?></div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-file-earmark-medical"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($data['role'] === 'admin'): ?>
            <div class="col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">Total Enrollments</div>
                                <div class="stat-number"><?php echo $data['total_enrollments'] ?? 0; ?></div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">Pending Verifications</div>
                                <div class="stat-number"><?php echo $data['pending_verifications'] ?? 0; ?></div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">Active Learners</div>
                                <div class="stat-number"><?php echo $data['active_learners'] ?? 0; ?></div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">Active IEPs</div>
                                <div class="stat-number"><?php echo $data['active_ieps'] ?? 0; ?></div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-file-earmark-medical"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($data['role'] === 'guidance'): ?>
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
        <?php endif; ?>
        
        <?php if ($data['role'] === 'principal'): ?>
            <div class="col-md-6">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">Pending Approvals</div>
                                <div class="stat-number"><?php echo count($data['pending_approvals'] ?? []); ?></div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-hourglass-split"></i>
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
                                <div class="stat-label">Recent Approvals</div>
                                <div class="stat-number"><?php echo count($data['recent_approvals'] ?? []); ?></div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
    </div>
    
    <div class="row g-4">
        
        <!-- SPED Teacher Content -->
        <?php if ($data['role'] === 'sped_teacher'): ?>
            
            <!-- Pending Verifications -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-clipboard-check me-2 text-brand-red"></i>
                            Pending Verifications
                        </h5>
                        <?php if (!empty($data['pending_verifications']) && count($data['pending_verifications']) > 5): ?>
                            <a href="<?php echo URLROOT; ?>/enrollment/verify" class="btn btn-sm btn-outline-primary">
                                View All (<?php echo count($data['pending_verifications']); ?>)
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($data['pending_verifications'])): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach (array_slice($data['pending_verifications'], 0, 5) as $enrollment): ?>
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($enrollment->learner_first_name . ' ' . $enrollment->learner_last_name); ?></h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    Submitted: <?php echo date('M j, Y', strtotime($enrollment->created_at)); ?>
                                                </small>
                                            </div>
                                            <a href="<?php echo URLROOT; ?>/enrollment/verify?id=<?php echo $enrollment->id; ?>" class="btn btn-sm btn-primary">
                                                Review
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-check-circle fs-1 d-block mb-2"></i>
                                <p>No pending verifications</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Recent Submissions -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-file-earmark-arrow-up me-2 text-brand-red"></i>
                            Recent Submissions
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($data['recent_submissions'])): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($data['recent_submissions'] as $submission): ?>
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($submission->first_name . ' ' . $submission->last_name); ?></h6>
                                                <small class="text-muted d-block">
                                                    Material: <?php echo htmlspecialchars($submission->material_title); ?>
                                                </small>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    <?php echo date('M j, Y g:i A', strtotime($submission->submitted_at)); ?>
                                                </small>
                                            </div>
                                            <a href="<?php echo URLROOT; ?>/learner/submissions?id=<?php echo $submission->id; ?>" class="btn btn-sm btn-outline-primary">
                                                Review
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                <p>No recent submissions</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
        <?php endif; ?>
        
        <!-- Guidance Content -->
        <?php if ($data['role'] === 'guidance'): ?>
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
        <?php endif; ?>
        
        <!-- Principal Content -->
        <?php if ($data['role'] === 'principal'): ?>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-file-earmark-check me-2 text-brand-red"></i>
                            IEPs Pending Approval
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($data['pending_approvals'])): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Learner ID</th>
                                            <th>Created</th>
                                            <th>Period</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['pending_approvals'] as $iep): ?>
                                            <tr>
                                                <td><?php echo $iep->learner_id; ?></td>
                                                <td><?php echo date('M j, Y', strtotime($iep->created_at)); ?></td>
                                                <td>
                                                    <?php echo date('M j, Y', strtotime($iep->start_date)); ?> - 
                                                    <?php echo date('M j, Y', strtotime($iep->end_date)); ?>
                                                </td>
                                                <td>
                                                    <a href="<?php echo URLROOT; ?>/iep/approve?id=<?php echo $iep->id; ?>" class="btn btn-sm btn-primary">
                                                        Review
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-check-circle fs-1 d-block mb-2"></i>
                                <p>No IEPs pending approval</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Admin Content -->
        <?php if ($data['role'] === 'admin' && !empty($data['recent_activity'])): ?>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-activity me-2 text-brand-red"></i>
                            Recent System Activity
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>User ID</th>
                                        <th>Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($data['recent_activity'], 0, 10) as $activity): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($activity->action_type); ?></td>
                                            <td><?php echo $activity->user_id; ?></td>
                                            <td><?php echo date('M j, Y g:i A', strtotime($activity->created_at)); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
    </div>
    
</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
