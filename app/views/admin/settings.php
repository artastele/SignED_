<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">
                <i class="bi bi-gear me-2 text-brand-red"></i>
                System Settings
            </h1>
            <p class="text-muted mb-0">Configure system settings and preferences</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
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
    
    <!-- Settings Form -->
    <form action="<?php echo URLROOT; ?>/admin/updateSettings" method="POST">
        <div class="row g-4">
            <!-- General Settings -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-sliders me-2 text-brand-red"></i>
                            General Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">System Name</label>
                            <input type="text" name="system_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($data['settings']['system_name']['value'] ?? 'SignED SPED System'); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">System Email</label>
                            <input type="email" name="system_email" class="form-control" 
                                   value="<?php echo htmlspecialchars($data['settings']['system_email']['value'] ?? 'admin@signed.edu'); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Timezone</label>
                            <select name="timezone" class="form-select">
                                <option value="Asia/Manila" <?php echo ($data['settings']['timezone']['value'] ?? 'Asia/Manila') == 'Asia/Manila' ? 'selected' : ''; ?>>Asia/Manila</option>
                                <option value="Asia/Singapore" <?php echo ($data['settings']['timezone']['value'] ?? '') == 'Asia/Singapore' ? 'selected' : ''; ?>>Asia/Singapore</option>
                                <option value="UTC" <?php echo ($data['settings']['timezone']['value'] ?? '') == 'UTC' ? 'selected' : ''; ?>>UTC</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Security Settings -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-shield-check me-2 text-brand-red"></i>
                            Security Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Session Timeout (minutes)</label>
                            <input type="number" name="session_timeout" class="form-control" min="5" max="120"
                                   value="<?php echo htmlspecialchars($data['settings']['session_timeout']['value'] ?? 30); ?>">
                            <small class="text-muted">Users will be logged out after this period of inactivity</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Max Login Attempts</label>
                            <input type="number" name="max_login_attempts" class="form-control" min="3" max="10"
                                   value="<?php echo htmlspecialchars($data['settings']['max_login_attempts']['value'] ?? 5); ?>">
                            <small class="text-muted">Account will be locked after this many failed attempts</small>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="require_email_verification" 
                                       <?php echo ($data['settings']['require_email_verification']['value'] ?? true) ? 'checked' : ''; ?>>
                                <label class="form-check-label">
                                    Require Email Verification
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="enable_audit_logging"
                                       <?php echo ($data['settings']['enable_audit_logging']['value'] ?? true) ? 'checked' : ''; ?>>
                                <label class="form-check-label">
                                    Enable Audit Logging
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Email Settings -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-envelope me-2 text-brand-red"></i>
                            Email Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">SMTP Host</label>
                            <input type="text" name="smtp_host" class="form-control" 
                                   value="<?php echo htmlspecialchars($data['settings']['smtp_host']['value'] ?? 'smtp.gmail.com'); ?>">
                            <small class="text-muted">SMTP server address (e.g., smtp.gmail.com)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SMTP Port</label>
                            <input type="number" name="smtp_port" class="form-control" 
                                   value="<?php echo htmlspecialchars($data['settings']['smtp_port']['value'] ?? 587); ?>">
                            <small class="text-muted">Usually 587 for TLS or 465 for SSL</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">From Email</label>
                            <input type="email" name="smtp_from_email" class="form-control" 
                                   value="<?php echo htmlspecialchars($data['settings']['smtp_from_email']['value'] ?? 'noreply@signed.edu'); ?>">
                            <small class="text-muted">Email address shown as sender</small>
                        </div>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            <small><strong>Note:</strong> SMTP username and password are stored in <code>config/config.php</code> for security. Only Host, Port, and From Email can be changed here.</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- System Information -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2 text-brand-red"></i>
                            System Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Version:</strong> 1.0.0
                        </div>
                        <div class="mb-3">
                            <strong>PHP Version:</strong> <?php echo phpversion(); ?>
                        </div>
                        <div class="mb-3">
                            <strong>Database:</strong> MySQL
                        </div>
                        <div class="mb-3">
                            <strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>
                        </div>
                        <div class="mb-3">
                            <strong>Environment:</strong> Development
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save me-2"></i>Save Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
