<?php require_once '../app/views/layouts/header.php'; ?>

<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="bi bi-gear text-primary me-2"></i>
            Settings
        </h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Notification Settings -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Notification Preferences</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo URLROOT; ?>/user/updateSettings">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" name="email_notifications" checked>
                            <label class="form-check-label" for="emailNotifications">
                                <strong>Email Notifications</strong><br>
                                <small class="text-muted">Receive email notifications for important updates</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="enrollmentUpdates" name="enrollment_updates" checked>
                            <label class="form-check-label" for="enrollmentUpdates">
                                <strong>Enrollment Updates</strong><br>
                                <small class="text-muted">Get notified about enrollment status changes</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="iepUpdates" name="iep_updates" checked>
                            <label class="form-check-label" for="iepUpdates">
                                <strong>IEP Updates</strong><br>
                                <small class="text-muted">Receive notifications about IEP meetings and approvals</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="assessmentUpdates" name="assessment_updates" checked>
                            <label class="form-check-label" for="assessmentUpdates">
                                <strong>Assessment Updates</strong><br>
                                <small class="text-muted">Get notified about assessment schedules and results</small>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Save Preferences
                        </button>
                    </form>
                </div>
            </div>

            <!-- Privacy Settings -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Privacy & Security</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Two-Factor Authentication</h6>
                        <p class="text-muted">Add an extra layer of security to your account</p>
                        <button class="btn btn-outline-primary btn-sm" disabled>
                            <i class="bi bi-shield-lock me-2"></i>Enable 2FA (Coming Soon)
                        </button>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <h6>Session Management</h6>
                        <p class="text-muted">Manage your active sessions and devices</p>
                        <button class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-x-circle me-2"></i>Sign Out All Devices
                        </button>
                    </div>
                </div>
            </div>

            <!-- Account Actions -->
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-white border-danger">
                    <h5 class="mb-0 text-danger">Danger Zone</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Delete Account</h6>
                        <p class="text-muted">Permanently delete your account and all associated data</p>
                        <button class="btn btn-danger btn-sm" disabled>
                            <i class="bi bi-trash me-2"></i>Delete Account (Contact Admin)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Info -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">About Settings</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        Customize your SignED SPED experience by adjusting your notification preferences and security settings.
                    </p>
                    <hr>
                    <p class="small mb-2">
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        <strong>Need Help?</strong>
                    </p>
                    <p class="small text-muted">
                        Contact your system administrator for assistance with account settings.
                    </p>
                </div>
            </div>
        </div>
    </div>

</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
