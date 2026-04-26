<?php require_once '../app/views/layouts/header.php'; ?>

<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="bi bi-person text-primary me-2"></i>
            My Profile
        </h1>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            Profile updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4 mb-4">
            <!-- Profile Card -->
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="avatar-circle-large mx-auto mb-3">
                        <?php echo strtoupper(substr($data['user']->fullname ?? 'U', 0, 1)); ?>
                    </div>
                    <h4><?php echo htmlspecialchars($data['user']->fullname ?? 'User'); ?></h4>
                    <p class="text-muted"><?php echo ucfirst(str_replace('_', ' ', $data['user']->role ?? '')); ?></p>
                    <hr>
                    <div class="text-start">
                        <p class="mb-2">
                            <i class="bi bi-envelope me-2 text-muted"></i>
                            <small><?php echo htmlspecialchars($data['user']->email ?? ''); ?></small>
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-calendar me-2 text-muted"></i>
                            <small>Joined <?php echo date('F Y', strtotime($data['user']->created_at ?? 'now')); ?></small>
                        </p>
                        <p class="mb-0">
                            <i class="bi bi-shield-check me-2 text-muted"></i>
                            <small><?php echo $data['user']->is_verified ? 'Verified' : 'Not Verified'; ?></small>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Edit Profile Form -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo URLROOT; ?>/user/updateProfile">
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Full Name</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="fullname" 
                                   name="fullname" 
                                   value="<?php echo htmlspecialchars($data['user']->fullname ?? ''); ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo htmlspecialchars($data['user']->email ?? ''); ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="role" 
                                   value="<?php echo ucfirst(str_replace('_', ' ', $data['user']->role ?? '')); ?>" 
                                   disabled>
                            <small class="text-muted">Contact administrator to change your role</small>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">Change Password</h6>
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="current_password" 
                                   name="current_password">
                            <small class="text-muted">Leave blank if you don't want to change password</small>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="new_password" 
                                   name="new_password">
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="confirm_password" 
                                   name="confirm_password">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Save Changes
                            </button>
                            <a href="<?php echo URLROOT; ?>/<?php echo $data['role']; ?>/dashboard" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</main>

<style>
.avatar-circle-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #a01422 0%, #1e4072 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: bold;
}
</style>

<?php require_once '../app/views/layouts/footer.php'; ?>
