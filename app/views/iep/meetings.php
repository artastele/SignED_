<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/partials/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>
                    <i class="bi bi-calendar-event me-2"></i>
                    IEP Meetings
                </h1>
                <p class="mb-0">View and manage scheduled IEP meetings</p>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Meetings Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-list-ul me-2 text-brand-red"></i>
                Scheduled Meetings
            </h5>
        </div>
        <div class="card-body">
            <?php if (!empty($data['meetings'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Learner</th>
                                <th>LRN</th>
                                <th>Grade Level</th>
                                <th>Meeting Date</th>
                                <th>Meeting Time</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Scheduled By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['meetings'] as $meeting): ?>
                                <?php
                                // Determine if meeting is upcoming or past
                                $meetingDateTime = strtotime($meeting->meeting_date . ' ' . $meeting->meeting_time);
                                $isUpcoming = $meetingDateTime >= time();
                                $isPast = $meetingDateTime < time();
                                ?>
                                <tr class="<?php echo $isPast ? 'table-secondary' : ''; ?>">
                                    <td>
                                        <strong>
                                            <?php 
                                            echo htmlspecialchars($meeting->first_name . ' ' . $meeting->last_name);
                                            ?>
                                        </strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($meeting->lrn ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($meeting->grade_level ?? 'N/A'); ?></td>
                                    <td>
                                        <?php echo date('M j, Y', strtotime($meeting->meeting_date)); ?>
                                        <?php if ($isUpcoming): ?>
                                            <span class="badge bg-info ms-1">Upcoming</span>
                                        <?php elseif ($isPast): ?>
                                            <span class="badge bg-secondary ms-1">Past</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('g:i A', strtotime($meeting->meeting_time)); ?></td>
                                    <td><?php echo htmlspecialchars($meeting->location); ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'scheduled' => 'warning',
                                            'confirmed' => 'success',
                                            'completed' => 'primary',
                                            'cancelled' => 'danger'
                                        ];
                                        $statusColor = $statusColors[$meeting->status] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $statusColor; ?>">
                                            <?php echo ucfirst($meeting->status); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($meeting->scheduled_by_name ?? 'N/A'); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?php echo URLROOT; ?>/iep/view?id=<?php echo $meeting->iep_id; ?>" 
                                               class="btn btn-outline-primary" title="View IEP">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            <?php if ($meeting->status === 'scheduled' && $isUpcoming): ?>
                                                <a href="<?php echo URLROOT; ?>/iep/confirmAttendance?meeting_id=<?php echo $meeting->id; ?>" 
                                                   class="btn btn-outline-success" title="Confirm Attendance">
                                                    <i class="bi bi-check-circle"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($meeting->status === 'confirmed' && $isPast): ?>
                                                <a href="<?php echo URLROOT; ?>/iep/recordMeeting?meeting_id=<?php echo $meeting->id; ?>" 
                                                   class="btn btn-outline-info" title="Record Minutes">
                                                    <i class="bi bi-file-text"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                    <p class="mb-3">No meetings scheduled</p>
                    <p class="small">Meetings will appear here after IEPs are sent</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Meeting Statistics -->
    <?php if (!empty($data['meetings'])): ?>
    <div class="row g-4 mt-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">
                        <?php 
                        $scheduled = array_filter($data['meetings'], fn($m) => $m->status === 'scheduled');
                        echo count($scheduled);
                        ?>
                    </h3>
                    <p class="mb-0 text-muted">Scheduled</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">
                        <?php 
                        $confirmed = array_filter($data['meetings'], fn($m) => $m->status === 'confirmed');
                        echo count($confirmed);
                        ?>
                    </h3>
                    <p class="mb-0 text-muted">Confirmed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">
                        <?php 
                        $completed = array_filter($data['meetings'], fn($m) => $m->status === 'completed');
                        echo count($completed);
                        ?>
                    </h3>
                    <p class="mb-0 text-muted">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-danger">
                        <?php 
                        $cancelled = array_filter($data['meetings'], fn($m) => $m->status === 'cancelled');
                        echo count($cancelled);
                        ?>
                    </h3>
                    <p class="mb-0 text-muted">Cancelled</p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
