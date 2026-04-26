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
    
    <!-- Settings Content -->
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
                    <form>
                        <div class="mb-3">
                            <label class="form-label">System Name</label>
                            <input type="text" class="form-control" value="SignED SPED System" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">System Email</label>
                            <input type="email" class="form-control" value="admin@signed.edu" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Timezone</label>
                            <select class="form-select" disabled>
                                <option>Asia/Manila</option>
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Settings management coming soon
                        </div>
                    </form>
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
                        <input type="number" class="form-control" value="30" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Max Login Attempts</label>
                        <input type="number" class="form-control" value="5" readonly>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked disabled>
                            <label class="form-check-label">
                                Require Email Verification
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked disabled>
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
                        <input type="text" class="form-control" value="smtp.gmail.com" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SMTP Port</label>
                        <input type="text" class="form-control" value="587" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">From Email</label>
                        <input type="email" class="form-control" value="noreply@signed.edu" readonly>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Email configuration is managed in config files
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
    </div>
    
</main>

<?php require_once '../app/views/layouts/footer.php'; ?>
