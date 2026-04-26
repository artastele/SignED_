<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">
                <i class="bi bi-people me-2 text-brand-red"></i>
                User Management
            </h1>
            <p class="text-muted mb-0">Manage all system users, roles, and permissions</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/admin/dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Alerts -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php
            $msg = $_GET['success'];
            if ($msg === 'role_updated') echo 'User role updated successfully.';
            elseif ($msg === 'user_deleted') echo 'User deleted successfully.';
            else echo htmlspecialchars($msg);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Total Users</div>
                            <div class="stat-number"><?php echo count($data['users'] ?? []); ?></div>
                        </div>
                        <div class="stat-icon text-primary">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Verified</div>
                            <div class="stat-number">
                                <?php echo count(array_filter($data['users'] ?? [], fn($u) => $u->is_verified)); ?>
                            </div>
                        </div>
                        <div class="stat-icon text-success">
                            <i class="bi bi-patch-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Unverified</div>
                            <div class="stat-number">
                                <?php echo count(array_filter($data['users'] ?? [], fn($u) => !$u->is_verified)); ?>
                            </div>
                        </div>
                        <div class="stat-icon text-warning">
                            <i class="bi bi-person-x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Admins</div>
                            <div class="stat-number">
                                <?php echo count(array_filter($data['users'] ?? [], fn($u) => $u->role === 'admin')); ?>
                            </div>
                        </div>
                        <div class="stat-icon text-info">
                            <i class="bi bi-shield-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="userSearch" class="form-control border-start-0"
                               placeholder="Search by name or email...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="roleFilter" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="sped_teacher">SPED Teacher</option>
                        <option value="teacher">Teacher</option>
                        <option value="guidance">Guidance</option>
                        <option value="principal">Principal</option>
                        <option value="parent">Parent</option>
                        <option value="learner">Learner</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="verifiedFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="1">Verified</option>
                        <option value="0">Unverified</option>
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <span class="text-muted small" id="resultCount">
                        <?php echo count($data['users'] ?? []); ?> users
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <?php if (!empty($data['users'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="usersTable">
                        <thead>
                            <tr>
                                <th class="ps-3">User</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['users'] as $user): ?>
                                <tr data-role="<?php echo $user->role; ?>"
                                    data-verified="<?php echo $user->is_verified; ?>"
                                    data-name="<?php echo strtolower($user->fullname); ?>"
                                    data-email="<?php echo strtolower($user->email); ?>">

                                    <!-- User Info -->
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-3" style="width:40px;height:40px;font-size:1rem;flex-shrink:0;">
                                                <?php echo strtoupper(substr($user->fullname, 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($user->fullname); ?></div>
                                                <small class="text-muted"><?php echo htmlspecialchars($user->email); ?></small>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Role (editable) -->
                                    <td>
                                        <form action="<?php echo URLROOT; ?>/admin/updateRole" method="POST"
                                              class="d-flex align-items-center gap-2 role-form">
                                            <input type="hidden" name="id" value="<?php echo $user->id; ?>">
                                            <select name="role" class="form-select form-select-sm" style="width:auto;">
                                                <?php
                                                $roles = [
                                                    'admin'        => 'Admin',
                                                    'sped_teacher' => 'SPED Teacher',
                                                    'teacher'      => 'Teacher',
                                                    'guidance'     => 'Guidance',
                                                    'principal'    => 'Principal',
                                                    'parent'       => 'Parent',
                                                    'learner'      => 'Learner',
                                                ];
                                                foreach ($roles as $val => $label):
                                                ?>
                                                    <option value="<?php echo $val; ?>"
                                                        <?php echo ($user->role == $val) ? 'selected' : ''; ?>>
                                                        <?php echo $label; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Verified Status -->
                                    <td>
                                        <?php if ($user->is_verified): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Verified
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock me-1"></i>Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Date -->
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y', strtotime($user->created_at)); ?>
                                        </small>
                                    </td>

                                    <!-- Actions -->
                                    <td class="text-end pe-3">
                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete(<?php echo $user->id; ?>, '<?php echo htmlspecialchars($user->fullname, ENT_QUOTES); ?>')">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-people fs-1 text-muted d-block mb-3"></i>
                    <h5 class="text-muted">No Users Found</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>

</main>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Are you sure you want to delete:</p>
                <p class="fw-bold" id="deleteUserName"></p>
                <p class="text-muted small mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <a id="deleteConfirmBtn" href="#" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash me-1"></i>Delete
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Delete confirmation modal
function confirmDelete(userId, userName) {
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteConfirmBtn').href = '<?php echo URLROOT; ?>/admin/deleteUser/' + userId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Live search + filter
const searchInput  = document.getElementById('userSearch');
const roleFilter   = document.getElementById('roleFilter');
const verifiedFilter = document.getElementById('verifiedFilter');
const rows         = document.querySelectorAll('#usersTable tbody tr');
const resultCount  = document.getElementById('resultCount');

function filterTable() {
    const search   = searchInput.value.toLowerCase();
    const role     = roleFilter.value;
    const verified = verifiedFilter.value;
    let visible    = 0;

    rows.forEach(row => {
        const matchSearch   = row.dataset.name.includes(search) || row.dataset.email.includes(search);
        const matchRole     = !role     || row.dataset.role     === role;
        const matchVerified = verified === '' || row.dataset.verified === verified;

        if (matchSearch && matchRole && matchVerified) {
            row.style.display = '';
            visible++;
        } else {
            row.style.display = 'none';
        }
    });

    resultCount.textContent = visible + ' user' + (visible !== 1 ? 's' : '');
}

searchInput.addEventListener('input', filterTable);
roleFilter.addEventListener('change', filterTable);
verifiedFilter.addEventListener('change', filterTable);
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
