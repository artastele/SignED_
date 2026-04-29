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
                    <i class="bi bi-file-earmark-medical me-2"></i>
                    IEP Management
                </h1>
                <p class="mb-0">Manage Individualized Education Programs</p>
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

    <!-- IEPs Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-list-ul me-2 text-brand-red"></i>
                All IEPs
            </h5>
        </div>
        <div class="card-body">
            <?php if (!empty($data['ieps'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Learner</th>
                                <th>LRN</th>
                                <th>Grade Level</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['ieps'] as $iep): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?php 
                                            echo htmlspecialchars($iep->first_name . ' ' . $iep->last_name);
                                            ?>
                                        </strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($iep->lrn ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($iep->grade_level ?? 'N/A'); ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'pending_meeting' => 'warning',
                                            'pending_approval' => 'info',
                                            'approved' => 'success',
                                            'active' => 'primary',
                                            'rejected' => 'danger'
                                        ];
                                        $statusColor = $statusColors[$iep->status] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $statusColor; ?>">
                                            <?php echo ucwords(str_replace('_', ' ', $iep->status)); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($iep->created_by_name ?? 'N/A'); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($iep->created_at)); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <?php if ($iep->status === 'draft'): ?>
                                                <a href="<?php echo URLROOT; ?>/iep/create?learner_id=<?php echo $iep->learner_id; ?>" 
                                                   class="btn btn-outline-primary" title="Edit Draft">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo URLROOT; ?>/iep/view?id=<?php echo $iep->id; ?>" 
                                                   class="btn btn-outline-primary" title="View IEP">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($iep->status === 'pending_meeting' && !$iep->meeting_scheduled): ?>
                                                <a href="<?php echo URLROOT; ?>/iep/scheduleMeeting?iep_id=<?php echo $iep->id; ?>" 
                                                   class="btn btn-outline-success" title="Schedule Meeting">
                                                    <i class="bi bi-calendar-plus"></i>
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
                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                    <p class="mb-3">No IEPs found</p>
                    <p class="small">IEPs will appear here after assessments are completed</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
