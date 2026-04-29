<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">
                <i class="bi bi-megaphone me-2 text-brand-red"></i>
                System Announcements
            </h1>
            <p class="text-muted mb-0">Manage system-wide announcements and notifications</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                <i class="bi bi-plus-circle me-1"></i>Create Announcement
            </button>
            <a href="<?php echo URLROOT; ?>/admin/dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Announcements List -->
    <div class="row g-4">
        <div class="col-md-12">
            <?php if (!empty($data['announcements'])): ?>
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-3">Title</th>
                                        <th>Priority</th>
                                        <th>Target</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th class="text-end pe-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['announcements'] as $announcement): ?>
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-semibold"><?php echo htmlspecialchars($announcement->title); ?></div>
                                                <small class="text-muted"><?php echo htmlspecialchars(substr($announcement->content, 0, 80)) . (strlen($announcement->content) > 80 ? '...' : ''); ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $priorityColors = [
                                                    'urgent' => 'danger',
                                                    'high' => 'warning',
                                                    'normal' => 'info',
                                                    'low' => 'secondary'
                                                ];
                                                $color = $priorityColors[$announcement->priority] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?php echo $color; ?>">
                                                    <?php echo ucfirst($announcement->priority); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    <?php echo ucfirst(str_replace('_', ' ', $announcement->target_audience)); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($announcement->is_active): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('M j, Y', strtotime($announcement->created_at)); ?>
                                                </small>
                                            </td>
                                            <td class="text-end pe-3">
                                                <a href="<?php echo URLROOT; ?>/admin/toggleAnnouncement/<?php echo $announcement->id; ?>" 
                                                   class="btn btn-sm btn-outline-<?php echo $announcement->is_active ? 'warning' : 'success'; ?>"
                                                   onclick="return confirm('Toggle announcement status?')">
                                                    <i class="bi bi-<?php echo $announcement->is_active ? 'pause' : 'play'; ?>"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="editAnnouncement(<?php echo htmlspecialchars(json_encode($announcement)); ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="<?php echo URLROOT; ?>/admin/deleteAnnouncement/<?php echo $announcement->id; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Delete this announcement?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-megaphone fs-1 text-muted d-block mb-3"></i>
                        <h5 class="text-muted">No Announcements Yet</h5>
                        <p class="text-muted mb-4">Create your first system announcement to notify all users</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                            <i class="bi bi-plus-circle me-1"></i>Create Announcement
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
</main>

<!-- Create Announcement Modal -->
<div class="modal fade" id="createAnnouncementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo URLROOT; ?>/admin/createAnnouncement" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-megaphone me-2"></i>Create Announcement
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Enter announcement title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="5" placeholder="Enter announcement message" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="low">Low</option>
                                <option value="normal" selected>Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Target Audience</label>
                            <select name="target_audience" class="form-select">
                                <option value="all" selected>All Users</option>
                                <option value="parents">Parents Only</option>
                                <option value="teachers">Teachers Only</option>
                                <option value="sped_staff">SPED Staff Only</option>
                                <option value="learners">Learners Only</option>
                                <option value="admins">Admins Only</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expires At (Optional)</label>
                        <input type="datetime-local" name="expires_at" class="form-control">
                        <small class="text-muted">Leave empty for no expiration</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i>Publish Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Announcement Modal -->
<div class="modal fade" id="editAnnouncementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo URLROOT; ?>/admin/updateAnnouncement" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil me-2"></i>Edit Announcement
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="content" id="edit_content" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" id="edit_priority" class="form-select">
                                <option value="low">Low</option>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Target Audience</label>
                            <select name="target_audience" id="edit_target" class="form-select">
                                <option value="all">All Users</option>
                                <option value="parents">Parents Only</option>
                                <option value="teachers">Teachers Only</option>
                                <option value="sped_staff">SPED Staff Only</option>
                                <option value="learners">Learners Only</option>
                                <option value="admins">Admins Only</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expires At (Optional)</label>
                        <input type="datetime-local" name="expires_at" id="edit_expires" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Update Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editAnnouncement(announcement) {
    document.getElementById('edit_id').value = announcement.id;
    document.getElementById('edit_title').value = announcement.title;
    document.getElementById('edit_content').value = announcement.content;
    document.getElementById('edit_priority').value = announcement.priority;
    document.getElementById('edit_target').value = announcement.target_audience;
    
    if (announcement.expires_at) {
        const date = new Date(announcement.expires_at);
        const formatted = date.toISOString().slice(0, 16);
        document.getElementById('edit_expires').value = formatted;
    } else {
        document.getElementById('edit_expires').value = '';
    }
    
    new bootstrap.Modal(document.getElementById('editAnnouncementModal')).show();
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
